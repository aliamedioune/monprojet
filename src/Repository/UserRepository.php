<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

   /**
    * @return User[] Returns an array of User objects
    */
   public function findByAboutUs(): array
   {
       return $this->createQueryBuilder('u')
            ->select('u.aboutUs','count(u.aboutUs) as count')
            ->where('u.aboutUs != :val')
            ->setParameter('val', "")
            ->groupBy('u.aboutUs')
           ->getQuery()
           ->getResult()
       ;
   }

   /**
    * @return User[] Returns an array of User objects
    */
   public function findByCity(): array
   {
       return $this->createQueryBuilder('u')
            ->select('u.city','count(u.city) as count')
            ->where('u.city != :val')
            ->setParameter('val', "")
            ->groupBy('u.city')
            ->orderBy('count', 'ASC')
            ->setMaxResults(5)
           ->getQuery()
           ->getResult()
       ;
   }
   public function findStrictlyEmployees()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_EMPLOYE"%') // This assumes roles are stored as a JSON encoded string
            ->andWhere('u.roles NOT LIKE :adminRole')
            ->setParameter('adminRole', '%"ROLE_ADMIN"%')
            ->andWhere('u.roles NOT LIKE :userRole')
            ->setParameter('userRole', '%"ROLE_USER"%')
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
