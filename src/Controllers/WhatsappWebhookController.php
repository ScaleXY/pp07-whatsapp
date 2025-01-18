<?php

namespace ScaleXY\Whatsapp\Controllers;

use Illuminate\Http\Request;

class WhatsappWebhookController
{
    protected $app_name;

    protected $webhook_secret;

    protected $api_key;

    protected $callback_static_class;

    protected $callback_static_function;

    public function __construct($config = [])
    {
        $this->app_name = $config['app_name'] ?? config('whatsapp.apps.'.config('whatsapp.default_app').'.app_name');
        $this->webhook_secret = $config['webhook_secret'] ?? config('whatsapp.apps.'.$this->app_name.'.webhook_secret');
        $this->api_key = $config['api_key'] ?? config('whatsapp.apps.'.$this->app_name.'.api_key');
        $this->callback_static_class = $config['callback_static_class'] ?? null;
        $this->callback_static_function = $config['callback_static_function'] ?? null;
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
                        // self::handleMessage($contact['wa_id'], $message['text']['body']);
                        $this->callback_static_class::{$this->callback_static_function}($contact['wa_id'], $message['text']['body']);
                    }
                }
            }
        }
    }
}
