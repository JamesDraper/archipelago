<?php

interface Archipelago_Island_Boundary_Adapter
{
    /**
     * @return Archipelago_Island_Inner_Request
     */
    public function requestIn(Archipelago_Island_Outer_Request $request);

    /**
     * @return Archipelago_Island_Outer_Response
     */
    public function responseOut(Archipelago_Island_Inner_Response $response);
}
