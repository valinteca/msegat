<?php

namespace Valinteca\Msegat\Responses;

use BadMethodCallException;
use Illuminate\Support\Str;
use Valinteca\Msegat\Exceptions\ResponseErrorException;

class MsegatResponse
{
    private $success_codes = [
        '1'     => 'success',
        'M0000' => 'success',
    ];

    private $error_codes = [
        'M0001'  => 'Variables missing',
        'M0002'  => 'Invalid login info',
        'M0022'  => 'Exceed number of senders allowed',
        'M0023'  => 'Sender Name is active or under activation or refused',
        'M0024'  => 'Sender Name should be in English or number',
        'M0025'  => 'Invalid Sender Name Length',
        'M0026'  => 'Sender Name is already activated or not found',
        'M0027'  => 'Activation Code is not Correct',
        '1010'   => 'Variables missing',
        '1020'   => 'Invalid login info',
        '1050'   => 'MSG body is empty',
        '1060'   => 'Balance is not enough',
        '1061'   => 'MSG duplicated',
        '1064'   => 'Free OTP , Invalid MSG content you should use "Pin Code is: xxxx" or "Verification Code: xxxx" or "رمز التحقق: 1234" , or upgrade your account and activate your sender to send any content',
        '1110'   => 'Sender name is missing or incorrect',
        '1120'   => 'Mobile numbers is not correct',
        '1140'   => 'MSG length is too long',
        'M0029'  => 'Invalid Sender Name - Sender Name should contain only letters, numbers and the maximum length should be 11 characters',
        'M0030'  => 'Sender Name should ended with AD',
        'M0031'  => 'Maximum allowed size of uploaded file is 5 MB',
        'M0032'  => 'Only pdf,png,jpg and jpeg files are allowed!',
        'M0033'  => 'Sender Type should be normal or whitelist only',
        'M0034'  => 'Please Use POST Method',
        'M0036'  => 'There is no any sender',
    ];

    public function __construct(private $http_response)
    {
        
    }

    public function getForFormData($key = 'value')
    {
        $response = $this->http_response->body();
        $success = ! in_array($response, array_keys($this->error_codes));
        $code = $success ? "1" : $response;
        $message = $success ? $this->success_codes[$code] : $this->error_codes[$code];

        if (! $success) {
            throw new ResponseErrorException("Error code $code: $message");
        }

        return [
            'success' => true,
            'data' => [
                $key => $response,
            ],
        ];
    }

    public function get($require_bulk_id = false, $has_data_key = true)
    {
        $response = $this->http_response->json();

        if ($require_bulk_id) {
            $code = $response['code'];
            $bulk_id = Str::after($code, '-');

            if (! empty($bulk_id) && $code != $bulk_id) {
                $response['bulk_id'] = $bulk_id;
                $response['code'] = Str::before($code, '-');
            }
        }

        $return['success'] = null;

        if (in_array($response['code'] ?? '', array_keys($this->success_codes))) {
            $return['success'] = true;
        } elseif (in_array($response['code'] ?? '', array_keys($this->error_codes))) {
            if (empty($response['message'])) {
                $response['message'] = $this->error_codes[$response['code']];
            }

            throw new ResponseErrorException("Error code {$response['code']}: {$response['message']}");
        }

        return [
            'success' => true,
            'data' => $has_data_key ? ($response['data'] ?? []) : $response,
        ];
    }
}