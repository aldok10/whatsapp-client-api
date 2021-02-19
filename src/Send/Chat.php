<?php
namespace AldoKarendra\WhatsappClient\Send;

use AldoKarendra\WhatsappClient\ApiService;

class Chat extends ApiService
{
	public $service_name = "send";

    public function text($phone, $message)
    {
        $response = $this->post(
        	"/text/{$phone}",
        	['message' => $message]
        );
        return json_decode($response, true);
    }
    
}