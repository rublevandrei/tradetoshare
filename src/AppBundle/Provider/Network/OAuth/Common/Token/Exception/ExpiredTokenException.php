<?php

namespace AppBundle\Provider\Network\OAuth\Common\Token\Exception;

use AppBundle\Provider\Network\OAuth\Common\Exception\Exception;

/**
 * Exception thrown when an expired token is attempted to be used.
 */
class ExpiredTokenException extends Exception
{
}
