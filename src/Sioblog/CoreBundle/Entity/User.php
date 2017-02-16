<?php

namespace Sioblog\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Model\UserInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use Gedmo\Mapping\Annotation as Gedmo;
use Sioblog\CoreBundle\Helper\ConnectionHelper;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\AdvancedEncoderBundle\Security\Encoder\EncoderAwareInterface;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Sioblog\CoreBundle\Repository\UserRepository")
 * @ExclusionPolicy("ALL")
 */
class User extends BaseUser implements UserInterface, EncoderAwareInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     * @Groups({"user_full"})
     */
    protected $id;

    /**
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Expose
     * @Groups({"user_full"})
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /** @ORM\Column(type="string", length=255) */
    protected $algorithm = 'sha512';

    public function getEncoderName() {
        return $this->algorithm == 'sha1' ? 'legacy' : 'default';
    }
}
