<?php

namespace AppBundle\Provider\Network\Two;

//use Laravel\Socialite\Two\InvalidStateException;

class VkontakteProvider extends AbstractProvider implements ProviderInterface
{
    protected $fields = ['uid', 'first_name', 'last_name', 'screen_name', 'photo'];

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['email', 'wall', 'offline'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://oauth.vk.com/authorize', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://oauth.vk.com/access_token';
    }

    /**
     * Get the access token response for the given code.
     *
     * @param  string $code
     * @return array
     */
    public function getAccessTokenResponse($code)
    {
        return json_decode($this->getHttpClient($this->getTokenUrl(), 'POST', $this->getTokenFields($code)));
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $token = (array)$token;

        //  $lang = $this->getConfig('lang');
        $lang = 'en';
        $lang = $lang ? '&lang=' . $lang : '';
        $response = file_get_contents('https://api.vk.com/method/users.get?user_ids=' . $token['user_id'] . '&fields=' . implode(',', $this->fields) . $lang . '&https=1');

        return json_decode($response);
//        $response = $this->getHttpClient()->get(
//            'https://api.vk.com/method/users.get?user_ids='.$token['user_id'].'&fields='.implode(',', $this->fields).$lang.'&https=1'
//        );

//        $response = json_decode($response->getBody()->getContents(), true)['response'][0];
//        return array_merge($response, [
//            'email' => array_get($token, 'email'),
//        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'id' => $this->accessor->getValue($user, '[uid]'),
            'name' => $this->accessor->getValue($user, '[first_name]') . ' ' . $this->accessor->getValue($user, '[last_name]'),
            'fname' => $this->accessor->getValue($user, '[first_name]'),
            'lname' => $this->accessor->getValue($user, '[last_name]'),
            'email' => $this->accessor->getValue($user, '[email]'),
            'avatar' => $this->accessor->getValue($user, '[photo]'),
            'location' => null,
            'gender' => $this->accessor->getValue($user, '[gender]'),
            'url' => null,
            'avatar_original' => null,
        ];
//        return (new User())->setRaw($user)->map([
//            'id' => array_get($user, 'uid'), 'nickname' => array_get($user, 'screen_name'),
//            'name' => trim(array_get($user, 'first_name').' '.array_get($user, 'last_name')),
//            'email' => array_get($user, 'email'), 'avatar' => array_get($user, 'photo'),
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

    /**
     * {@inheritdoc}
     */
    protected function parseAccessToken($body)
    {
        return json_decode($body, true);
    }
//    /**
//     * {@inheritdoc}
//     */
//    public function user()
//    {
//        if ($this->hasInvalidState()) {
//            throw new InvalidStateException();
//        }
//        $user = $this->mapUserToObject($this->getUserByToken(
//            $token = $this->getAccessTokenResponse($this->getCode())
//        ));
//        return $user->setToken(array_get($token, 'access_token'));
//    }
    /**
     * Set the user fields to request from Vkontakte.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['lang'];
    }

    protected function getUserContacts($token)
    {
        $token = (array)$token;
        $response = file_get_contents('https://api.vk.com/method/friends.get?user_id=' . $token['user_id'] . '&fields=photo_200_orig,country');

        return json_decode($response, true)['response'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        $arr = [];
        foreach ($contacts as $contact) {
            $arr[] = [
                'id' => $this->accessor->getValue($contact, '[uid]'),
                'name' => $this->accessor->getValue($contact, '[first_name]') . ' ' . $this->accessor->getValue($contact, '[last_name]'),
                'avatar' => $this->accessor->getValue($contact, '[photo_200_orig]'),
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
        $token = (array)$token;
        $response = file_get_contents('https://api.vk.com/method/wall.get?count=10&owner_id=' . $token['user_id']);

        return json_decode($response, true)['response'];
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
            if (is_array($post)) {
                $attacment_type = isset($post['attachment']) ? $post['attachment']['type'] : null;
                $arr[] = [
                    'id' => $post['id'],
                    'description' => ($attacment_type === 'audio') ? $post['text'] . $post['attachment']['audio']['artist'] . ' ' . $post['attachment']['audio']['title'] : $post['text'],
                    'image' => ($attacment_type === 'photo') ? $post['attachment']['photo']['src_big'] :
                        (($attacment_type === 'video') ? $post['attachment']['video']['image_big'] : null)

                ];
            }
        }

        return $arr;
    }

    protected function addPost($token, array $attributes)
    {
        $token = (array)$token;
        $attributes = [
            'message' => 'content',
            'attachments' => 'https://pbs.twimg.com/media/C3_dNBKWcAAoK9U.jpg',
        ];

        $response = json_decode(file_get_contents('https://api.vk.com/method/wall.post?message=content&access_token=' . $token['access_token']));
//  $response = json_decode(file_get_contents('https://api.vk.com/method/wall.post?message=content&access_token=' . 'ca1bedb89804d69a577314fed6a3410c90d44d1b66592000327c2e75351a9154590064545da54696b1aff'));

        dump($response);
        die();
//        return json_decode($response, true)['summary']['total_count'];
        return [];
    }
}
