<?php

namespace Sioblog\ApiBundle\Tests\Fixtures\Entity;

use Sioblog\CoreBundle\Entity\City;
use Sioblog\CoreBundle\Entity\Country;
use Sioblog\CoreBundle\Entity\Event;
use Sioblog\CoreBundle\Entity\EventCategory;
use Sioblog\CoreBundle\Entity\EventStatus;
use Sioblog\CoreBundle\Entity\State;
use Sioblog\CoreBundle\Entity\Tax;
use Sioblog\CoreBundle\Entity\TicketBatch;
use Sioblog\CoreBundle\Entity\TicketBatchStatus;
use Sioblog\CoreBundle\Entity\User;
use Sioblog\CoreBundle\Entity\Venue;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Article fixture.
 *
 * @author Hugo Magalhães <hugomn@gmail.com>
 */
class LoadArticleData extends AbstractFixture implements OrderedFixtureInterface {

    static public $articles;

    /**
     * Loads the object
     * @param  ObjectManager $manager Manager
     */
    public function load(ObjectManager $manager) {
        // Reset events array
        self::$articles = array();


        // Flush
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 2; // the order in which fixtures will be loaded
    }
}
