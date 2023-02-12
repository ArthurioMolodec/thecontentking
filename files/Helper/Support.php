<?php
function cors() {

    //unset($_SESSION['access_token']);
    
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }
    
   
}


function contentFilter($content, $token){

    //this model is for content filtering !

    $model = "content-filter-alpha";
    $content = "<|endoftext|>$content\n--\nLabel:";

    $fields = [
        'prompt' => $content,
        "temperature" => 0,
        "max_tokens" => 1,
        "top_p" => 1,
        "frequency_penalty" => 0,
        "presence_penalty" => 0,
    ];

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer $token"
    ];

    $url = "https://api.openai.com/v1/engines/$model/completions";

    $curl = curl_init();

    $curl_info = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_POSTFIELDS => json_encode($fields),
        CURLOPT_HTTPHEADER => $headers,
    ];

    curl_setopt_array($curl, $curl_info);
    $response = curl_exec($curl);

    //if error occur !
    if (curl_errno($curl)) {
        curl_close($curl);
        return false;
    }

    curl_close($curl);

    $response = json_decode($response, true);

    if (isset($response['error'])) {
        http_response_code(500);
        echo "Something went wrong, please contact support";
        exit;
        return false;
    }

    $outputLabel = $response['choices'][0]['text'];
   
    $toxicThreshold = -0.355;

    if ($outputLabel == "2"){


         # If the model returns "2", return its confidence in 2 or other output-labels
        $logprobs = $response["choices"][0]["logprobs"]["top_logprobs"][0];

        # If the model is not sufficiently confident in "2",
        # choose the most probable of "0" or "1"
        # Guaranteed to have a confidence for 2 since this was the selected token.
        if ($logprobs["2"] < $toxicThreshold){

            //if 0 key exist then return else return null
            $logprob_0 = $longprobs['0'] ?? null;                
            $logprob_1 =  $longprobs['1'] ?? null;

            # If both "0" and "1" have probabilities, set the output label
            # to whichever is most probable
            if ($logprob_0 !== null && $logprob_1 !== null){

                if ($logprob_0 >= $logprob_1){

                    $outputLabel = "0";

                }
                else{
                    $outputLabel = "1";
                }
                   

            }
            # If only one of them is found, set output label to that one
            elseif($logprob_0 !== null){
               $outputLabel = "0";
            }
                
            elseif($logprob_1 !== null){
                $outputLabel = "1";
            }

            # If neither "0" or "1" are available, stick with "2"
            # by leaving output_label unchanged.
              

        }
    
    }

    # if the most probable token is none of "0", "1", or "2"
    # this should be set as unsafe
    $optLabels = ["0", "1", "2"];
    if(!in_array($outputLabel, $optLabels)){
       $outputLabel = "2";
    }

    return $outputLabel;

}

function abort404(){
    http_response_code(404);
    exit("404");
}

function abort401(){
    http_response_code(401);
    exit("401");
}

function redirect($path){
    header("Location: " . webUrl . $path);
    exit;
}

function validateFields($requiredFields = [], $optionalFields = [], $inputData = null, $fieldsLables = null) {
    if ($inputData === null) {
        $inputData = array_merge($_SERVER['REQUEST_METHOD'] === 'GET' ? $_GET : $_POST);
    }

    $result = [];

    $errors = [];

    foreach($requiredFields as $fieldName) {
        if (!isset($inputData[$fieldName]) || !$inputData[$fieldName]) {
            $errors[] = (isset($fieldsLables[$fieldName]) ? $fieldsLables[$fieldName] : $fieldName) . ' is required!';
            continue;
        }
        $result[$fieldName] = $inputData[$fieldName];
    }

    foreach($optionalFields as $fieldName) {
        if (!isset($inputData[$fieldName]) || !$inputData[$fieldName]) {
            $result[$fieldName] = null;
            continue;
        }
        $result[$fieldName] = $inputData[$fieldName];
    }

    return array($result, $errors);
}

function validateCaptcha() {
    list($result, $errors) = validateFields(['g-recaptcha-response', 'h-captcha-response']);

    if (count($errors)) {
        return $errors;
    }

    $result = validateHCaptcha($result['h-captcha-response']);

    if (!$result) {
        return [ 'Captcha is invalid' ];
    }

    return true;
}


?>