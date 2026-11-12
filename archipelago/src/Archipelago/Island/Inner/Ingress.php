<?php

interface Archipelago_Island_Inner_Ingress
{
    /**
     * @return Archipelago_Island_Inner_Response
     */
    public function invoke(Archipelago_Island_Inner_Request $request);
}
