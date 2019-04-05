<?php

namespace AppBundle\Provider\Network\Two;
use AppBundle\MailHelper;

class HotmailProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected $scopes = ['wl.basic', 'wl.emails', 'wl.signin', 'wl.contacts_emails'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://login.live.com/oauth20_authorize.srf', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://login.live.com/oauth20_token.srf';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = file_get_contents('https://apis.live.net/v5.0/me?access_token=' . $token);

        return json_decode($response, true);
//        $response = $this->getHttpClient()->get(
//            'https://apis.live.net/v5.0/me?access_token='.$token
//        );
//
//        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'id' => $this->accessor->getValue($user, '[id]'),
            'name' => $this->accessor->getValue($user, '[name]'),
            'fname' => null,
            'lname' => null,
            'email' => $this->accessor->getValue($user, '[emails][account]'),
            'avatar' => null,
            'location' => null,
            'gender' => $this->accessor->getValue($user, '[gender]'),
            'url' => null,
            'avatar_original' => null,
        ];
//        return (new User())->setRaw($user)->map([
//            'id' => $user['id'], 'nickname' => null, 'name' => $user['name'],
//            'email' => $user['emails']['account'], 'avatar' => null,
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
        $response = file_get_contents('https://apis.live.net/v5.0/me/contacts?access_token=' . $token . '&limit=100');

        return json_decode($response, true)['data'];
    }

    protected function mapUserContactsToObject(array $contacts)
    {
        $arr = [];
        foreach ($contacts as $contact) {
            if ($contact['emails']['personal']) {
                $arr[] = [
                    // 'id' => $this->accessor->getValue($contact, '[id]'),
                    'email' => $this->accessor->getValue($contact, '[emails][personal]'),
                    'name' => $this->accessor->getValue($contact, '[name]'),
                    'avatar' => null
                ];
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
