<?php

interface Archipelago_Island_Boundary_Bootstrapper
{
    /**
     * @return void
     */
    public function onFirstRun();

    /**
     * @return void
     */
    public function onRegister();
}
