<?php

namespace Casbin\CodeIgniter;

use Casbin\Log\Logger as LoggerContract;

class Logger implements LoggerContract
{
    public $enable = false;

    /**
     * controls whether print the message.
     *
     * @param bool $enable
     */
    public function enableLog($enable)
    {
        $this->enable = $enable;
    }

    /**
     * returns if logger is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enable;
    }

    /**
     * formats using the default formats for its operands and logs the message.
     *
     * @param mixed ...$v
     *
     * @return mixed
     */
    public function write(...$v)
    {
        if (!$this->enable) {
            return;
        }
        $content = '';
        foreach ($v as $value) {
            if (\is_array($value) || \is_object($value)) {
                $value = json_encode($value);
            }
            $content .= $value;
        }

        log_message('info', $content);
    }

    /**
     * formats according to a format specifier and logs the message.
     *
     * @param $format
     * @param mixed ...$v
     *
     * @return mixed
     */
    public function writef($format, ...$v)
    {
        if (!$this->enable) {
            return;
        }

        log_message('info', sprintf($format, ...$v));
    }
}
