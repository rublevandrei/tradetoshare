<?php
namespace AppBundle\Provider\Network\Two;

class YouTubeProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['https://www.googleapis.com/auth/youtube.readonly'];
    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://accounts.google.com/o/oauth2/auth', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://accounts.google.com/o/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://www.googleapis.com/youtube/v3/channels?part=snippet&mine=true', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true)['items'][0];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'], 'nickname' => $user['snippet']['title'],
            'name' => null, 'email' => null,
            'avatar' => $user['snippet']['thumbnails']['high']['url'],
        ]);
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