<?php

namespace ScaleXY\Whatsapp;

use Illuminate\Support\Facades\Http;

class Whatsapp
{
    protected $app_name;

    protected $number_id;

    protected $type;

    protected $api_key;

    protected $client;

    protected $recipient_type;

    protected $to;

    protected $footer;

    public function __construct($config = [])
    {
        $this->app_name = $config['app_name'] ?? config('whatsapp.default_app_name');
        // $this->webhook_secret = $config['webhook_secret'] ?? config('whatsapp.apps.' . $this->app_name . '.webhook_secret');
        $this->api_key = $config['api_key'] ?? config('whatsapp.apps.'.$this->app_name.'.api_key');
        $this->number_id = $config['number_id'] ?? config('whatsapp.apps.'.$this->app_name.'.number_id');
        $this->client = Http::withToken($this->api_key);
    }

    public function sendFreeText($message)
    {
        $this->type = 'text';

        return $this->sendRawMessage([
            'text' => [
                'body' => $message,
            ],
        ]);
    }

    public function sendDocument($link, $caption = null, $filename = null)
    {
        $this->type = 'document';
        $document = ['link' => $link];
        if ($caption) {
            $document['caption'] = $caption;
        }
        if ($filename) {
            $document['filename'] = $filename;
        }

        return $this->sendRawMessage([
            'document' => $document,
        ]);
    }

    public function sendAuthMessage($code, $template_id = null)
    {
        $this->type = 'template';
        $template_id = $template_id ?? config('whatsapp.default_templates.auth_message');

        return $this->sendRawMessage([
            'template' => [
                'name' => $template_id,
                'language' => [
                    'code' => 'en',
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $code,
                            ],
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $code,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function sendTemplateMessage($template_message)
    {
        $template_id = $template_message->getTemplateId();
        $components = $template_message->getComponentsJSON();
        $this->type = 'template';

        return $this->sendRawMessage([
            'template' => [
                'name' => $template_id,
                'language' => [
                    'code' => 'en',
                ],
                'components' => $components,
            ],
        ]);
    }

    public function setIndividualAsRecipient($number)
    {
        $this->recipient_type = 'individual';
        $this->to = $number;

        return $this;
    }

    public function sendRawMessage($data)
    {
        $data['messaging_product'] = 'whatsapp';
        if ($this->type) {
            $data['type'] = $this->type;
        } else {
            throw new \InvalidArgumentException('Message type not set. Call appropriate send method first.');
        }
        if ($this->recipient_type) {
            $data['recipient_type'] = $this->recipient_type;
        } else {
            throw new \InvalidArgumentException('Recipient type not set. Call setIndividualAsRecipient() first.');
        }
        if ($this->to) {
            $data['to'] = $this->to;
        } else {
            throw new \InvalidArgumentException('Recipient number not set. Call setIndividualAsRecipient() first.');
        }

        return $this->client->post('https://graph.facebook.com/v22.0/'.$this->number_id.'/messages', $data)->json();
    }
}
