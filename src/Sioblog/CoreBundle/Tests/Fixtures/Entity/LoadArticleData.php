<?php

namespace Sioblog\CoreBundle\Tests\Fixtures\Entity;

use Sioblog\CoreBundle\Entity\City;
use Sioblog\CoreBundle\Entity\Country;
use Sioblog\CoreBundle\Entity\Event;
use Sioblog\CoreBundle\Entity\EventCategory;
use Sioblog\CoreBundle\Entity\EventStatus;
use Sioblog\CoreBundle\Entity\State;
use Sioblog\CoreBundle\Entity\Tax;
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
class LoadEventData extends AbstractFixture implements OrderedFixtureInterface {

    static public $articles = array();

    /**
     * Loads the object
     * @param  ObjectManager $manager Manager
     */
    public function load(ObjectManager $manager) {


        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 2;
    }
}
