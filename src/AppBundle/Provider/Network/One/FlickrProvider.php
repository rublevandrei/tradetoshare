<?php
namespace AppBundle\Provider\Network\One;


class FlickrProvider extends AbstractProvider
{
    protected $className = 'Flickr';


    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->provider_class->requestPhp('flickr.test.login');

        return unserialize($response)['user'];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'id' => $this->accessor->getValue($user, '[id]'),
            'name' => $this->accessor->getValue($user, '[realname][_content]'),
            'username' => $this->accessor->getValue($user, '[username][_content]')
        ];

    }

    public function getUserContacts($token)
    {
        $response = $this->provider_class->requestPhp('flickr.contacts.getList');
        return unserialize($response)['contacts']['contact'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        $arr = [];
        foreach ($contacts as $contact) {
            $arr[] = [
                'id' => $this->accessor->getValue($contact, '[nsid]'),
                'username' => $this->accessor->getValue($contact, '[username]'),
                'name' => $this->accessor->getValue($contact, '[realname]'),
                'location' => $this->accessor->getValue($contact, '[location]'),
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
        $response = $this->provider_class->requestPhp('flickr.photos.getContactsPhotos', 'POST', ['count' => 2]);

        return unserialize($response)['photos']['photo'];
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
            $arr[] = [
                'id' => $post['id'],
                'description' => $post['title'],
                'image' => 'https://farm' . (int)$post['farm'] . '.staticflickr.com/' . $post['server'] . '/' . $post['id'] . '_' . $post['secret'] . '.jpg',
            ];
        }

        return $arr;
    }

    protected function addPost($token, array $attributes)
    {
         $args['photo'] = '@' . realpath(__DIR__.'/test.jpg');

        $response = $this->provider_class->request('https://up.flickr.com/services/upload/', 'POST', $args);
       dump($response);
       die();
        $this->token = $token;
        return $this->sync_upload(  __DIR__.'/test.jpg');
        dump(realpath('https://pbs.twimg.com/media/C4KSOhmW8AEndeL.jpg'));
        die();
    }

    function sync_upload($photo, $title = null, $description = null, $tags = null, $is_public = null, $is_friend = null, $is_family = null)
    {
        $params = array(
            'oauth_nonce' => '8dC0JzNDIaWvxvPb6U569D0WNiVoZa8W',
            'oauth_timestamp' => "1487117732",
            'oauth_consumer_key' => '24b5fc7ff2f96bb311fbe0c3fd374ead',
            'oauth_signature_method' => "HMAC-SHA1",
            'oauth_signature' => 'OcjMsUYBGRdGPiApV2%2B0Q3MVUss%3D',
            'oauth_version' => '1.0',
            'oauth_token' => '72157676691620710-60086cad4bb39a11');
        $test =["api_key" => '24b5fc7ff2f96bb311fbe0c3fd374ead', "title" => $title, "description" => $description, "tags" => $tags, "is_public" => $is_public, "is_friend" => $is_friend, "is_family" => $is_family,
         ];
        $args = array_merge($params, $test);
//        $args = array('auth_token'=> $this->token->getAccessToken(),"api_key" => '24b5fc7ff2f96bb311fbe0c3fd374ead', "title" => $title, "description" => $description, "tags" => $tags, "is_public" => $is_public, "is_friend" => $is_friend, "is_family" => $is_family);
        ksort($args);
        $auth_sig = "";

        foreach ($args as $key => $data) {
            if (is_null($data)) {
                unset($args[$key]);
            } else {
                $auth_sig .= $key . $data;
            }
        }

            $api_sig = md5('a4e2d96a78431b81' . $auth_sig);
            $args["api_sig"] = $api_sig;


        $photo = realpath($photo);

        $args['photo'] = '@' . $photo;
        $curl = curl_init('https://up.flickr.com/services/upload/');


        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        curl_close($curl);
        dump($response);
        die();

    }


}