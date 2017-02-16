<?php

namespace Sioblog\ApiBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Sioblog\StaticBundle\Service\PathManager;

/**
 * Serialization listener for User model.
 */
class UserSerializationListener implements EventSubscriberInterface {

    private $logger;
    private $authorizationChecker;

    /**
     * Default constructor.
     */
    public function __construct($logger, $authorizationChecker) {
        $this->logger = $logger;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @inheritdoc
     */
    static public function getSubscribedEvents() {
        return array(
            array('event' => 'serializer.post_serialize', 'class' => 'Sioblog\CoreBundle\Entity\User', 'method' => 'onPostSerialize'),
            array('event' => 'serializer.pre_serialize', 'class' => 'Sioblog\CoreBundle\Entity\User', 'method' => 'onPreSerialize'),
        );
    }

    /**
     * Pre serialization
     */
    public function onPreSerialize(ObjectEvent $serializeEvent) {
    }

    /**
     * Post serialization
     */
    public function onPostSerialize(ObjectEvent $serializeEvent) {
    }

}
