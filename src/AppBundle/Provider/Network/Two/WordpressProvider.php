<?php
namespace AppBundle\Provider\Network\Two;

class WordpressProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://public-api.wordpress.com/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://public-api.wordpress.com/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://public-api.wordpress.com/rest/v1.1/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map(
            [
                'id' => $user['ID'],
                'nickname' => $user['username'],
                'name' => $user['display_name'],
                'email' => array_get($user, 'email'),
                'avatar' => $user['avatar_URL'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    protected function getUserContacts($token)
    {
        $response = file_get_contents($this->graphUrl . '/' . $this->version . '/me/friends?access_token=' . $token);
        dump($response);
        die();
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