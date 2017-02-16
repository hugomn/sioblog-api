<?php

namespace Sioblog\CoreBundle\Tests\Fixtures\Entity;

use Sioblog\CoreBundle\Entity\User;
use Sioblog\CoreBundle\Entity\UserStatus;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Authentication fixture.
 *
 * @author Hugo Magalhães <hugomn@gmail.com>
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * Loads the object
     * @param  ObjectManager $manager Manager
     */
    public function load(ObjectManager $manager) {

        $user = new User();
        $user->setCreated(new \DateTime());
        $user->setUpdated(new \DateTime());
        $user->setAlgorithm("sha1");
        $user->setUsername("hugomn");
        $user->setUsernameCanonical("hugomn");
        $user->setEmail("hugomn@gmail.com");
        $user->setEmailCanonical("hugomn@gmail.com");
        $user->setEnabled(1);
        $user->setPassword("cf9304361684cbc005c56a5f55c93af6481bdd52");
        $user->setLocked(0);
        $user->setExpired(0);
        $user->setRoles(unserialize('a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}'));
        $manager->persist($user);

        // Set references
        $this->addReference('user', $user);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }
}
