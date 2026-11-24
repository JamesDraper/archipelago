<?php

final class Archipelago
{
    /**
     * @param string $island
     * @return Archipelago_Island_Outer_Response
     */
    public static function run($island, Archipelago_Island_Outer_Request $request)
    {
        require_once dirname(__FILE__) . '/Archipelago/Internal/Root.php';

        return Archipelago_Internal_Root::getInstance()->run($island, $request);
    }

    /**
     * @return void
     */
    public static function register(Archipelago_Island $island)
    {
        require_once dirname(__FILE__) . '/Archipelago/Internal/Root.php';

        Archipelago_Internal_Root::getInstance()->register($island);
    }

    private function __construct() {}
}
