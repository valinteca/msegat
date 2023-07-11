<?php

namespace Valinteca\Msegat;

use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Valinteca\Msegat\Clients\MsegatClient;
use Valinteca\Msegat\Responses\MsegatResponse;
use Valinteca\Msegat\Constants\GlobalConstants;
use Valinteca\Msegat\Services\SaudiNumberFormatter;
use Valinteca\Msegat\Services\MsegatArgumentValidator;
use Valinteca\Msegat\Exceptions\InvalidArgumentException;

class Msegat
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

    /**
     * Set numbers.
     *
     * @param string|array $numbers numbers to send message
     *
     * @throws InvalidArgumentException if numbers is not valid
     * @return Msegat
     */
    public function to(string|array $numbers)
    {
        $this->numbers = $numbers;
        return $this;
    }

    /**
     * Set message.
     *
     * @param string $message message text
     *
     * @throws InvalidArgumentException if message is not valid
     * @return Msegat
     */
    public function message(string $message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set time to send.
     *
     * @param string|Carbon $at time to send message
     *
     * @throws InvalidArgumentException if at is not valid
     * @return Msegat
     */
    public function at(string|Carbon $at)
    {
        if ($at instanceof Carbon) {
            $at = $at->format(GlobalConstants::DATETIME_FORMAT);
        }

        $this->validator->forAt($at);

        $this->at = $at;
        return $this;
    }

    /**
     * Set sender name.
     *
     * @param string $sender sender name
     *
     * @throws InvalidArgumentException if at is not valid
     * @return Msegat
     */
    public function sender(string $sender)
    {
        $this->validator->forSender($sender);

        $this->sender = $sender;
        return $this;
    }

    /**
     * Set custom options.
     *
     * @param array $options = [] custom options
     *
     * @throws InvalidArgumentException if at is not valid
     * @return Msegat
     */
    public function options(array $options = [])
    {
        $this->validator->forOptions($options);

        $this->options = $options;
        return $this;
    }

    /**
     * Send SMS message
     *
     * @throws InvalidArgumentException if set properties are not valid
     * @throws ResponseErrorException if response code is not success
     * @return array response json
     */
    public function send() : array
    {
        $this->validator->forSend(['numbers' => $this->numbers, 'message' => $this->message]);

        $this->numbers = is_array($this->numbers) ? implode(",", $this->numbers) : $this->numbers;

        $response = $this->client->send([
            'numbers'     => $this->numbers,
            'userSender'  => $this->sender,
            'msg'         => $this->message,
            'msgEncoding' => $this->options['msgEncoding'] ?? 'UTF8',
            'reqBulkId'   => $this->options['reqBulkId'] ?? false,
            'timeToSend'  => $this->at == 'now' ? 'now' : 'later',
            'exactTime'   => $this->at != 'now' ? $this->at : 'now',
            'reqFilter'   => $this->options['reqFilter'] ?? true,
        ]);

        $this->resetProps();
        return (new MsegatResponse($response))->get(require_bulk_id: $this->options['reqBulkId'] ?? false);
    }

    /**
     * Send SMS test message
     *
     * @throws InvalidArgumentException if set properties are not valid
     * @throws ResponseErrorException if response code is not success
     * @return array response json
     */
    public function sendTestMessage()
    {
        $this->validator->forSendTestMessage(['numbers' => $this->numbers]);

        $this->numbers = is_array($this->numbers) ? implode(",", $this->numbers) : $this->numbers;

        return $this->sender('auth-mseg')
            ->message('Verification Code: xxxx')
            ->send();
    }

    /**
     * Send SMS personalized message
     *
     * @throws InvalidArgumentException if set properties are not valid
     * @throws ResponseErrorException if response code is not success
     * @return array response json
     */
    public function sendPersonalized(array $vars)
    {
        $this->validator->forSendPersonalized([
            'numbers' => $this->numbers, 
            'message' => $this->message,
            'vars' => $vars,
        ]);

        $this->numbers = is_array($this->numbers) ? implode(",", $this->numbers) : $this->numbers;

        $response = $this->client->sendPersonalized([
            'numbers'     => $this->numbers,
            'userSender'  => $this->sender,
            'msg'         => $this->message,
            'msgEncoding' => $this->options['msgEncoding'] ?? 'UTF8',
            'reqBulkId'   => $this->options['reqBulkId'] ?? false,
            'timeToSend'  => $this->at == 'now' ? 'now' : 'later',
            'exactTime'   => $this->at != 'now' ? $this->at : 'now',
            'reqFilter'   => $this->options['reqFilter'] ?? true,
            'vars'        => $vars,
        ]);

        $this->resetProps();
        return (new MsegatResponse($response))->get(require_bulk_id: $this->options['reqBulkId'] ?? false);
    }

    public function sendOTP()
    {
        $this->validator->forSendOTP(['number' => $this->numbers, 'message' => $this->message]);

        $response = $this->client->sendOTP([
            'lang' => $this->options['lang'] ?? $this->lang,
            'number' => $this->numbers,
            'userSender' => $this->sender,
        ]);

        $this->resetProps();
        return (new MsegatResponse($response))->get();
    }

    public function verifyOTP(string $id, string $code)
    {
        $response = $this->client->verifyOTP([
            'lang' => $this->options['lang'] ?? $this->lang,
            'id' => $id,
            'code' => $code,
        ]);

        $this->resetProps();
        return [
            'status' => $response->status(),
            'data'   => $response->json(),
        ];
    }

    public function forBulkId(string $bulk_id)
    {
        $this->bulk_id = $bulk_id;
        return $this;
    }

    public function page(int $page)
    {
        $this->validator->forPage($page);

        $this->page = $page;
        return $this;
    }

    public function limit(int $limit)
    {
        $this->validator->forLimit($limit);

        $this->limit = $limit;
        return $this;
    }

    public function getMessages()
    {
        $this->validator->forGetMessages(['bulk_id' => $this->bulk_id]);

        $filters['reqBulkId'] = $this->bulk_id;
        $filters['pageNumber'] = $this->page;
        if ($this->limit) {
            $filters['limit'] = $this->limit;
        }
        $response = $this->client->getMessages($filters);

        $this->resetProps();
        return (new MsegatResponse($response))->get();
    }

    /**
     * Get senders and its status.
     *
     * @return array response json
     */
    public function getSenders()
    {
        $this->resetClient();
        $response = $this->client->getSenders();

        return (new MsegatResponse($response))->get(has_data_key: false);
    }

    /**
     * Get your current balance.
     *
     * @return array response json
     */
    public function getBalance()
    {
        $this->resetClient();
        $response = $this->client->getBalance();

        // Facade::clearResolvedInstance('msegat');

        return (new MsegatResponse($response))->getForFormData(key: 'balance');
    }

    /**
     * Calculate message cost.
     *
     * @throws ResponseErrorException if response code is not success
     * @return array response json
     */
    public function calculateCost()
    {
        $this->validator->forCalculateCost([
            'numbers' => $this->numbers,
            'message' => $this->message,
        ]);

        if (is_string($this->numbers)) {
            $this->numbers = [$this->numbers];
        }

        $this->numbers = array_map(fn($number) => (new SaudiNumberFormatter($number))->getWithCountryCode(), $this->numbers);
        $this->numbers = implode(",", $this->numbers);

        $response = $this->client->calcMessageCost([
            'contactType' => 'numbers',
            'contacts'    => $this->numbers,
            'msg'         => $this->message,
            'By'          => 'Link',
            "msgEncoding" => 'UTF8',
        ]);

        return (new MsegatResponse($response))->getForFormData(key: 'cost');
    }

    /**
     * Resets properties to the default values
     * to prevent facade singleton behaviour 
     */
    private function resetProps()
    {
        $this->numbers = null;
        $this->options = [];
        $this->message = null;
        $this->bulk_id = null;
        $this->at = 'now';
    }

    private function resetClient()
    {
        $this->client = new MsegatClient;
    }
}
