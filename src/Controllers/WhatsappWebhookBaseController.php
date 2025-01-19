<?php

namespace ScaleXY\Whatsapp\Controllers;

use Illuminate\Http\Request;
use ScaleXY\Whatsapp\Events\TextMessageReceived;

class WhatsappWebhookBaseController
{
    protected $webhook_secret;

    protected $api_key;

    public function __construct()
    {
        $this->webhook_secret = config('whatsapp.apps.'.$this->app_name.'.webhook_secret');
        $this->api_key = config('whatsapp.apps.'.$this->app_name.'.api_key');
    }

    public function handleWebhookVerficationRequest(Request $request): string
    {
        $hub_mode = $request->input('hub_mode', null);
        $hub_verify_token = $request->input('hub_verify_token', null);
        $hub_challenge = $request->input('hub_challenge', '');
        if ($hub_mode !== 'subscribe' || $hub_verify_token !== $this->webhook_secret) {
            http_response_code(403);

            return $hub_challenge;
        }
        http_response_code(200);

        return $hub_challenge;
    }

    public function handlePayload(Request $req)
    {
        switch ($req->object) {
            case 'whatsapp_business_account':
                return self::handleWhatsappBusinessAccount($req->entry);
                break;
        }
    }

    public function handleWhatsappBusinessAccount($entry)
    {
        foreach ($entry as $item) {
            foreach ($item['changes'] as $change) {
                foreach ($change['value']['contacts'] as $contact) {
                    foreach ($change['value']['messages'] as $message) {
                        TextMessageReceived::dispatch($contact['wa_id'].' said '.$message['text']['body']);
                    }
                }
            }
        }
    }
}
