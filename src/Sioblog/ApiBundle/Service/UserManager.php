<?php

namespace Sioblog\ApiBundle\Service;

use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use Sioblog\ApiBundle\Helper\StringHelper;
use Sioblog\CoreBundle\Entity\User;

class UserManager extends FOSUserManager
{
    protected $em;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     * @param EntityManager           $em
     * @param User                    $class
     */
    public function __construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $fos_em, $class, $em)
    {
        $this->em = $em;
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $fos_em, $class);
    }

    /**
     * Creates all user relations and fill required parameters.
     *
     * @return User
     */
    public function processUser($user)
    {
        $user->setUsername(StringHelper::slugify($user->getFirstname().' '.$user->getLastname()));
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_USER'));
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
