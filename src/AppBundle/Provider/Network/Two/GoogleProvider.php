<?php
namespace AppBundle\Provider\Network\Two;


use AppBundle\MailHelper;

class GoogleProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        'openid',
        'profile',
        'email',
        'https://www.googleapis.com/auth/plus.me',
        'https://www.googleapis.com/auth/plus.stream.write',
//        'https://www.googleapis.com/auth/plus.login',
        'https://www.google.com/m8/feeds/'
    ];

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://accounts.google.com/o/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://accounts.google.com/o/oauth2/auth', $state);
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), ['grant_type' => 'authorization_code']);
//        return array_push (
//            parent::getTokenFields($code), 'grant_type', 'authorization_code'
//        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->curl_file_get_contents('https://www.googleapis.com/plus/v1/people/me?access_token=' . $token);

        return json_decode($response, true);
        //  $response = $this->getHttpClient('https://www.googleapis.com/plus/v1/people/me?access_token='.$token, 'GET');

//        $response = $this->getHttpClient()->get('https://www.googleapis.com/plus/v1/people/me?', [
//            'query' => [
//                'prettyPrint' => 'false',
//            ],
//            'headers' => [
//                'Accept' => 'application/json',
//                'Authorization' => 'Bearer ' . $token,
//            ],
//        ]);

        //       return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'id' => $this->accessor->getValue($user, '[id]'),
            'name' => $this->accessor->getValue($user, '[displayName]'),
            'fname' => $this->accessor->getValue($user, '[name][givenName]'),
            'lname' => $this->accessor->getValue($user, '[name][familyName]'),
            'email' => $this->accessor->getValue($user, '[emails][0][value]'),
            'avatar' => preg_replace('/^([^?]+)(\?.*?)?(#.*)?$/', '$1$3', $this->accessor->getValue($user, '[image][url]')),
            'location' => $this->accessor->getValue($user, '[placesLived][0][value]'),
            'gender' => $this->accessor->getValue($user, '[gender]'),
            'url' => $this->accessor->getValue($user, '[url]'),
        ];
//        return (new User)->setRaw($user)->map([
//            'id' => $user['id'],
//            'name' => array_get($user, 'nickname'),
//            'fname' => array_get($user, 'name.givenName'),
//            'lname' => array_get($user, 'name.familyName'),
//            'password' => '',
//            'email' => array_get($user, 'emails.0.value'),
//            'avatar' => preg_replace('/^([^?]+)(\?.*?)?(#.*)?$/', '$1$3', array_get($user, 'image.url')),
//            'location' => array_get($user, 'placesLived.0.value'),
//            'gender' => array_get($user, 'gender'),
//            'type' => '',
//            'url' => array_get($user, 'url'),
//        ]);
    }

    public function getUserContacts($token)
    {
        $response = file_get_contents('https://www.google.com/m8/feeds/contacts/default/full?alt=json&max-results=500&oauth_token=' . $token);

        return json_decode($response, true)['feed']['entry'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        $arr = [];
        foreach ($contacts as $contact) {
            if (isset($contact['gd$email'])) {
                $arr[] = [
                    'email' => $contact['gd$email'][0]['address'],
                    'name' => $contact['gd$email'][0]['address'],
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


    public function getUserPosts($token)
    {
        $response = file_get_contents('https://www.googleapis.com/plus/v1/people/me/activities/public?alt=json&max-results=500&oauth_token=' . $token);

        return json_decode($response, true)['items'];
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
            $arr[] = [
                'id' => $post['id'],
                'description' => $post['object']['content'],
                'image' => $post['object']['attachments'][0]['image']['url'],
            ];
        }

        return $arr;
    }

    protected function addPost($token, array $attributes){
/*
        $headers = array(
            'POST  /content/v1/MULTI_CLIENT_ACCOUNT_ID/managedaccounts HTTP/1.1',
            'Host: content.googleapis.com',
            'Content-Type: application/atom+xml',
            'Authorization: GoogleLogin auth='.$token
        );

        $requestXmlBody ="
<?xml version='1.0'?>
<entry xmlns='http://www.w3.org/2005/Atom'
    xmlns:sc='http://schemas.google.com/structuredcontent/2009'>
  <title type='text'>John</title>
  <content type='text'>John's Store</content>
  <link rel='alternate' type='text/html' href='http://www.mysite.com/members/john/'/>
  <sc:adult_content>no</sc:adult_content>
  <sc:internal_id>john</sc:internal_id>
  <sc:reviews_url>http://www.mysite.com/reviews/john/</sc:reviews_url>
</entry>";

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($connection, CURLOPT_POST, 1);
        curl_setopt($connection, CURLOPT_POSTFIELDS, $requestXmlBody);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($connection);
        dump($response); die();*/



        $userId = '114786184121376641845';

// create the URL for this user ID
      //  $url = sprintf('https://www.googleapis.com/plusDomains/v1/people/%s/activities?alt=json&oauth_token=' . $token, $userId);
        $url = sprintf('https://www.googleapis.com/plusDomains/v1/people/%s/activities', $userId);

// create your HTTP request object
       // $headers = ['content-type' => 'application/json'];
        $body = [
            "object" => [
                "originalContent" => "Were putting new coversheets on all the TPS reports before they go out now."
            ],
            "access" => [
                "items" => [
                    ["type" => "domain"]
                ],
                "domainRestricted" => true
            ]
        ];
        $header = ['Content-Type: application/json', "Authorization: Bearer " . $token];
        $response = $this->HttpClient($url, 'POST', $body, $header);

    }

    public function HttpClient($url, $method, $postFields = [], $header = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');


        curl_setopt($ch, CURLOPT_HTTPHEADER,  $header);

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
                break;
//            case 'DELETE':
//                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
//                break;
//            case 'PUT':
//                $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
//                break;
        }

        $contents = curl_exec($ch);
        dump($contents); die();
        curl_close($ch);
        return curl_exec($ch);
    }

        private function curl_file_get_contents($url)
    {
        $curl = curl_init();
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $contents = curl_exec($curl);
        curl_close($curl);

        return $contents;
    }

    /**
     * Make an HTTP request
     *
     * @param string $url
     * @param string $method
     * @param string $authorization
     * @param array $postfields
     *
     * @return string
     *
     */
    private function request($url, $method, $authorization, $postfields)
    {


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
                break;
//            case 'DELETE':
//                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
//                break;
//            case 'PUT':
//                $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
//                break;
        }


        $response = curl_exec($ch);
        $data = json_decode($response);

        return $data->access_token;

        /* Curl settings */
        $options = [
            // CURLOPT_VERBOSE => true,
            CURLOPT_CAINFO => __DIR__ . DIRECTORY_SEPARATOR . 'cacert.pem',
            CURLOPT_CONNECTTIMEOUT => $this->connectionTimeout,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json', $authorization, 'Expect:'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $this->userAgent,
        ];

        if ($this->gzipEncoding) {
            $options[CURLOPT_ENCODING] = 'gzip';
        }

        if (!empty($this->proxy)) {
            $options[CURLOPT_PROXY] = $this->proxy['CURLOPT_PROXY'];
            $options[CURLOPT_PROXYUSERPWD] = $this->proxy['CURLOPT_PROXYUSERPWD'];
            $options[CURLOPT_PROXYPORT] = $this->proxy['CURLOPT_PROXYPORT'];
            $options[CURLOPT_PROXYAUTH] = CURLAUTH_BASIC;
            $options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
        }

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = Util::buildHttpQuery($postfields);
                break;
            case 'DELETE':
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
            case 'PUT':
                $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
                break;
        }

        if (in_array($method, ['GET', 'PUT', 'DELETE']) && !empty($postfields)) {
            $options[CURLOPT_URL] .= '?' . Util::buildHttpQuery($postfields);
        }


        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $options);
        $response = curl_exec($curlHandle);

        // Throw exceptions on cURL errors.
        if (curl_errno($curlHandle) > 0) {
            throw new TwitterOAuthException(curl_error($curlHandle), curl_errno($curlHandle));
        }

        $this->response->setHttpCode(curl_getinfo($curlHandle, CURLINFO_HTTP_CODE));
        $parts = explode("\r\n\r\n", $response);
        $responseBody = array_pop($parts);
        $responseHeader = array_pop($parts);
        $this->response->setHeaders($this->parseHeaders($responseHeader));

        curl_close($curlHandle);

        return $responseBody;
    }


}
