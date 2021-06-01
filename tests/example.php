<?php

require_once __DIR__.'/../vendor/autoload.php';

use AldoKarendra\WhatsappClient\Send\Chat;
use AldoKarendra\WhatsappClient\Auth\Qr;

//use your own config
$config = [
    'base_url' => 'http://127.0.0.1:3002/api/v1/whatsapp',
    'public_key' => 'aldo_k',
    'secret_key' => 'mysecretpassword',
    // 'enable_error' => true,
    'enable_error' => false,
];

// use Qr service
$qr = new Qr($config);

// get barcode if not scan barcode whatsapp
$response = $qr->getQr('html');

if ($response === '{"info":{"status_battery":"-","status_plugged":"-"},"msg":"Sudah Login"}') {
    
    // use chat service
    $chat = new Chat($config);

    // Send Msg
    $response = $chat->text('62895623526803', 'Send Msg!');
    echo "<pre style='background:#fafafa;'>";
    var_dump($response);
    echo "</pre>";
} else {
    // echo $response;
    if ($response=='{"status":false,"message":"Akses Terlarang!","data":null}') {
        echo "invalid access public_key / secret_key!";
    } else {
        echo "Get Access. <br>";
        echo $response;
    }
}
