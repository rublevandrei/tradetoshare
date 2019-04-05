<?php
namespace AppBundle\Provider\Network\Two;

class ImgurProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://api.imgur.com/oauth2/authorize', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.imgur.com/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer " . $token,
            ]
        ]);

        $response = file_get_contents('https://api.imgur.com/3/account/me', false, $context);

        return json_decode($response, true)['data'];
//        $response = $this->getHttpClient()->get(
//            'https://api.imgur.com/3/account/me', [
//            'headers' => [
//                'Authorization' => 'Bearer '.$token,
//            ],
//        ]);
        //     return json_decode($response->getBody()->getContents(), true)['data'];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'id' => $this->accessor->getValue($user, '[id]'),
            'name' => $this->accessor->getValue($user, '[url]'),
            'fname' => null,
            'lname' => null,
            'email' => null,
            'avatar' => null,
            'location' => null,
            'gender' => null,
            'url' => null,
        ];
//        return (new User())->setRaw($user)->map([
//            'id' => $user['id'], 'nickname' => $user['url'], 'name' => null,
//            'email' => null, 'avatar' => null,
//        ]);
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
        return [];
        dump($response);
        die();
        return json_decode($response, true)['summary']['total_count'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        return [];
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

    protected function getUserPosts($token)
    {
        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer " . $token,
            ]
        ]);

        $response = file_get_contents('https://api.imgur.com/3/account/me/images/0.json?perPage=10', false, $context);

        return json_decode($response, true)['data'];
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
            $arr[] = [
                'id' => $post['id'],
                'description' => $post['description'],
                'image' => $post['link'],
            ];
        }

        return $arr;
    }

    protected function addPost($token, array $attributes)
    {
        $postdata = [
            'title' => $attributes['text'],
            'description' => $attributes['text'],
            'image' => 'https://pbs.twimg.com/media/C4KSOhmW8AEndeL.jpg',
        ];

        return $this->getHttpClient('https://api.imgur.com/3/image', 'POST', $postdata, ["Authorization: Bearer " . $token]);
    }


}