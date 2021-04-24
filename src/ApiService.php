<?php
namespace AldoKarendra\WhatsappClient;

use GuzzleHttp\Client;

class ApiService
{

    /**
     * Guzzle HTTP Client object
     * @var \GuzzleHttp\Client
     */
    private $clients;

    /**
     * Request headers
     * @var array
     */
    private $headers;

    /**
     * Authorization header value
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $public_key;

    /**
     * @var string
     */
    private $secret_key;

    /**
     * @var string
     */
    private $base_url;

    /**
     * @var string
     */
    public $service_name;

    /**
     * @var string
     */
    public $enable_error = false;

    public function __construct($configurations)
    {
        $this->clients = new Client([
            'verify' => false
        ]);

        foreach ($configurations as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }

        //set Authorization, and finally the headers
        $this->setHeaders();
    }

    protected function setHeaders()
    {
        $file = md5($this->secret_key) . "/" . sha1($this->public_key) . ".json";
        $path = "./" . $file;

        $response = "";
        $isNotExists = true;

        if (file_exists($path)) {
            $file_handle = fopen($path, 'r');
            $response = fread($file_handle, filesize($path));
            fclose($file_handle);

            $response = json_decode($response);
            
            $isNotExists = false;

            if ($response!=="" && $response!=='{"status":false,"message":"Username & Password Harus Diisi","data":null}') {
                if ((bool)$response->status === false) {
                    $isNotExists = true;
                }
            }
        }

        if ($isNotExists) {
            if (is_dir(md5($this->secret_key)) === false) {
                mkdir(md5($this->secret_key), 0777, true);
            }

            try {
                $response = $this->clients->request(
                    'POST',
                    $this->base_url . '/auth',
                    [
                        'headers' => $this->headers,
                        'form_params' => [
                            'username' => $this->public_key,
                            'password' => $this->secret_key,
                        ],
                    ]
                )->getBody()->getContents();
            } catch (\Exception $e) {
                if ($this->enable_error === false) {
                    $response = '{"status":false,"message":"Akses Terlarang!","data":null}';
                } else {
                    $response = $e->getResponse()->getBody()->getContents();
                }
            }

            $file_handle = fopen($path, 'w');
            fwrite($file_handle, $response);
            fclose($file_handle);

            $response = json_decode($response);
        }

        if ((bool)$response->status === true) {
            $this->token = $response->data->token;
        }

        if ($this->token) {
            $this->headers = [
                'Authorization' => "Bearer " . $this->token,
            ];
        }

        return $this;
    }

    protected function get($feature)
    {
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';
        try {
            $response = $this->clients->request(
                'GET',
                $this->base_url . '/' . $this->service_name . $feature,
                [
                    'headers' => $this->headers
                ]
            )->getBody()->getContents();
        } catch (\Exception $e) {
            if ($this->enable_error === false) {
                $response = '{"status":false,"message":"Akses Terlarang!","data":null}';
            } else {
                $response = $e->getResponse()->getBody()->getContents();
            }
        }
        return $response;
    }

    protected function post($feature, $data = [], $headers = [])
    {
        $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        if (!empty($headers)) {
            $this->headers = array_merge($this->headers, $headers);
        }
        try {
            $response = $this->clients->request(
                'POST',
                $this->base_url . '/' . $this->service_name . $feature,
                [
                    'headers' => $this->headers,
                    'form_params' => $data,
                ]
            )->getBody()->getContents();
        } catch (\Exception $e) {
            if ($this->enable_error === false) {
                $response = '{"status":false,"message":"Akses Terlarang!","data":null}';
            } else {
                $response = $e->getResponse()->getBody()->getContents();
            }
        }
        return $response;
    }

    protected function put($feature, $data = [])
    {
        $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        try {
            $response = $this->clients->request(
                'PUT',
                $this->base_url . '/' . $this->service_name . $feature,
                [
                    'headers' => $this->headers,
                    'form_params' => $data,
                ]
            )->getBody()->getContents();
        } catch (\Exception $e) {
            if ($this->enable_error === false) {
                $response = '{"status":false,"message":"Akses Terlarang!","data":null}';
            } else {
                $response = $e->getResponse()->getBody()->getContents();
            }
        }
        return $response;
    }


    protected function delete($feature, $data = [])
    {
        $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        try {
            $response = $this->clients->request(
                'DELETE',
                $this->base_url . '/' . $this->service_name . $feature,
                [
                    'headers' => $this->headers,
                    'form_params' => $data,
                ]
            )->getBody()->getContents();
        } catch (\Exception $e) {
            if ($this->enable_error === false) {
                $response = '{"status":false,"message":"Akses Terlarang!","data":null}';
            } else {
                $response = $e->getResponse()->getBody()->getContents();
            }
        }
        return $response;
    }
}
