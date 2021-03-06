<?php

namespace Sioblog\CoreBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Sioblog\CoreBundle\Entity\EventRole;
use Sioblog\CoreBundle\Entity\CustomerStatus;
use Sioblog\CoreBundle\Entity\State;
use Sioblog\CoreBundle\Entity\TicketOrderStatus;
use Sioblog\CoreBundle\Entity\User;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository {

    /**
     * @param string $email
     * @param QueryBuilder $qb
     * @return integer
     */
    public function countDuplicates($email, QueryBuilder $qb = null) {
        $em = $this->getEntityManager();
        if (is_null($qb)) {
            $qb = $em->createQueryBuilder();
        }
        $qb->select('count(u.id)')
                ->from('SioblogCoreBundle:User', 'u')
                ->where($qb->expr()->like($qb->expr()->upper('u.email'), "'" . strtoupper($email) . "'"));
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

}
