<?php

namespace AppBundle\Provider\Network\OAuth\OAuth1\Service;

use AppBundle\Provider\Network\OAuth\Common\Consumer\CredentialsInterface;
use AppBundle\Provider\Network\OAuth\Common\Storage\TokenStorageInterface;
use AppBundle\Provider\Network\OAuth\Common\Token\TokenInterface;
use AppBundle\Provider\Network\OAuth\Common\Http\Client\ClientInterface;
use AppBundle\Provider\Network\OAuth\Common\Http\Uri\UriInterface;
use AppBundle\Provider\Network\OAuth\Common\Http\Exception\TokenResponseException;
use AppBundle\Provider\Network\OAuth\Common\Service\ServiceInterface as BaseServiceInterface;
use AppBundle\Provider\Network\OAuth\OAuth1\Signature\SignatureInterface;

/**
 * Defines the common methods across OAuth 1 services.
 */
interface ServiceInterface extends BaseServiceInterface
{
    /**
     * Retrieves and stores/returns the OAuth1 request token obtained from the service.
     *
     * @return TokenInterface $token
     *
     * @throws TokenResponseException
     */
    public function requestRequestToken();

    /**
     * Retrieves and stores/returns the OAuth1 access token after a successful authorization.
     *
     * @param string $token       The request token from the callback.
     * @param string $verifier
     * @param string $tokenSecret
     *
     * @return TokenInterface $token
     *
     * @throws TokenResponseException
     */
    public function requestAccessToken($token, $verifier, $tokenSecret);

    /**
     * @return UriInterface
     */
    public function getRequestTokenEndpoint();
}
