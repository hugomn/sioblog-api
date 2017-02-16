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
     * @Groups({"article_full", "user_full"})
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

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="user", cascade={"persist"})
     */
    protected $articles;

    /** @ORM\Column(type="string", length=255) */
    protected $algorithm = 'sha512';

    public function getEncoderName() {
        return $this->algorithm == 'sha1' ? 'legacy' : 'default';
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return User
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set algorithm
     *
     * @param string $algorithm
     *
     * @return User
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * Get algorithm
     *
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * Add article
     *
     * @param \Sioblog\CoreBundle\Entity\Article $article
     *
     * @return User
     */
    public function addArticle(\Sioblog\CoreBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \Sioblog\CoreBundle\Entity\Article $article
     */
    public function removeArticle(\Sioblog\CoreBundle\Entity\Article $article)
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
}
