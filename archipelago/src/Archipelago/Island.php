<?php

interface Archipelago_Island
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return Archipelago_Island_Inner_Ingress
     */
    public function getIngress();

    /**
     * @return Archipelago_Island_Boundary_Bootstrapper|null
     */
    public function getBootstrapper();

    /**
     * @return Archipelago_Island_Boundary_Configurator|null
     */
    public function getConfigurator();

    /**
     * @return Archipelago_Island_Boundary_Adapter|null
     */
    public function getAdapter();
}
