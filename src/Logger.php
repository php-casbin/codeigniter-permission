<?php

namespace Casbin\CodeIgniter;

use Casbin\Log\Logger as LoggerContract;
use Casbin\Bridge\Logger\LoggerBridge;
use Config\Services;

class Logger extends LoggerBridge implements LoggerContract
{
    /**
     * LoggerBridge constructor.
     */
    public function __construct()
    {
        parent::__construct(Services::logger(true));
    }
}
