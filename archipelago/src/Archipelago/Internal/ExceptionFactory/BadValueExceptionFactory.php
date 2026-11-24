<?php

/**
 * @internal
 */
class Archipelago_Internal_ExceptionFactory_BadValueExceptionFactory
{
    /**
     * @var self|null
     */
    private static $instance = null;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $message
     * @return Archipelago_BadValueException
     */
    public function makeException($message)
    {
        require_once dirname(__FILE__) . '/../../Exception.php';
        require_once dirname(__FILE__) . '/../../BadValueException.php';

        return new Archipelago_BadValueException($message);
    }
}
