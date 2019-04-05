<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\FOSGroup")
     * @ORM\JoinTable(name="user_fos_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $position;

    /**
     * @var string
     * @Assert\Length(min=3)
     * @ORM\Column(name="lastDiploma", type="string", length=255, nullable=true)
     */
    private $lastDiploma;

    /**
     * @var string
     * @Assert\Length(min=3)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="educationYear", type="string", length=255, nullable=true)
     */
    private $educationYear;

    /**
     * @var string
     * @Assert\Length(min=3)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;


    /**
     * @var string
     * @Assert\Url()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var string
     * @Assert\Image(
     *     mimeTypes = {"image/jpg", "image/jpeg", "image/gif", "image/png"}
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;


    /**
     * @ORM\Column(type="string", columnDefinition="enum('away', 'busy', 'invisible', 'online' )")
     */
    private $status = 'online';

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Company", mappedBy="user", cascade={"persist"})
     */
    private $companies;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Network", mappedBy="user", cascade={"persist"})
     */
    private $connections;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notification", mappedBy="user", cascade={"persist"})
     */
    private $notifications;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Network", mappedBy="fromUser", cascade={"persist"})
     */
    private $fromConnections;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Message", mappedBy="user", cascade={"persist"})
     */
    private $messages;
    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Message", mappedBy="from", cascade={"persist"})
     */
    private $messagesFrom;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user", cascade={"persist"})
     */
    private $posts;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user", cascade={"persist"})
     */
    private $comments;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Review", mappedBy="user", cascade={"persist"})
     */
    private $reviews;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tradeland", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="users_tradelands",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tradeland_id", referencedColumnName="id")}
     *      )
     */
    private $tradelands;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Tradeland", mappedBy="owner", cascade={"persist"})
     */
    private $ownTradelands;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Article", mappedBy="user", cascade={"persist"})
     */
    private $articles;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Vote", mappedBy="user", cascade={"persist"})
     */
    private $votes;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Provider", mappedBy="user", cascade={"persist"})
     */
    private $providers;

    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="user", cascade={"persist"})
     */
    private $media;

    public function __construct()
    {
        parent::__construct();

        $this->companies = new ArrayCollection();
        $this->connections = new ArrayCollection();
        $this->fromConnections = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tradelands = new ArrayCollection();
        $this->ownTradelands = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->media = new ArrayCollection();
    }

    /**
     * Set position
     *
     * @param string $position
     *
     * @return User
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set lastDiploma
     *
     * @param string $lastDiploma
     *
     * @return User
     */
    public function setLastDiploma($lastDiploma)
    {
        $this->lastDiploma = $lastDiploma;

        return $this;
    }

    /**
     * Get lastDiploma
     *
     * @return string
     */
    public function getLastDiploma()
    {
        return $this->lastDiploma;
    }

    /**
     * Set organization
     *
     * @param string $organization
     *
     * @return User
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set educationYear
     *
     * @param string $educationYear
     *
     * @return User
     */
    public function setEducationYear($educationYear)
    {
        $this->educationYear = $educationYear;

        return $this;
    }

    /**
     * Get educationYear
     *
     * @return string
     */
    public function getEducationYear()
    {
        return $this->educationYear;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return User
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return User
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }


    /**
     * Add company
     *
     * @param \AppBundle\Entity\Company $company
     *
     * @return User
     */
    public function addCompany(\AppBundle\Entity\Company $company)
    {
        $this->companies[] = $company;

        return $this;
    }

    /**
     * Remove company
     *
     * @param \AppBundle\Entity\Company $company
     */
    public function removeCompany(\AppBundle\Entity\Company $company)
    {
        $this->companies->removeElement($company);
    }

    /**
     * Get companies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Add connection
     *
     * @param \AppBundle\Entity\Network $connection
     *
     * @return User
     */
    public function addConnection(\AppBundle\Entity\Network $connection)
    {
        $this->connections[] = $connection;

        return $this;
    }

    /**
     * Remove connection
     *
     * @param \AppBundle\Entity\Network $connection
     */
    public function removeConnection(\AppBundle\Entity\Network $connection)
    {
        $this->connections->removeElement($connection);
    }

    /**
     * Get connections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Add notification
     *
     * @param \AppBundle\Entity\Notification $notification
     *
     * @return User
     */
    public function addNotification(\AppBundle\Entity\Notification $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param \AppBundle\Entity\Notification $notification
     */
    public function removeNotification(\AppBundle\Entity\Notification $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Add fromConnection
     *
     * @param \AppBundle\Entity\Network $fromConnection
     *
     * @return User
     */
    public function addFromConnection(\AppBundle\Entity\Network $fromConnection)
    {
        $this->fromConnections[] = $fromConnection;

        return $this;
    }

    /**
     * Remove fromConnection
     *
     * @param \AppBundle\Entity\Network $fromConnection
     */
    public function removeFromConnection(\AppBundle\Entity\Network $fromConnection)
    {
        $this->fromConnections->removeElement($fromConnection);
    }

    /**
     * Get fromConnections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFromConnections()
    {
        return $this->fromConnections;
    }

    /**
     * Add post
     *
     * @param \AppBundle\Entity\Post $post
     *
     * @return User
     */
    public function addPost(\AppBundle\Entity\Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \AppBundle\Entity\Post $post
     */
    public function removePost(\AppBundle\Entity\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add review
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return User
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \AppBundle\Entity\Review $review
     */
    public function removeReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews->removeElement($review);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Add tradeland
     *
     * @param \AppBundle\Entity\Tradeland $tradeland
     *
     * @return User
     */
    public function addTradeland(\AppBundle\Entity\Tradeland $tradeland)
    {
        $this->tradelands[] = $tradeland;

        return $this;
    }

    /**
     * Remove tradeland
     *
     * @param \AppBundle\Entity\Tradeland $tradeland
     */
    public function removeTradeland(\AppBundle\Entity\Tradeland $tradeland)
    {
        $this->tradelands->removeElement($tradeland);
    }

    /**
     * Get tradelands
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTradelands()
    {
        return $this->tradelands;
    }

    /**
     * Add ownTradeland
     *
     * @param \AppBundle\Entity\Tradeland $ownTradeland
     *
     * @return User
     */
    public function addOwnTradeland(\AppBundle\Entity\Tradeland $ownTradeland)
    {
        $this->ownTradelands[] = $ownTradeland;

        return $this;
    }

    /**
     * Remove ownTradeland
     *
     * @param \AppBundle\Entity\Tradeland $ownTradeland
     */
    public function removeOwnTradeland(\AppBundle\Entity\Tradeland $ownTradeland)
    {
        $this->ownTradelands->removeElement($ownTradeland);
    }

    /**
     * Get ownTradelands
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnTradelands()
    {
        return $this->ownTradelands;
    }

    /**
     * Add article
     *
     * @param \AppBundle\Entity\Article $article
     *
     * @return User
     */
    public function addArticle(\AppBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \AppBundle\Entity\Article $article
     */
    public function removeArticle(\AppBundle\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add vote
     *
     * @param \AppBundle\Entity\Vote $vote
     *
     * @return User
     */
    public function addVote(\AppBundle\Entity\Vote $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \AppBundle\Entity\Vote $vote
     */
    public function removeVote(\AppBundle\Entity\Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    public function getVoteCompany()
    {
        return array_map(
            function ($vote) {
                return $vote->getCompany();
            },
            $this->votes->toArray()
        );
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);
    }

    /**
     * Add message
     *
     * @param \AppBundle\Entity\Message $message
     *
     * @return User
     */
    public function addMessage(\AppBundle\Entity\Message $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \AppBundle\Entity\Message $message
     */
    public function removeMessage(\AppBundle\Entity\Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add messagesFrom
     *
     * @param \AppBundle\Entity\Message $messagesFrom
     *
     * @return User
     */
    public function addMessagesFrom(\AppBundle\Entity\Message $messagesFrom)
    {
        $this->messagesFrom[] = $messagesFrom;

        return $this;
    }

    /**
     * Remove messagesFrom
     *
     * @param \AppBundle\Entity\Message $messagesFrom
     */
    public function removeMessagesFrom(\AppBundle\Entity\Message $messagesFrom)
    {
        $this->messagesFrom->removeElement($messagesFrom);
    }

    /**
     * Get messagesFrom
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessagesFrom()
    {
        return $this->messagesFrom;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return User
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set about
     *
     * @param string $about
     *
     * @return User
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add provider
     *
     * @param \AppBundle\Entity\Provider $provider
     *
     * @return User
     */
    public function addProvider(\AppBundle\Entity\Provider $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * Remove provider
     *
     * @param \AppBundle\Entity\Provider $provider
     */
    public function removeProvider(\AppBundle\Entity\Provider $provider)
    {
        $this->providers->removeElement($provider);
    }

    /**
     * Get providers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Get none set providers
     *
     * @return array
     */
    public function getNoneSetProviders()
    {
        $providers = ['facebook', 'google', 'twitter', 'vkontakte', 'tumblr', 'instagram', 'flickr', 'foursquare', 'pinterest', 'imgur', 'yahoo', 'odnoklassniki'];
        foreach ($this->providers as $provider) {
            if(($key = array_search($provider->getName(), $providers)) !== false) {
                unset($providers[$key]);
            }
        }

        return $providers;
    }

    /**
     * Add medium
     *
     * @param \AppBundle\Entity\Media $medium
     *
     * @return User
     */
    public function addMedia(\AppBundle\Entity\Media $medium)
    {
        $this->media[] = $medium;

        return $this;
    }

    /**
     * Remove medium
     *
     * @param \AppBundle\Entity\Media $medium
     */
    public function removeMedia(\AppBundle\Entity\Media $medium)
    {
        $this->media->removeElement($medium);
    }

    /**
     * Get media
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedia()
    {
        return $this->media;
    }
}
