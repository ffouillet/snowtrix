<?php
namespace Fx\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository{

    public function findOneByUsernameOrEmail($usernameOrEmail) {

        $qb = $this->createQueryBuilder('u' );
        $qb->select('u');
        $qb->where('u.username = :usernameOrEmail OR u.email = :usernameOrEmail');
        $qb->setParameter('usernameOrEmail', $usernameOrEmail);

        return $qb->getQuery()->getSingleResult();

    }

} 
