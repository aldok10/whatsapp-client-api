<?php
namespace AldoKarendra\WhatsappClient\Auth;

use AldoKarendra\WhatsappClient\ApiService;

class Qr extends ApiService
{
	public $service_name = "";

    public function getQr($type = 'json')
    {
        $response = $this->get(
        	"login?type={$type}",
        );
        return $response;
    }
    
}