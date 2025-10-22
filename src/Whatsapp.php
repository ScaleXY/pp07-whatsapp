<?php

namespace ScaleXY\Whatsapp;

use Illuminate\Support\Facades\Http;

class Whatsapp
{
    protected $app_name;

    protected $number_id;

    protected $api_key;

    protected $client;

    public function __construct($config = [])
    {
        $this->app_name = $config['app_name'] ?? config('whatsapp.apps.'.config('whatsapp.default_app').'.app_name');
        // $this->webhook_secret = $config['webhook_secret'] ?? config('whatsapp.apps.' . $this->app_name . '.webhook_secret');
        $this->api_key = $config['api_key'] ?? config('whatsapp.apps.'.$this->app_name.'.api_key');
        $this->number_id = $config['number_id'] ?? config('whatsapp.apps.'.$this->app_name.'.number_id');
        $this->client = Http::withToken($this->api_key);
    }

    public function sendFreeText($number, $message)
    {
        return $this->client->post('https://graph.facebook.com/v22.0/'.$this->number_id.'/messages', [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $number,
            'text' => [
                'preview_url' => false,
                'body' => $message,
            ],
        ]);
    }
}
