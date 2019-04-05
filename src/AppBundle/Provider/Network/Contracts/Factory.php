<?php

namespace AppBundle\Provider\Network\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param  string  $driver
     * @return \AppBundle\Provider\Network\Contracts\Provider
     */
    public function driver($driver = null);
}
