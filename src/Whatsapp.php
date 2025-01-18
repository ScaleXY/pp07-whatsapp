<?php

namespace ScaleXY\Whatsapp;

class Whatsapp
{
    protected $app_name;

    // protected $webhook_secret;
    protected $api_key;

    public function __construct($config = [])
    {
        $this->app_name = $config['app_name'] ?? config('whatsapp.apps.'.config('whatsapp.default_app').'.app_name');
        // $this->webhook_secret = $config['webhook_secret'] ?? config('whatsapp.apps.' . $this->app_name . '.webhook_secret');
        $this->api_key = $config['api_key'] ?? config('whatsapp.apps.'.$this->app_name.'.api_key');
    }
}
