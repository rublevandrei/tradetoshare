<?php
namespace AppBundle\Provider\Network\Two;

class FoursquareProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://foursquare.com/oauth2/authenticate', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://foursquare.com/oauth2/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = file_get_contents('https://api.foursquare.com/v2/users/self?oauth_token=' . $token . '&v=20150214');

        return json_decode($response, true)['response']['user'];

//        $response = $this->getHttpClient()->get(
//            'https://api.foursquare.com/v2/users/self?oauth_token='.$token.'&v=20150214'
//        );
//        return json_decode($response->getBody()->getContents(), true)['response']['user'];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'id' => $this->accessor->getValue($user, '[id]'),
            'name' => $this->accessor->getValue($user, '[firstName]') . ' ' . $this->accessor->getValue($user, '[lastName]'),
            'fname' => null,
            'lname' => null,
            'email' => $this->accessor->getValue($user, '[contact][email]'),
            'avatar' => $this->accessor->getValue($user, '[photo][prefix]') . $this->accessor->getValue($user, '[photo][suffix]'),
            'location' => $this->accessor->getValue($user, '[homeCity]'),
            'gender' => $this->accessor->getValue($user, '[gender]'),
            'url' => null,
            'avatar_original' => null,
        ];
//        return (new User())->setRaw($user)->map([
//            'id' => $user['id'], 'nickname' => null,
//            'name' => array_get($user, 'firstName') . ' ' . array_get($user, 'lastName'),
//            'email' => $user['contact']['email'],
//            'avatar' => $user['photo']['prefix'] . $user['photo']['suffix'],
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
        $response = file_get_contents('https://api.foursquare.com/v2/users/self/friends?oauth_token=' . $token . '&v=20150214');

        return json_decode($response, true)['response']['friends'];

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
        $details = json_decode(file_get_contents("http://ipinfo.io/{$_SERVER['REMOTE_ADDR']}"));

        $response = file_get_contents('https://api.foursquare.com/v2/venues/search?limit=10&ll='.$details->loc.'&oauth_token=' . $token . '&v=20150214');

        return json_decode($response, true)['response']['venues'];
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
            $address = '';
            if(isset($post['location']) and isset($post['location']['formattedAddress'])){
                foreach ($post['location']['formattedAddress'] as $item){
                    $address .= $item . ' ';
                }
                $address = trim($address);
            }

            $arr[] = [
                'id' => $post['id'],
                'description' => $post['name'] . "\n" . $address . "\n" . (isset($post['url']) ? $post['url'] : ''),
                'image' => null,
            ];
        }

        return $arr;
    }

    protected function addPost($token, array $attributes){}
}