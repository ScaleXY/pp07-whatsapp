<?php

namespace ScaleXY\Whatsapp\Components;

class Template
{
    protected $buttons;

    protected $body_components = [];

    protected $header_components = [];

    public function __construct(
        protected $template_id
    ) {}

    public function setVariable($group, $name, $value)
    {
        $this->{$group.'_components'}[] = [
            'type' => 'text',
            'parameter_name' => $name,
            'text' => $value,
        ];
    }

    public function setHeaderImageUrl($url)
    {
        $this->header_components = [
            [
                'type' => 'image',
                'image' => [
                    'link' => $url,
                ],
            ],
        ];
    }

    public function getComponentsJSON()
    {
        $components = [];
        if (count($this->body_components) > 0) {
            $components[] = [
                'type' => 'body',
                'parameters' => $this->body_components,
            ];
        }
        if (count($this->header_components) > 0) {
            $components[] = [
                'type' => 'header',
                'parameters' => $this->header_components,
            ];
        }

        return $components;
    }

    public function getTemplateId()
    {
        return $this->template_id;
    }
}
