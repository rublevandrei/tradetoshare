<?php

namespace AppBundle\Provider\Network\OAuth\OAuth1\Signature\Exception;

use AppBundle\Provider\Network\OAuth\Common\Exception\Exception;

/**
 * Thrown when an unsupported hash mechanism is requested in signature class.
 */
class UnsupportedHashAlgorithmException extends Exception
{
}
