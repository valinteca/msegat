<?php

namespace Valinteca\Msegat\Exceptions\Contracts;

use Exception;

abstract class MsegatException extends Exception
{
    /**
     * @param $e
     */
    public function __construct(private string $e = '')
    {
        $this->message = $e;
    }
}