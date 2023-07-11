<?php

namespace Valinteca\Msegat\Clients;

use Illuminate\Support\Facades\Http;

class MsegatClient
{
    private $http;

    public function __construct()
    {
        $this->http = $this->getClient();
    }

    private function getClient()
    {
        return Http::baseUrl(config('msegat.base_url'));
    }

    private function getCrendentials()
    {
        return [
            'userName' => config('msegat.username'),
            'apiKey'   => config('msegat.api_key'),
        ];
    }

    public function send(array $data)
    {
        return $this->http->post(config('msegat.endpoints.send'), array_merge($this->getCrendentials(), $data));
    }

    public function sendPersonalized(array $data)
    {
        return $this->http->post(config('msegat.endpoints.send_personalized'), array_merge($this->getCrendentials(), $data));
    }

    public function sendOTP(array $data)
    {
        return $this->http->post(config('msegat.endpoints.send_otp'), array_merge($this->getCrendentials(), $data));
    }

    public function verifyOTP(array $data)
    {
        return $this->http->post(config('msegat.endpoints.verify_otp'), array_merge($this->getCrendentials(), $data));
    }

    public function getSenders()
    {
        return $this->http->post(config('msegat.endpoints.get_senders'), $this->getCrendentials());
    }

    public function getMessages(array $filters)
    {
        return $this->http->post(config('msegat.endpoints.get_messages'), array_merge($this->getCrendentials(), $filters));
    }

    public function getBalance()
    {
        $http = $this->http;
        $form_data = $this->getCrendentials();

        foreach ($form_data as $key => $value) {
            $http->attach($key, $value);
        }

        return $http->post(config('msegat.endpoints.balance_inquiry'), []);
    }

    public function calcMessageCost(array $data)
    {
        $http = $this->http;
        $form_data = array_merge($this->getCrendentials(), $data);

        foreach ($form_data as $key => $value) {
            $http->attach($key, $value);
        }

        return $http->post(config('msegat.endpoints.calculate_cost'), []);
    }
}