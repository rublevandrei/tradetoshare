<?php
namespace AppBundle\Provider\Network\Two;

class RedditProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['identity'];
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://ssl.reddit.com/api/v1/authorize', $state
        );
    }
    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://ssl.reddit.com/api/v1/access_token';
    }
    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://oauth.reddit.com/api/v1/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'User-Agent' => $this->getUserAgent(),
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'], 'nickname' => $user['name'],
            'name' => null, 'email' => null, 'avatar' => null,
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => $this->getUserAgent(),
            ],
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => $this->getTokenFields($code),
        ]);
        $this->credentialsResponseBody = json_decode($response->getBody(), true);
        return $this->credentialsResponseBody;
    }
    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return [
            'grant_type' => 'authorization_code', 'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
    }
    /**
     * {@inheritdoc}
     */
    protected function getUserAgent()
    {
        return implode(':', [
            $this->getConfig('platform'),
            $this->getConfig('app_id'),
            $this->getConfig('version_string'),
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['platform', 'app_id', 'version_string'];
    }

    protected function getUserContacts($token)
    {
        $response = file_get_contents($this->graphUrl . '/' . $this->version . '/me/friends?access_token=' . $token);
        dump($response); die();
        return json_decode($response, true)['summary']['total_count'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        dump($contacts);
        die();
        $arr = [];
        foreach ($contacts as $contact) {
            if (isset($contact['gd$email'])) {
                $arr[] = $contact['gd$email'][0]['address'];
            }
        }

        return $arr;
    }

    public function sendInvitation($contacts)
    {
        return false;
    }

    protected function getUserPosts($token){}
    public function mapUserPostsToObject(array $posts){}

    protected function addPost($token, array $attributes){}
}