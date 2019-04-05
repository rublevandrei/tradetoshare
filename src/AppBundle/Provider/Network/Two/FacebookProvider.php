<?php

namespace AppBundle\Provider\Network\Two;


class FacebookProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The base Facebook Graph URL.
     *
     * @var string
     */
    protected $graphUrl = 'https://graph.facebook.com';

    /**
     * The Graph API version for the request.
     *
     * @var string
     */
    protected $version = 'v2.6';

    /**
     * The user fields being requested.
     *
     * @var array
     */
    protected $fields = ['name', 'email', 'gender', 'verified'];

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['email', 'user_friends', 'user_posts'];

    /**
     * Display the dialog in a popup view.
     *
     * @var bool
     */
    protected $popup = false;

    /**
     * Re-request a declined permission.
     *
     * @var bool
     */
    protected $reRequest = false;

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.facebook.com/' . $this->version . '/dialog/oauth', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->graphUrl . '/oauth/access_token';
    }

    /**
     * Get the access token response for the given code.
     *
     * @param  string $code
     * @return array
     */
    public function getAccessTokenResponse($code)
    {
        $string = json_decode($this->getHttpClient($this->getTokenUrl(), 'POST', $this->getTokenFields($code)));
        $access_token = $string->access_token;

        return $access_token;
    }
//    /**
//     * {@inheritdoc}
//     */
//    public function getAccessTokenResponse($code)
//    {
//        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
//
//        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
//            $postKey => $this->getTokenFields($code),
//        ]);
//
//        $data = [];
//
//        parse_str($response->getBody(), $data);
//
//        return Arr::add($data, 'expires_in', Arr::pull($data, 'expires'));
//    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $meUrl = $this->graphUrl . '/' . $this->version . '/me?access_token=' . $token . '&fields=' . implode(',', $this->fields);

        if (!empty($this->clientSecret)) {
            $appSecretProof = hash_hmac('sha256', $token, $this->clientSecret);

            $meUrl .= '&appsecret_proof=' . $appSecretProof;
        }

        $response = file_get_contents($meUrl);

        return json_decode($response, true);
//        $response = $this->getHttpClient()->get($meUrl, [
//            'headers' => [
//                'Accept' => 'application/json',
//            ],
//        ]);

        //       return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        $id = $this->accessor->getValue($user, '[id]');
        $avatarUrl = $this->graphUrl . '/' . $this->version . '/' . $id . '/picture';

        return [
            'id' => $id,
            'name' => $this->accessor->getValue($user, '[name]'),
            'fname' => null,
            'lname' => null,
            'email' => $this->accessor->getValue($user, '[email]'),
            'avatar' => $avatarUrl . '?type=normal',
            'location' => null,
            'gender' => $this->accessor->getValue($user, '[gender]'),
            'url' => null,
            'avatar_original' => $avatarUrl . '?width=1920',
        ];
//        return (new User)->setRaw($user)->map([
//            'id' => $user['id'], 'nickname' => null, 'name' => isset($user['name']) ? $user['name'] : null,
//            'email' => isset($user['email']) ? $user['email'] : null, 'avatar' => $avatarUrl . '?type=normal',
//            'avatar_original' => $avatarUrl . '?width=1920',
//        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null)
    {
        $fields = parent::getCodeFields($state);

        if ($this->popup) {
            $fields['display'] = 'popup';
        }

        if ($this->reRequest) {
            $fields['auth_type'] = 'rerequest';
        }

        return $fields;
    }

    /**
     * Set the user fields to request from Facebook.
     *
     * @param  array $fields
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Set the dialog to be displayed as a popup.
     *
     * @return $this
     */
    public function asPopup()
    {
        $this->popup = true;

        return $this;
    }

    /**
     * Re-request permissions which were previously declined.
     *
     * @return $this
     */
    public function reRequest()
    {
        $this->reRequest = true;
    }

    protected function getUserContacts($token)
    {       header('Location: /tradeland/invite' ); die();
        $response = file_get_contents($this->graphUrl . '/' . $this->version . '/me/friends?access_token=' . $token);
        dump($response);
        die();
//        return json_decode($response, true)['summary']['total_count'];
        return [];
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
        $response = file_get_contents($this->graphUrl . '/' . $this->version . '/me/feed?fields=full_picture,message&access_token=' . $token);

        return json_decode($response, true)['data'];
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
            $arr[] = [
                'id' => $post['id'],
                'description' => isset($post['message']) ? $post['message'] : '',
                'image' => isset($post['full_picture']) ? $post['full_picture'] : '',
            ];
        }

        return $arr;
    }

    protected function addPost($token, array $attributes)
    {
        $postdata =
            [
                'message' => $attributes['text'],
                'link' => 'http://tradetoshare.com',
                'picture' => 'http://i.imgur.com/xVreWbu.jpg',
            ];

        return $this->getHttpClient($this->graphUrl . '/' . $this->version . '/me/feed?access_token=' . $token, 'POST', $postdata);
    }

}
