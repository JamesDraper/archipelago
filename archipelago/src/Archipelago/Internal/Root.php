<?php

/**
 * @internal
 */
class Archipelago_Internal_Root
{
    /**
     * @return self|null
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

    public function __construct()
    {
        // todo: finish this.
    }

    /**
     * @param string $island
     * @return Archipelago_Island_Outer_Response
     */
    public function run($island, Archipelago_Island_Outer_Request $request)
    {
        // todo: finish this.
    }

    /**
     * @return void
     */
    public function register(Archipelago_Island $island)
    {
        // todo: finish this.
    }
}
