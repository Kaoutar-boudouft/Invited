<?php

namespace App\Repository;

use App\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Profile>
 *
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile[]    findAll()
 * @method Profile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    public function save(Profile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Profile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUserProfileById($profileId): float|int|array|string
    {
        $query = $this->createQueryBuilder('p')
            ->select()
            ->where('p.id = :idProfile')
            ->setParameters(['idProfile'=> $profileId ])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function UpdateProfile(Request $request,$profileId){
        $data = json_decode($request->getContent(), true);
        $query = $this->createQueryBuilder('p')
            ->update()
            ->set('p.firstName', ':firstName')
            ->set('p.lastName', ':lastName')
            ->set('p.dob', ':dob')
            ->set('p.email', ':email')
            ->set('p.phoneNumber', ':phoneNumber')
            ->where('p.id = :profileId')
            ->setParameter('firstName', $data['firstName'])
            ->setParameter('lastName', $data['lastName'])
            ->setParameter('dob', $data['dob'])
            ->setParameter('email', $data['email'])
            ->setParameter('phoneNumber', $data['phoneNumber'])
            ->setParameter('profileId', $profileId)
            ->getQuery();
        return $query->execute();
    }
    public function UpdateProfilePic(Request $request,$profileId){
        $data = json_decode($request->getContent(), true);
        $query = $this->createQueryBuilder('p')
            ->update()
            ->set('p.profile_picture', ':profile_picture')
            ->where('p.id = :profileId')
            ->setParameter('profile_picture', $data['profile_picture'])
            ->setParameter('profileId', $profileId)
            ->getQuery();
        return $query->execute();
    }


//    /**
//     * @return Profile[] Returns an array of Profile objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Profile
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
