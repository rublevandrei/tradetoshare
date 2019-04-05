<?php

namespace AppBundle\Provider\Network\One;

use AppBundle\Provider\Network\Contracts\Provider as ProviderContract;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use AppBundle\Provider\Network\OAuth\Common\Storage\Session;
use AppBundle\Provider\Network\OAuth\Common\Consumer\Credentials;
use AppBundle\Provider\Network\OAuth\OAuth1\Signature\Signature;
use AppBundle\Provider\Network\OAuth\Common\Http\Client\StreamClient;

abstract class AbstractProvider implements ProviderContract
{
    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Get the raw user for the given access token.
     *
     * @param  string $token
     * @return array
     */
    abstract protected function getUserByToken($token);

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array $user
    //* @return \Laravel\Socialite\Two\User
     */
    abstract protected function mapUserToObject(array $user);

    abstract protected function getUserContacts($token);
    abstract protected function getUserPosts($token);
    abstract protected function addPost($token, array $attributes);
    abstract protected function mapUserPostsToObject(array $posts);
    abstract protected function mapUserContactsToObject(array $user);

    abstract public function sendInvitation($contacts);

    protected $credentials;
    protected $httpClient, $signature, $storage, $provider_class;
    protected $className, $accessor;

    /**
     * Create a new provider instance.
     *
     * @param  array $config
     */
    public function __construct($config)
    {

        $this->credentials = new Credentials(
            $config['client_id'],
            $config['client_secret'],
            $config['redirect_url']
        );

        $class_name = '\AppBundle\Provider\Network\OAuth\OAuth1\Service\\' . $this->className;
        $this->signature = new Signature($this->credentials);
        $this->httpClient = new StreamClient();
        $this->storage = new Session();
        $this->provider_class = new $class_name($this->credentials, $this->httpClient, $this->storage, $this->signature, $baseApiUri = null);

        $this->request = Request::createFromGlobals();
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }


    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect()
    {
        $token = $this->provider_class->requestRequestToken();

        $url = $this->provider_class->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
        header('Location: ' . $url); die();
      // return new RedirectResponse($url);

    }

//    /**
//     * Get the User instance for the authenticated user.
//     *
//     * @throws \InvalidArgumentException
//     * @return \Laravel\Socialite\One\User
//     */
    public function user()
    {

        $token = $this->storage->retrieveAccessToken($this->className);

        // This was a callback request from tumblr, get the token
        $response = $this->provider_class->requestAccessToken(
            $this->request->query->get('oauth_token'),
            $this->request->query->get('oauth_verifier'),
            $token->getRequestTokenSecret()
        );


        $user = $this->mapUserToObject($this->getUserByToken(
            $response
        ));

        return $user;
    }


    public function userContacts()
    {
        $token = $this->storage->retrieveAccessToken($this->className);

        // This was a callback request from tumblr, get the token
        $response = $this->provider_class->requestAccessToken(
            $this->request->query->get('oauth_token'),
            $this->request->query->get('oauth_verifier'),
            $token->getRequestTokenSecret()
        );
        $contacts = $this->mapUserContactsToObject($this->getUserContacts(
            $response
        ));

        return $contacts;
    }

    public function userPosts()
    {
        $token = $this->storage->retrieveAccessToken($this->className);

        // This was a callback request from tumblr, get the token
        $response = $this->provider_class->requestAccessToken(
            $this->request->query->get('oauth_token'),
            $this->request->query->get('oauth_verifier'),
            $token->getRequestTokenSecret()
        );
        $posts = $this->mapUserPostsToObject($this->getUserPosts(
            $response
        ));

        return $posts;
    }

    public function addPublication(array $attributes)
    {
        $token = $this->storage->retrieveAccessToken($this->className);

        // This was a callback request from tumblr, get the token
        $response = $this->provider_class->requestAccessToken(
            $this->request->query->get('oauth_token'),
            $this->request->query->get('oauth_verifier'),
            $token->getRequestTokenSecret()
        );
        $post = $this->addPost($response, $attributes);

        return $post;
    }
//
//    /**
//     * Get the token credentials for the request.
//     *
//     * @return \AppBundle\Provider\Network\OAuth1\Client\Credentials\TokenCredentials
//     */
//    protected function getToken()
//    {
//        $session = new Session();
//        $temp = $session->get('oauth.temp');
//        //$temp = $this->request->session()->get('oauth.temp');
//
//        return $this->server->getTokenCredentials(
//            $temp, $this->request->get('oauth_token'), $this->request->get('oauth_verifier')
//        );
//    }
//
//    /**
//     * Determine if the request has the necessary OAuth verifier.
//     *
//     * @return bool
//     */
//    protected function hasNecessaryVerifier()
//    {
//        return $this->request->has('oauth_token') && $this->request->has('oauth_verifier');
//    }
//
//    /**
//     * Set the request instance.
//     *
//     * @param  Request $request
//     * @return $this
//     */
//    public function setRequest(Request $request)
//    {
//        $this->request = $request;
//
//        return $this;
//    }
}
