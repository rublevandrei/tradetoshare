<?php
namespace AppBundle\Security\Core\User;

use AppBundle\Entity\Provider;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Session\Session;

class FOSUBUserProvider extends BaseClass
{
    protected $em;
    protected $tokenStorage;
    public function __construct(UserManager $userManager, Array $properties, EntityManager $em, TokenStorage $tokenStorage, $rootDir)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->rootDir = $rootDir;
        parent::__construct($userManager, $properties);
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $provider_id = $response->getUsername();
        $service = $response->getResourceOwner()->getName();

        if (null !== $provider = $this->em->getRepository('AppBundle:Provider')->findOneBy(['user' => $user->getId(), 'name' => $service, 'provider_id' => $provider_id])) {
            $provider->setAccessToken($response->getAccessToken());
        } else {
            $provider = new Provider();
            $provider->setUser($user);
            $provider->setName($service);
            $provider->setProviderId($provider_id);
            $provider->setAccessToken($response->getAccessToken());
        }

        $this->em->persist($provider);
        $this->em->flush();
        header('Location: /user/connect'); die();
        return new RedirectResponse('/user/connect');
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {    
        $provider_id = $response->getUsername();
        $service = ($response->getResourceOwner()->getName() == 'imgur2') ? 'imgur' : $response->getResourceOwner()->getName();
        
        if($this->tokenStorage->getToken()){
        	$session = new Session(); 
        	$instagramProviderData = [
			'name' => $service,
			'provider_id' => $provider_id,
			'token' => $response->getAccessToken()
        	];
        	$session->set('instagram-provider-data', $instagramProviderData);
		$instagramInfo = $response->getResourceOwner()->getUserInformation(array(
	            'access_token' => $response->getAccessToken(),
	        ));
	        $instagramProfileData = [
			'realName' => $instagramInfo->getRealName(),
			'profilePicture' => $instagramInfo->getProfilePicture(),
        	];        	
		$session->set('instagram-profile-data', $instagramProfileData);		
        	//$user = $this->tokenStorage->getToken()->getUser();
        	// instagram have one redirect for not uath and for auth
        	// dublicate coonect funtionality for instagram                	
	
	        /*if (null !== $provider = $this->em->getRepository('AppBundle:Provider')->findOneBy(['user' => $user->getId(), 'name' => $service, 'provider_id' => $provider_id])) {
	            $provider->setAccessToken($response->getAccessToken());
	        } else {
	            $provider = new Provider();
	            $provider->setUser($user);
	            $provider->setName($service);
	            $provider->setProviderId($provider_id);
	            $provider->setAccessToken($response->getAccessToken());
	        }
	
	        $this->em->persist($provider);
	        $this->em->flush();*/
	        header('Location: /user/instagram-question'); die;	        	        	
        	
        } else {
        	$provider = $this->em->getRepository('AppBundle:Provider')->findOneBy(['name' => $service, 'provider_id' => $provider_id]);
	
	        if (is_null($provider)) {
	
	            $email = !is_null($response->getEmail()) ? $response->getEmail() : $provider_id . '@' . $service . '.com';
	            $user = $this->userManager->findUserBy(['email' => $email]);
	
		    $userWasNull = false;
	            if (is_null($user)) {
	                $user = $this->userManager->createUser();
	                $userWasNull = true;
	            }
			
		    if($response->getResourceOwner()->getName() == 'vkontakte'){
		    	$name = $response->getFirstName() . ' ' . $response->getLastName();
		    } else {
	            	$name = $response->getNickname() ?: $response->getFirstName() . ' ' . $response->getLastName();		    
		    }		
	
	            $user->setEmail($email);
	            $user->setName($name);
	            $user->setPassword($provider_id);
	            $user->setEnabled(true);
	            $this->userManager->updateUser($user);
	
		    if($userWasNull){		    

			if (!is_null($response->getProfilePicture())) {	
	                    $url = $response->getProfilePicture();
	                    $name = 'ava'.$user->getId().'.jpg';
	                    file_put_contents($this->rootDir."/../web/bundles/framework/images/user/$name", file_get_contents($url));
	                    $user->setAvatar($name);
	                }
		    }

	            $provider = new Provider();
	            $provider->setUser($user);
	            $provider->setName($service);
	            $provider->setProviderId($provider_id);
	            $provider->setAccessToken($response->getAccessToken());
	        } else {
	            $user = $provider->getUser();
	            $provider->setAccessToken($response->getAccessToken());
	        }
	
	        $this->em->persist($provider);
	        $this->em->flush();
	
	        return $user;
        }
        
        
    }

}