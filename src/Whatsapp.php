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
        $this->app_name = $config['app_name'] ?? config('whatsapp.default_app_name');
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

    public function sendAuthMessage($number, $code, $template_id = null)
    {
		$template_id = $template_id ?? config('whatsapp.default_templates.auth_message');
        return $this->client->post('https://graph.facebook.com/v22.0/'.$this->number_id.'/messages', [
            'messaging_product' => 'whatsapp',
  			"type" => "template",
            'to' => $number,
            'template' => [
                'name' => $template_id,
				"language" => [
					"code" => "en"
				],
				"components" => [
					[
						"type" => "body",
						"parameters" => [
							[
								"type" => "text",
								"text" => $code
							]
						]
					],
					[
						"type" => "button",
						"sub_type" => "url",
						"index" => "0",
						"parameters" => [
							[
								"type" => "text",
								"text" => $code
							]
						]
					]
				]
            ],
        ]);
    }
}
