<?php

namespace Sioblog\ApiBundle\Tests\Fixtures\Entity;

use Sioblog\ApiBundle\Entity\AccessToken;
use Sioblog\ApiBundle\Entity\Client;
use Sioblog\CoreBundle\Entity\User;
use Sioblog\CoreBundle\Entity\UserAccountStatus;
use Sioblog\CoreBundle\Entity\UserStatus;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Authentication fixture.
 *
 * @author Hugo Magalhães <hugomn@gmail.com>
 */
class LoadAuthData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    static public $accessToken;
    static public $client;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Loads the object
     * @param  ObjectManager $manager Manager
     */
    public function load(ObjectManager $manager) {

        // Creates user
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setFirstname("Hugo");
        $user->setLastname("Magalhães");
        $user->setLocale("pt_BR");
        $user->setTimezone("America/Sao_Paulo");
        $user->setCreated(new \DateTime());
        $user->setUpdated(new \DateTime());
        $user->setAlgorithm("sha1");
        $user->setUsername("hugomn");
        $user->setUsernameCanonical("hugomn");
        $user->setEmail("hugomn@gmail.com");
        $user->setEmailCanonical("hugomn@gmail.com");
        $user->setPlainPassword('abc123');
        $user->setEnabled(true);
        $user->setLocked(false);
        $user->setExpired(false);
        $user->setRoles(unserialize('a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}'));
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $userManager->updateUser($user, true);
        $manager->persist($user);

        // Creates Api Client
        $client = new Client();
        $client->setRandomId("2e7h7e439rmsg0wk4sgo0kgo8o4088cscsso484k04swk0swoc");
        $client->setSecret("5fwm23zxtns4ogc0gwgskgo804cgowg8oggkk8cs48cskows4s");
        $client->setRedirectUris(unserialize('a:1:{i:0;s:21:"http://sioblog.com/";}'));
        $client->setAllowedGrantTypes(unserialize('a:6:{i:0;s:8:"password";i:1;s:13:"refresh_token";i:2;s:18:"client_credentials";i:3;s:38:"http://sioblog.local/grants/facebook";i:4;s:5:"token";i:5;s:18:"authorization_code";}'));
        $manager->persist($client);

        // Creates Api Access Token
        $accessToken = new AccessToken();
        $accessToken->setClient($client);
        $accessToken->setUser($user);
        $accessToken->setToken("ZGM3Nzk5NTk4ZDA1NDBjNzA5ZjZmMzZkNzAwY2RhY2I3NWFhNmVjYjA4NzZmYzk4ZTkwZjliOWQ2MjMxNzNmOA");
        $accessToken->setExpiresAt(strtotime('tomorrow'));
        $accessToken->setScope("user");
        $manager->persist($accessToken);

        // Flushes data
        $manager->flush();

        // Set references
        $this->addReference('user', $user);

        // Saves acess token statically
        self::$accessToken = $accessToken;
        self::$client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }
}
