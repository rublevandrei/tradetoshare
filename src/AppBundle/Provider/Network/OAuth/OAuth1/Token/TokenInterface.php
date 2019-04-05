<?php

namespace AppBundle\Provider\Network\OAuth\OAuth1\Token;

use AppBundle\Provider\Network\OAuth\Common\Token\TokenInterface as BaseTokenInterface;

/**
 * OAuth1 specific token interface
 */
interface TokenInterface extends BaseTokenInterface
{
    /**
     * @return string
     */
    public function getAccessTokenSecret();

    /**
     * @param string $accessTokenSecret
     */
    public function setAccessTokenSecret($accessTokenSecret);

    /**
     * @return string
     */
    public function getRequestTokenSecret();

    /**
     * @param string $requestTokenSecret
     */
    public function setRequestTokenSecret($requestTokenSecret);

    /**
     * @return string
     */
    public function getRequestToken();

    /**
     * @param string $requestToken
     */
    public function setRequestToken($requestToken);
}
