<?php
namespace AppBundle\Provider\Network\Two;

class InstagramProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';
    /**
     * {@inheritdoc}
     */
    protected $scopes = ['basic', 'follower_list'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://api.instagram.com/oauth/authorize', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.instagram.com/oauth/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = file_get_contents('https://api.instagram.com/v1/users/self?access_token=' . $token);

        return json_decode($response, true)['data'];
//        $response = $this->getHttpClient()->get(
//            'https://api.instagram.com/v1/users/self?access_token='.$token, [
//            'headers' => [
//                'Accept' => 'application/json',
//            ],
//        ]);
        // return json_decode($response->getBody()->getContents(), true)['data'];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'id' => $this->accessor->getValue($user, '[id]'),
            'name' => $this->accessor->getValue($user, '[full_name]'),
            'fname' => null,
            'lname' => null,
            'email' => null,
            'avatar' => $this->accessor->getValue($user, '[profile_picture]'),
            'location' => null,
            'gender' => null,
            'url' => null,
            'avatar_original' => null,
        ];
//        return (new User())->setRaw($user)->map([
//            'id' => $user['id'], 'nickname' => $user['username'],
//            'name' => $user['full_name'], 'email' => null,
//            'avatar' => $user['profile_picture'],
//        ]);
    }
//    /**
//     * {@inheritdoc}
//     */
//    public function getAccessToken($code)
//    {
//        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
//            'form_params' => $this->getTokenFields($code),
//        ]);
//        $this->credentialsResponseBody = json_decode($response->getBody(), true);
//        return $this->parseAccessToken($response->getBody());
//    }
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
        $response = file_get_contents('https://api.instagram.com/v1/users/self/followed-by?access_token=' . $token);

        return json_decode($response, true)['data'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        $arr = [];
        foreach ($contacts as $contact) {
            $arr[] = [
                'id' => $this->accessor->getValue($contact, '[id]'),
                'username' => $this->accessor->getValue($contact, '[username]'),
                'name' => $this->accessor->getValue($contact, '[full_name]'),
                'avatar' => $this->accessor->getValue($contact, '[profile_picture]'),
                'email' => null
            ];
        }

        return $arr;
    }

    public function sendInvitation($contacts)
    {
        return false;
    }

    protected function getUserPosts($token)
    {
        $response = file_get_contents('https://api.instagram.com/v1/users/self/media/recent?count=10&access_token=' . $token);

        return json_decode($response, true)['data'];
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
            $arr[] = [
                'id' => $post['id'],
                'description' => null,
                'image' => $post['images']['standard_resolution']['url'],
            ];
        }

        return $arr;
    }

    protected function addPost($token, array $attributes){}
}