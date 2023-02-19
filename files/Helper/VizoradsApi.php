123
<?php

class StripeApi
{
    private $login;
    private $password;
    private $db;

    function __construct(string $login, string $password, $dbInstance)
    {
        $this->login = $login;
        $this->password = $password;
        $this->db = $dbInstance;
    }

    private function apiRequest($endpoint, $data, $token) {
        $verify = curl_init();
        $headers = ['Content-Type' => 'application/json'];

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        curl_setopt($verify, CURLOPT_URL, "https://redirect-solution.openxcell.dev/api/$endpoint");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_HEADER, $headers);
        curl_setopt($verify, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        $responseData = json_decode($response);

        return $responseData;
    }

    function auth()
    {
        $authData = $this->apiRequest('v1/login', ['email' => $this->login, 'password' => $this->password], null);

        if ($authData && $authData->status === 0) {
            return $authData->data->token;
        }

        return false;
    }

    private function inserPaymentLink($id, $payload)
    {
        $this->db->insertPaymentLink(PAYMENT_LINK_STRIPE . '-' . $id, $payload);
    }

    public function createEmbed(float $amount, $productName, $type, $uid, $currency = 'usd')
    {

        $price = \Stripe\Price::create(['currency' => $currency, 'unit_amount' => floor($amount * 100), 'product_data' => ['name' => $productName]]);
        $paymentLink = \Stripe\PaymentLink::create([
            'line_items' => [['price' => $price->id, 'quantity' => 1]],
            'metadata' => [
                'type' => $type,
                'uid' => $uid,
            ]
        ]);

        $this->inserPaymentLink($paymentLink->id, [
            'uid' => $paymentLink->id,
            'type' => $type,
            'subject_uid' => $uid,
            'status' => 'created',
        ]);

        return $paymentLink->url;
    }
}
