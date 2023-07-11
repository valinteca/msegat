<?php

namespace Valinteca\Msegat\Services;

use Illuminate\Support\Facades\Validator;
use Valinteca\Msegat\Rules\ValidSaudiNumber;
use Valinteca\Msegat\Constants\GlobalConstants;
use Valinteca\Msegat\Rules\FilledArrayOrString;
use Valinteca\Msegat\Exceptions\InvalidArgumentException;
use Valinteca\Msegat\Rules\VarsArrayIncludesAllMessageKeys;

class MsegatArgumentValidator
{
    public function forSend(array $args)
    {
        $validator = Validator::make($args, [
            'numbers' => [new FilledArrayOrString, new ValidSaudiNumber],
            'message' => ['required', 'string'],
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forSendPersonalized(array $args)
    {
        $numbers_count = is_array($args['numbers']) ? count($args['numbers']) : 1;
        preg_match_all("/\{(.*?)\}/", $args['message'], $matches);
        $keys = isset($matches[1]) ? array_filter($matches[1], fn($match) => ! empty($match)) : [];

        $validator = Validator::make($args, [
            'numbers' => [new FilledArrayOrString, new ValidSaudiNumber],
            'message' => ['required', 'string'],
            'vars' => ['required', 'array', "size:$numbers_count", new VarsArrayIncludesAllMessageKeys($keys)],
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forSendTestMessage(array $args)
    {
        $validator = Validator::make($args, [
            'numbers' => [new FilledArrayOrString, new ValidSaudiNumber],
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forSendOTP(array $args)
    {
        $validator = Validator::make($args, [
            'number'  => ['required', 'string', new ValidSaudiNumber],
            'message' => ['required', 'string'],
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forGetMessages(array $args)
    {
        $validator = Validator::make($args, [
            'bulk_id' => ['required', 'string']
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forCalculateCost(array $args)
    {
        $validator = Validator::make($args, [
            'numbers' => [new FilledArrayOrString, new ValidSaudiNumber],
            'message' => ['required', 'string'],
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forSender($sender)
    {
        $validator = Validator::make(['sender' => $sender], [
            'sender' => ['required', 'string']
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forOptions(array $options)
    {
        $validator = Validator::make($options, [
            'reqBulkId' => ['nullable', 'boolean'],
            'msgEncoding' => ['nullable', 'in:UTF8,windows-1256'],
            'reqFilter' => ['nullable', 'boolean'],
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forAt($at)
    {
        $validator = Validator::make(['at' => $at], [
            'at' => ['required', 'date_format:' . GlobalConstants::DATETIME_FORMAT, 'after:now'],
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forPage($page)
    {
        $validator = Validator::make(['page' => $page], [
            'page' => ['required', 'integer', 'gte:1'],
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function forLimit($limit)
    {
        $validator = Validator::make(['limit' => $limit], [
            'limit' => ['required', 'integer', 'gte:1'],
        ]);

        $this->stopOnFirstFailure($validator);
    }

    public function stopOnFirstFailure(\Illuminate\Validation\Validator $validator)
    {
        if ($validator->stopOnFirstFailure()->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }
    }
}