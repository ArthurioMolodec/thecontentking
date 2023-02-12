<?php

use Google\Cloud\Firestore\FieldValue;
use Google\Cloud\Firestore\Query;
use Kreait\Firebase\Factory;
class DBHelper {
    private \Kreait\Firebase\Contract\Auth $auth;
    private \Google\Cloud\Firestore\FirestoreClient $database;

    function __construct($serviceAccPath = serviceAccoutPath)
    {
        $factory = (new Factory())->withServiceAccount($serviceAccPath);
        
        $firestore = $factory->createFirestore();

        $this->database = $firestore->database();
        $this->auth = $factory->createAuth();
    }

    function getUserCollection() {
        return $this->database->collection('user-profiles');
    }

    function getApiCallsCollection($moduleName) {
        return $this->database->collection('user-api-calls-' . $moduleName);
    }

    function getPaymentLinkCollection() {
        return $this->database->collection('payment-links');
    }

    function checkLogin($idToken) {    
        try {
            $tokenVerify = $this->auth->verifyIdToken($idToken, true);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    function registerAnonymously($ip) {
        $key = 'anon-' . bin2hex(random_bytes(20)) . sha1(microtime());

        $additional_data = [
            'key' => $key,
            'ip' => $ip,
            'type' => ACCOUNT_TYPE_ANON,
        ];
        $collection = $this->getUserCollection();
        $collection->document($key)->set($additional_data);
        $this->userApiCallsForAll($key);

        $_SESSION['userkey'] = $key;
    }

    function findAnon($ip, $userkey = null) {
        if (!$userkey) {
            $userkey = $_SESSION['userkey'];
        }
        $collection = $this->getUserCollection();

        $documents = $collection->where('ip', '=', $ip)->documents()->rows();

        foreach($documents as $document) {
            if (!$document->exists()) {
                continue;
            }

            return $document->data();
        }

        $documents = $collection->where('key', '=', $userkey)->documents()->rows();

        foreach($documents as $document) {
            if (!$document->exists()) {
                continue;
            }

            return $document->data();
        }

        return null;
    }

    function getUserId() {
        $user_id = $_SESSION['user_id'];

        if (!$user_id) {
            $userkey = $_SESSION['userkey'];

            $user_id = $userkey;
        }

        return $user_id;
    }

    function getUser($user_id = null, $userkey = null) {
        if (!$user_id) {
            $user_id= $this->getUserId();
        }

        $data = $this->getUserCollection()->document($user_id)->snapshot()->data();

        if ($data) {
            $data['uid'] = $user_id;
        }

        if ($data['type'] === ACCOUNT_TYPE_PREMIUM) {
            $_SESSION['premium_user'] = true;
        }

        return $data;
    }
    
    function loginWithEmailPass($email, $password) {    
        try {
            $tokenVerify = $this->auth->signInWithEmailAndPassword($email, $password);
            $_SESSION['user_id'] = $tokenVerify->firebaseUserId();
            $_SESSION['id_token'] = $this->auth->verifyIdToken($tokenVerify->idToken());
            return true;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    
    function signUpWithEmailPass($email, $password, $additional_data = []) {    
        $additional_data['email'] = $email;
        // $additional_data['password'] = $password;
    
        try {
            $user = $this->auth->createUserWithEmailAndPassword($email, $password);
            $collection = $this->getUserCollection();
            $collection->document($user->uid)->set($additional_data);
            $this->userApiCallsForAll($user->uid);
            return true;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    
    function logOut() {    
        if ($_SESSION['user_id']) {
            $this->auth->revokeRefreshTokens($_SESSION['user_id']);
        }
    
        $_SESSION['id_token'] = null;
        $_SESSION['user_id'] = null;
        $_SESSION['premium_user'] = null;
    }

    function userApiCallsForAll($user_id = null) {
        $user = $this->getUser($user_id);
        $user_type = isset($user['type']) ? $user['type'] : ACCOUNT_TYPE_DEFAULT;

        $this->depositUserApiCallsBalance(MODULE_NAME_AI_IMAGES, defaulModulesLimitByType[MODULE_NAME_AI_IMAGES][$user_type], $user_id);
    }
    
    function incrementApiCalls($module, $user_id = null) {
        if (!$user_id) {
            $user_id= $this->getUserId();
        }
        if (!$user_id) {
            throw new \Exception("User is not logged in!");
        }
        $collection = $this->getApiCallsCollection($module);
        $collection->document($user_id)->update([['path' => 'total_generated', 'value' => FieldValue::increment(1)], ['path' => 'total_available_balance', 'value' => FieldValue::increment(-1)], ['path' => 'updated', 'value' => microtime(true)]]);
    }

    function depositUserApiCallsBalance($module, $deposit = null, $user_id = null) {
        if (!$user_id) {
            $user_id= $this->getUserId();
        }
        if (!$user_id) {
            throw new \Exception("User is not logged in!");
        }
        $collection = $this->getApiCallsCollection($module);
        $docData = $collection->document($user_id)->snapshot()->exists();

        if ($deposit === null) {
            $user = $this->getUser($user_id);
            $user_type = isset($user['type']) ? $user['type'] : ACCOUNT_TYPE_DEFAULT;
            $deposit = defaulModulesLimitByType[$module][$user_type];
        }

        if (!$docData) {
            $collection->document($user_id)->set(['total_generated' => 0, 'total_available_balance' => $deposit, 'updated' => microtime(true)], [ 'merge' => true ]);
        } else {
            $collection->document($user_id)->set(['total_available_balance' => $deposit, 'updated' => microtime(true)], [ 'merge' => true ]);
        }
    }

    function getUserApiCalls($module, $initCount = false, $user_id = null) {
        if (!$user_id) {
            $user_id= $this->getUserId();
        }
        if (!$user_id) {
            throw new \Exception("User is not logged in!");
        }
        $collection = $this->getApiCallsCollection($module);
        $docData = $collection->document($user_id)->snapshot()->data();

        if (!$docData) {
            
            if ($initCount === false) {
                return 0;
            }

            $collection->document($user_id)->set(['total_generated' => 0, 'total_available_balance' => $initCount, 'updated' => microtime(true)], [ 'merge' => true ]);
            return $initCount;
        }

        if ( $docData['total_available_balance'] <= 0 && (!$docData['updated'] || time() - $docData['updated'] >= anonTariffDurationSecs)) {
            if ($initCount === false) {
                $initCount = null;
            }
            $this->depositUserApiCallsBalance($module, $initCount, $user_id);
            return $initCount;
        }

        return $docData['total_available_balance'];
    }

    function setUserType($user_id, $type) {
        $this->getUserCollection()->document($user_id)->set(['type' => $type], [ 'merge' => true ]);
        $this->userApiCallsForAll($user_id);
    }

    function insertPaymentLink($id, $payload) {
        $this->getPaymentLinkCollection()->document($id)->set($payload, [ 'merge' => true ]);
    }

    function processPaymentLinkSuccess($id, $payload) {
        $paymentLinkRef = $this->getPaymentLinkCollection()->document($id);
        $paymentLink = $paymentLinkRef->snapshot()->data();

        if (!$paymentLink || $paymentLink['status'] !== 'created') {
            return;
        }
        if ($paymentLink['type'] === PAYMENT_FOR_MODULE_PREMIUM_PREFIX) {
            $this->setUserType($paymentLink['subject_uid'], ACCOUNT_TYPE_PREMIUM);
            $paymentLinkRef->set(['status' => 'processed'], [ 'merge' => true ]);
        }
    }
}