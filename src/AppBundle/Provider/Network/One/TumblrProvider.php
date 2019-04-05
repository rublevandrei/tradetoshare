<?php
namespace AppBundle\Provider\Network\One;

class TumblrProvider extends AbstractProvider
{
    protected $className = 'Tumblr';

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->provider_class->request('user/info');

        return json_decode($response, true)['response']['user'];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return [
            'name' => $this->accessor->getValue($user, '[name]'),
        ];
    }

    public function getUserContacts($token)
    {
        $blog_id = $this->accessor->getValue($this->getUserByToken($token), '[name]');
        $response = $this->provider_class->request('blog/' . $blog_id . '/followers');

        return json_decode($response, true)['response']['users'];
    }

    public function mapUserContactsToObject(array $contacts)
    {
        $arr = [];
        foreach ($contacts as $contact) {
            $arr[] = [
                'name' => $this->accessor->getValue($contact, '[name]'),
                'link' => $this->accessor->getValue($contact, '[url]'),
                'email' => null,
                'avatar' => null
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
        $blog_id = $this->accessor->getValue($this->getUserByToken($token), '[name]');
        //$response = $this->provider_class->request('blog/' . $blog_id . '/posts/text?notes_info=true');
        $response = $this->provider_class->request('blog/' . $blog_id . '/posts/photo?limit=10');

        return json_decode($response, true)['response']['posts'];
    }

    public function mapUserPostsToObject(array $posts)
    {
        $arr = [];
        foreach ($posts as $post) {
                $arr[] = [
                    'id' => $post['id'],
                    'description' => $post['summary'],
                    'image' => $post['photos'][0]['original_size']['url'],
                ];
        }

        return $arr;
    }
    protected function addPost($token, array $attributes){
        $blog_id = $this->accessor->getValue($this->getUserByToken($token), '[name]');

        $body = [
            'type' => 'text', //text
            //  'caption' => 'dwdwqdwqdqw',
            'body' => 'http://tradetoshare.com '.$attributes['text'] . '<img src="https://pbs.twimg.com/media/C4KSOhmW8AEndeL.jpg">',
        ];
//        $body = [
//            'type' => 'photo', //text
//            'caption' => 'dwdwqdwqdqw',
//            'source' => 'https://pbs.twimg.com/media/C4KSOhmW8AEndeL.jpg',
//        ];
        return $this->provider_class->request('blog/' . $blog_id . '/post', 'POST', $body);
        dump($response); die();


    }
}
