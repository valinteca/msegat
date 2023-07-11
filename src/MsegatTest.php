<?php

namespace Valinteca\Msegat;

use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;
use Valinteca\Msegat\Clients\MsegatClient;
use Valinteca\Msegat\Responses\MsegatResponse;
use Valinteca\Msegat\Constants\GlobalConstants;
use Valinteca\Msegat\Services\SaudiNumberFormatter;
use Valinteca\Msegat\Services\MsegatArgumentValidator;
use Valinteca\Msegat\Exceptions\InvalidArgumentException;

class MsegatTest
{
    private $client;
    private $validator;
    private $sender;
    private $lang;
    private $numbers = null;
    private $options = [];
    private $message = null;
    private $bulk_id = null;
    private $page = 1;
    private $limit = null;
    private $at = 'now';

    public function __construct()
    {
        $this->client = new MsegatClient;
        $this->validator = new MsegatArgumentValidator;
        $this->sender = config('msegat.sender_name');
        $this->lang = config('msegat.lang');
    }

    public static function __callStatic($name, $arguments)
    {
        dump($name, $arguments);
    }

    public function __call($name, $arguments)
    {
        dump($name, $arguments);
    }
}
