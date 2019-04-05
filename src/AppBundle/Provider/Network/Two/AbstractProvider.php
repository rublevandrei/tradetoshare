<?php

namespace AppBundle\Provider\Network\Two;

use AppBundle\Provider\Network\Contracts\Provider as ProviderContract;

;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractProvider implements ProviderContract
{

    /**
     * The client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * The client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * The redirect URL.
     *
     * @var string
     */
    protected $redirectUrl;

    /**
     * The custom parameters to be sent with the request.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ',';

    /**
     * The type of the encoding in the query.
     *
     * @var int Can be either PHP_QUERY_RFC3986 or PHP_QUERY_RFC1738.
     */
    protected $encodingType = PHP_QUERY_RFC1738;

    /**
     * Indicates if the session state should be utilized.
     *
     * @var bool
     */
    protected $stateless = false;

    protected $request, $accessor;

    /**
     * Create a new provider instance.
     *
     * @param  array $config
     */
    public function __construct($config)
    {
        $this->clientId = $config['client_id'];
        $this->redirectUrl = $config['redirect_url'];
        $this->clientSecret = $config['client_secret'];

        $this->request = Request::createFromGlobals();
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string $state
     * @return string
     */
    abstract protected function getAuthUrl($state);

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    abstract protected function getTokenUrl();

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
    abstract protected function mapUserContactsToObject(array $user);
    abstract protected function mapUserPostsToObject(array $posts);

    abstract public function sendInvitation($contacts);

    /**
     * Redirect the user of the application to the provider's authentication screen.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect()
    {
        $state = null;

        if ($this->usesState()) {
            //   $this->request->session()->set('state', $state = $this->generateRandomString(40));
        }

        return new RedirectResponse($this->getAuthUrl($state));
    }

    /**
     * Get the random string.
     *
     * @param  int $length
     * @return string
     */
    protected function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string $url
     * @param  string $state
     * @return string
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        return $url . '?' . http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);
    }

    /**
     * Get the GET parameters for the code request.
     *
     * @param  string|null $state
     * @return array
     */
    protected function getCodeFields($state = null)
    {
        $fields = [
            'client_id' => $this->clientId, 'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'response_type' => 'code',
        ];

        if ($this->usesState()) {
            $fields['state'] = $state;
        }

        return array_merge($fields, $this->parameters);
    }

    /**
     * Format the given scopes.
     *
     * @param  array $scopes
     * @param  string $scopeSeparator
     * @return string
     */
    protected function formatScopes(array $scopes, $scopeSeparator)
    {
        return implode($scopeSeparator, $scopes);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if ($this->hasInvalidState()) {
//            dump('InvalidState');
//            die();
            // throw new InvalidStateException;
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->mapUserToObject($this->getUserByToken(
        //   $token = Arr::get($response, 'access_token')
            $token = $response
        ));

        return $user;

//        return $user->setToken($token)
//            ->setRefreshToken(Arr::get($response, 'refresh_token'))
//            ->setExpiresIn(Arr::get($response, 'expires_in'));
    }

    public function userContacts()
    {
        $response = $this->getAccessTokenResponse($this->getCode());

        $contacts = $this->mapUserContactsToObject($this->getUserContacts(
            $token = $response
        ));

        return $contacts;
    }


    public function userPosts()
    {
        $response = $this->getAccessTokenResponse($this->getCode());

        $posts = $this->mapUserPostsToObject($this->getUserPosts(
            $token = $response
        ));

        return $posts;
    }

    public function addPublication(array $attributes)
    {
        $response = $this->getAccessTokenResponse($this->getCode());

        $post = $this->addPost($response, $attributes);

        return $post;
    }
    /**
     * Get a Social User instance from a known access token.
     *
     * @param  string $token
    //  * @return \Laravel\Socialite\Two\User
     */
    public function userFromToken($token)
    {
        $user = $this->mapUserToObject($this->getUserByToken($token));

        return $user;
        //   return $user->setToken($token);
    }

    /**
     * Determine if the current request / session has a mismatching "state".
     *
     * @return bool
     */
    protected function hasInvalidState()
    {
        if ($this->isStateless()) {
            return false;
        }

        // $state = $this->request->session()->pull('state');

        $session = new Session();

        $state = $session->get('state');

        // return ! (strlen($state) > 0 && $this->request->input('state') === $state);

        return !(strlen($state) > 0 && $this->request->query->get('state') === $state);
    }

    /**
     * Get the access token response for the given code.
     *
     * @param  string $code
     * @return array
     */
    public function getAccessTokenResponse($code)
    {
        return json_decode($this->getHttpClient($this->getTokenUrl(), 'POST', $this->getTokenFields($code)))->access_token;
//        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
//
//        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
//            'headers' => ['Accept' => 'application/json'],
//            $postKey => $this->getTokenFields($code),
//        ]);
//
//        return json_decode($response->getBody(), true);
    }

    public function getHttpClient($url, $method, $postFields = [], $header = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
                $header, ['Content-Type: application/x-www-form-urlencoded']
        ));

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
                break;
//            case 'DELETE':
//                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
//                break;
//            case 'PUT':
//                $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
//                break;
        }

        //  $response = curl_exec($ch);

        return (curl_exec($ch));

        // return $data->access_token;
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return [
            'client_id' => $this->clientId, 'client_secret' => $this->clientSecret,
            'code' => $code, 'redirect_uri' => $this->redirectUrl,
        ];
    }

    /**
     * Get the code from the request.
     *
     * @return string
     */
    protected function getCode()
    {
        return $_REQUEST['code'] ?: null;
        //return $this->request->input('code');
    }

    /**
     * Set the scopes of the requested access.
     *
     * @param  array $scopes
     * @return $this
     */
    public function scopes(array $scopes)
    {
        $this->scopes = array_unique(array_merge($this->scopes, $scopes));

        return $this;
    }

    /**
     * Get the current scopes.
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Set the redirect URL.
     *
     * @param  string $url
     * @return $this
     */
    public function redirectUrl($url)
    {
        $this->redirectUrl = $url;

        return $this;
    }
//
//    /**
//     * Get a instance of the Guzzle HTTP client.
//     *
//     * @return \GuzzleHttp\Client
//     */
//    protected function getHttpClient()
//    {
//        if (is_null($this->httpClient)) {
//            $this->httpClient = new Client();
//        }
//
//        return $this->httpClient;
//    }
//
//    /**
//     * Set the Guzzle HTTP client instance.
//     *
//     * @param  \GuzzleHttp\Client  $client
//     * @return $this
//     */
//    public function setHttpClient(Client $client)
//    {
//        $this->httpClient = $client;
//
//        return $this;
//    }
//
//    /**
//     * Set the request instance.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @return $this
//     */
//    public function setRequest(Request $request)
//    {
//        $this->request = $request;
//
//        return $this;
//    }
//
    /**
     * Determine if the provider is operating with state.
     *
     * @return bool
     */
    protected function usesState()
    {
        return !$this->stateless;
    }

    /**
     * Determine if the provider is operating as stateless.
     *
     * @return bool
     */
    protected function isStateless()
    {
        return $this->stateless;
    }

    /**
     * Indicates that the provider should operate as stateless.
     *
     * @return $this
     */
    public function stateless()
    {
        $this->stateless = true;

        return $this;
    }
//
//    /**
//     * Set the custom parameters of the request.
//     *
//     * @param  array  $parameters
//     * @return $this
//     */
//    public function with(array $parameters)
//    {
//        $this->parameters = $parameters;
//
//        return $this;
//    }
}
