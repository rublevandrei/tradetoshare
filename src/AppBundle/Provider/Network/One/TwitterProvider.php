<?php
namespace AppBundle\Provider\Network\One;


class TwitterProvider extends AbstractProvider
{
    protected $className = 'Twitter';


    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->provider_class->request('account/verify_credentials.json');

        return json_decode($response, true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'id' => $this->accessor->getValue($user, '[id]'),
            'name' => $this->accessor->getValue($user, '[screen_name]'),
            'username' => $this->accessor->getValue($user, '[name]'),
            'location' => $this->accessor->getValue($user, '[location]'),
            'url' => $this->accessor->getValue($user, '[url]'),
            'avatar' => $this->accessor->getValue($user, '[profile_image_url_https]'),
        ];

    }

    public function getUserContacts($token)
    {
        $response = $this->provider_class->request('followers/list.json');

        return json_decode($response, true)['users'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        $arr = [];
        foreach ($contacts as $contact) {
            $arr[] = [
                'id' => $this->accessor->getValue($contact, '[id]'),
                'name' => $this->accessor->getValue($contact, '[name]'),
                'email' => null,
                'location' => $this->accessor->getValue($contact, '[location]'),
                'url' => $this->accessor->getValue($contact, '[url]'),
                'avatar' => $this->accessor->getValue($contact, '[profile_image_url_https]'),
            ];
        }

        return $arr;
    }

    //    public function sendInvitation($follower)
//    {
//        if ($_SESSION['access_token']) {
//            $access_token = $_SESSION['access_token'];
//
//            $connection = new TwitterOauth($this->clientId, $this->clientSecret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
//            $connection->post('direct_messages/new', ["screen_name" => $follower, "text" => 'connect me at TTS']);
//
//            return true;
//        }
//
//        return false;
//    }

    public function sendInvitation($contacts)
    {
        $body = [
            'text' => "connect me at https://tradetoshare.com",
            'skip_status' => true,

        ];
        foreach ($contacts as $contact) {
            $body['screen_name'] = $contact;
            $this->provider_class->request('direct_messages/new.json', 'POST', $body);
        }

        return true;
    }

    public function getUserPosts($token)
    {
        $response = $this->provider_class->request('statuses/user_timeline.json?count=10&include_rts=1');

        return json_decode($response, true);
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
            $arr[] = [
                'id' => $post['id'],
                'description' => $post['text'],
                'image' => isset($post['extended_entities']) ? $post['extended_entities']['media'][0]['media_url_https'] : null,
            ];
        }

        return $arr;
    }

    protected function addPost($token, array $attributes)
    {
        $body = [
            'status' => substr('http://tradetoshare.com ' . $attributes['text'], 0, 140),
            'display_coordinates' => true,
        ];

        if (!empty($attributes['image'])) {
            $file = file_get_contents($attributes['image']);
            $base = base64_encode($file);
            $parameters['media'] = $base;

            $body['media_ids'] = implode(',', [$this->uploadImage($parameters)]);
        }

        return $this->provider_class->request('statuses/update.json', 'POST', $body);
    }

    public function uploadImage($parameters)
    {
        $response = $this->provider_class->request('https://upload.twitter.com/1.1/media/upload.json', 'POST', $parameters, ['Accept: application/json']);

        return json_decode($response, true)['media_id_string'];
    }

}