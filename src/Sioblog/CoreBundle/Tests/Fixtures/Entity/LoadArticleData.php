<?php

namespace Sioblog\CoreBundle\Tests\Fixtures\Entity;

use Sioblog\CoreBundle\Entity\Article;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Article fixture.
 *
 * @author Hugo Magalhães <hugomn@gmail.com>
 */
class LoadArticleData extends AbstractFixture implements OrderedFixtureInterface {

    static public $articles = array();

    /**
     * Loads the object
     * @param  ObjectManager $manager Manager
     */
    public function load(ObjectManager $manager) {
        $user = $this->getReference('user');
        $article = new Article();
        $article->setTitle("Test article");
        $article->setContent("Lorem ipsum dolor sit amet");
        $article->setUser($user);
        $article->setCreated(new \DateTime());
        $article->setUpdated(new \DateTime());
        $manager->persist($article);
        $manager->flush();
        self::$articles[] = $article;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 2;
    }
}
