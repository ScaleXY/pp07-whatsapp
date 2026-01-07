<?php

namespace ScaleXY\Whatsapp\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ScaleXY\Whatsapp\Events\MessageStatusUpdate;
use ScaleXY\Whatsapp\Events\TextMessageReceived;

abstract class WhatsappWebhookBaseController
{
    protected $webhook_secret;

    protected $api_key;

    abstract protected static function getAppName(): string;

    public function __construct()
    {
        $this->webhook_secret = config('whatsapp.apps.'.$this->getAppName().'.webhook_secret');
        $this->api_key = config('whatsapp.apps.'.$this->getAppName().'.api_key');
    }

    public function __invoke(Request $request)
    {
        switch ($request->method()) {
            case 'GET':
                return self::handleWebhookVerficationRequest($request);
                break;
            case 'POST':
                return self::handlePayload($request);
                break;
            default:
                abort(415);
                break;
        }
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
            // $item['id'];
            foreach ($item['changes'] as $change) {
                // Look for inbound messages
                if (isset($change['value']['messages'])) {
                    foreach ($change['value']['messages'] as $message) {
                        foreach ($change['value']['contacts'] as $contact) {
                            TextMessageReceived::dispatch(
                                $contact['wa_id'],
                                $message['text']['body'],
                                $change['value']['metadata']['phone_number_id'],
                                $message
                            );
                            Log::warning($contact['wa_id'].' said '.$message['text']['body']);
                        }
                    }
                }
                if (isset($change['value']['statuses'])) {
                    foreach ($change['value']['statuses'] as $status) {
                        MessageStatusUpdate::dispatch($status['id'], $status['status'], $status);
                        Log::warning($status['id'].' is '.$status['status']);
                    }
                }
                // Look for message_template_status_update → template approval/rejection
                // Look for errors → delivery or platform errors
                // Look for contacts → user profile info
                // Look for metadata → phone number ID, display number
            }
        }
    }
}
