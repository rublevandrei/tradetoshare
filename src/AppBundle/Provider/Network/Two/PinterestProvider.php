<?php
namespace AppBundle\Provider\Network\Two;

class PinterestProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['read_public', 'read_relationships', 'write_public', 'write_relationships'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://api.pinterest.com/oauth/',
            $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.pinterest.com/v1/oauth/token';
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

        $response = file_get_contents('https://api.pinterest.com/v1/me/', false, $context);

        return json_decode($response, true)['data'];
//        $response = $this->getHttpClient()->get(
//            'https://api.pinterest.com/v1/me/',
//            [
//                'headers' => [
//                    'Authorization' => 'Bearer '.$token,
//                ],
//            ]
//        );

        //       return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
//        preg_match('#https://www.pinterest.com/(.+?)/#', $this->accessor->getValue($user, '[url]'), $matches);
//        $nickname = $matches[0];

        return [
            'id' => $this->accessor->getValue($user, '[id]'),
            'name' => $this->accessor->getValue($user, '[first_name]') . ' ' . $this->accessor->getValue($user, '[last_name]'),
            'fname' => null,
            'lname' => null,
            'email' => null,
            'avatar' => null,
            'location' => null,
            'gender' => null,
            'url' => $this->accessor->getValue($user, '[url]'),
        ];
//        return (new User())->setRaw($user)->map(
//            [
//                'id' => $user['data']['id'],
//                'nickname' => $nickname,
//                'name' => $user['data']['first_name'].' '.$user['data']['last_name'],
//            ]
//        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(
            parent::getTokenFields($code),
            [
                'grant_type' => 'authorization_code',
            ]
        );
    }

    protected function getUserContacts($token)
    {
        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer " . $token,
            ]
        ]);

        $response = file_get_contents('https://api.pinterest.com/v1/me/followers/', false, $context);

        return json_decode($response, true)['data'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        $arr = [];
        foreach ($contacts as $contact) {
            $arr[] = [
                'id' => $this->accessor->getValue($contact, '[id]'),
                'name' => $this->accessor->getValue($contact, '[first_name]') . ' ' . $this->accessor->getValue($contact, '[last_name]'),
                'url' => $this->accessor->getValue($contact, '[url]'),
                'avatar' => null,
                'email' => null
            ];
        }

        return $arr;
    }

    public function sendInvitation($contacts)
    {
        return false;
    }

    public function getUserPosts($token)
    {
        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer " . $token,
            ]
        ]);

        $response = file_get_contents('https://api.pinterest.com/v1/me/pins/?fields=media,image,note&limit=10', false, $context);

        return json_decode($response, true)['data'];
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
            $arr[] = [
                'id' => $post['id'],
                'description' => $post['note'],
                'image' => $post['image']['original']['url'],
            ];
        }

        return $arr;
    }

    protected function addPost($token, array $attributes)
    {
//        $postdata =  [
//            'board' => 'tmdcharles/Origami',
//            'note' => 'texxsst',
//            'image_url' => 'https://pbs.twimg.com/media/C4KSOhmW8AEndeL.jpg',
//        ];
//
//        $response = $this->getHttpClient('https://api.pinterest.com/v1/pins/', 'POST', $postdata, ["Authorization: Bearer" . $token]);
//        dump($response); die();

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Authorization: Bearer " . $token,
                'content' => ['board' => 'tmdcharles/Origami','note' => 'uxury Cheap Fashion', 'image_url' => 'https://pbs.twimg.com/media/C4KSOhmW8AEndeL.jpg']
            ]
        ]);

        $response = file_get_contents('https://api.pinterest.com/v1/pins/', false, $context);
dump($response); die();

        return [];
    }


}