<?php

final class Archipelago
{
    /**
     * @param string $island
     * @return Archipelago_Island_Outer_Response
     */
    public static function run($island, Archipelago_Island_Outer_Request $request)
    {
        return Archipelago_Internal_Root::getInstance()->run($island, $request);
    }

    /**
     * @return void
     */
    public static function register(Archipelago_Island $island)
    {
        Archipelago_Internal_Root::getInstance()->register($island);
    }

    private function __construct() {}
}
