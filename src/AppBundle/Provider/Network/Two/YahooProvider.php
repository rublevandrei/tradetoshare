<?php
namespace AppBundle\Provider\Network\Two;
use AppBundle\MailHelper;
class YahooProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * @var string
     */
    protected $xoauth_yahoo_guid;

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://api.login.yahoo.com/oauth2/request_auth', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.login.yahoo.com/oauth2/get_token';
    }

    public function getAccessTokenResponse($code)
    {
        return json_decode($this->getHttpClient($this->getTokenUrl(), 'POST', $this->getTokenFields($code)));
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer " . $token->access_token,
            ]
        ]);

        $response = file_get_contents('https://social.yahooapis.com/v1/user/' . $token->xoauth_yahoo_guid . '/profile?format=json', false, $context);

        return json_decode($response, true)['profile'];

//        $response = $this->getHttpClient()->get('https://social.yahooapis.com/v1/user/'.$this->xoauth_yahoo_guid.'/profile?format=json', [
//            'headers' => [
//                'Authorization' => 'Bearer '.$token,
//            ],
//        ]);
        //     return json_decode($response->getBody(), true)['profile'];
    }

    /**
     * Maps Yahoo object to User Object.
     *
     * Note: To have access to e-mail, you need to request "Profiles (Social Directory) - Read/Write Public and Private"
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'id' => $this->accessor->getValue($user, '[guid]'),
            'name' => $this->accessor->getValue($user, '[nickname]'),
            'fname' => null,
            'lname' => null,
            'email' => $this->accessor->getValue($user, '[emails][0][handle]'),
            'avatar' => $this->accessor->getValue($user, '[image][imageUrl]'),
            'location' => null,
            'gender' => $this->accessor->getValue($user, '[gender]'),
            'url' => null,
        ];
//        return (new User())->setRaw($user)->map([
//            'id' => $user['guid'],
//            'nickname' => $user['nickname'],
//            'name' => null,
//            'email' => array_get($user, 'emails.0.handle'),
//            'avatar' => array_get($user, 'image.imageUrl'),
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

    protected function parseAccessToken($body)
    {
        $this->xoauth_yahoo_guid = array_get($body, 'xoauth_yahoo_guid');
        return array_get($body, 'access_token');
    }

    protected function getUserContacts($token)
    {
        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer " . $token->access_token,
            ]
        ]);

        $response = file_get_contents('https://social.yahooapis.com/v1/user/' . $token->xoauth_yahoo_guid . '/contacts?format=json', false, $context);

        return json_decode($response, true)['contacts'];
//        $response = $this->getHttpClient()->get('https://social.yahooapis.com/v1/user/' . $token['xoauth_yahoo_guid'] . '/contacts?format=json', [
//            'headers' => [
//                'Authorization' => 'Bearer ' . $token['access_token'],
//            ],
//        ]);

        //       return json_decode($response->getBody(), true)['contacts'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        $arr = [];
        foreach ($contacts['contact'] as $id => $contact) {
            foreach ($contact['fields'] as $field) {
                if ($field['type'] == 'email') {
                    $arr[] = [
                        'email' => $field['value'],
                        'name' => $field['value'],
                        'avatar' => null
                    ];
                }
            }
        }

        return $arr;
    }

    public function sendInvitation($contacts)
    {
        $transport = \Swift_SmtpTransport::newInstance();
        $transport->setUsername('tts@dev.tradetoshare.com');
        $transport->setPassword('O95x26E44T29I86l');

        $helper = new MailHelper(new \Swift_Mailer($transport));
        foreach ($contacts as $contact) {
            $helper->sendEmail(
                'tts@example.com',
                $contact,
                "Hi, meet me on <a href='https://tradetoshare.com'>TTS</a> today to see what is  happening.",
                'Invitation'
            );
        }

        return true;
    }

    protected function getUserPosts($token){}
    public function mapUserPostsToObject(array $posts){}

    protected function addPost($token, array $attributes){}
}