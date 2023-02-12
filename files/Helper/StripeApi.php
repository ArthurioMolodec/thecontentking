<?php

use Stripe\Stripe;

class StripeApi
{
    private $secret;
    private $whsec;
    private $urlToWebHook;
    private $db;

    function __construct(string $secret, $dbInstance, string $whsec = null, string $urlToWebHook = null)
    {
        $this->secret = $secret;
        $this->whsec = $whsec;
        $this->urlToWebHook = $urlToWebHook;
        $this->db = $dbInstance;

        Stripe::setApiKey($this->secret);
    }

    function setWebhook()
    {
        $endpoint = \Stripe\WebhookEndpoint::create([
            'url' => $this->urlToWebHook,
            'enabled_events' => [
                'checkout.session.completed',
            ],
        ]);

        return $endpoint;
    }

    private function handleHookEvent($event)
    {
        if (!$event) {
            return;
        }
        if ($event->type === 'checkout.session.completed') {
            if (!$event->data->object->payment_link) {
                return;
            }

            if ($event->data->object->status !== 'complete') {
                return;
            }

            $this->db->processPaymentLinkSuccess(PAYMENT_LINK_STRIPE . '-' . $event->data->object->payment_link, $event);
        }
    }

    public function handleWebHook()
    {
        $endpoint_secret = $this->whsec;

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            print_r($e->getMessage());
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            print_r($e->getMessage());

            // Invalid signature
            http_response_code(400);
            exit();
        }

        $this->handleHookEvent($event);

        http_response_code(200);
    }

    private function inserPaymentLink($id, $payload)
    {
        $this->db->insertPaymentLink(PAYMENT_LINK_STRIPE . '-' . $id, $payload);
    }

    public function createPaymentLink(float $amount, $productName, $type, $uid, $currency = 'usd')
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
