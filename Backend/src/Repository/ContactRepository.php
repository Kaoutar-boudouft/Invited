<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function save(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByEmail($value): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findOneByPhoneNumber($value): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.phoneNumber = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getUserContacts($user): array|float|int|string
    {
        $data = $this->createQueryBuilder('c')
            ->select(['c.id','c.firstName','c.lastName','c.email','c.phoneNumber'])
            ->where('c.user = :id')
            ->setParameter('id', $user->getId())
            ->getQuery();

        return $data->getArrayResult();
    }

    public function getUserContactById($user,$contactId): array|float|int|string
    {
        $data = $this->createQueryBuilder('c')
            ->select(['c.id','c.firstName','c.lastName','c.email','c.phoneNumber'])
            ->where('c.user = :idUser')
            ->andWhere('c.id = :id')
            ->setParameters(['idUser'=> $user->getId() ,'id' => $contactId ])
            ->getQuery();

        return $data->getArrayResult();
    }

    public function deleteUserContactById($user,$contactId){
        $data = $this->createQueryBuilder('c')
            ->delete()
            ->where('c.user = :idUser')
            ->andWhere('c.id = :id')
            ->setParameters(['idUser'=> $user->getId() ,'id' => $contactId ])
            ->getQuery();

        return $data->getResult();
    }

    public function addNewContact(Request $request,$user){
        $data = json_decode($request->getContent(), true);
        $contact = new Contact();
        $contact->setUser($user)->setFirstName($data['first_name'])->setLastName($data['last_name'])
            ->setEmail($data['email'])->setPhoneNumber($data['phone_number']);
        $this->getEntityManager()->persist($contact);
        $this->getEntityManager()->flush();

        return $contact;
    }

    public function UpdateContact(Request $request,$contactId){
        $data = json_decode($request->getContent(), true);
        $query =$this->createQueryBuilder('c')
            ->update()
            ->set('c.firstName', ':first_name')
            ->set('c.lastName', ':last_name')
            ->set('c.email', ':email')
            ->set('c.phoneNumber', ':phone_number')
            ->where('c.id = :contactId')
            ->setParameter('first_name', $data['first_name'])
            ->setParameter('last_name', $data['last_name'])
            ->setParameter('email', $data['email'])
            ->setParameter('phone_number', $data['phone_number'])
            ->setParameter('contactId', $contactId)
            ->getQuery();

        return $query->execute();
    }


//    /**
//     * @return Contact[] Returns an array of Contact objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Contact
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
