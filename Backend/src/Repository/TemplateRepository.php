<?php

namespace App\Repository;

use App\Entity\Template;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Template>
 *
 * @method Template|null find($id, $lockMode = null, $lockVersion = null)
 * @method Template|null findOneBy(array $criteria, array $orderBy = null)
 * @method Template[]    findAll()
 * @method Template[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class);
    }

    public function save(Template $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Template $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUserTemplates($user): float|int|array|string
    {
        $query = $this->createQueryBuilder('t')
            ->select(['t.id','t.html','t.css','t.title'])
            ->where('t.user = :id')
            ->setParameter('id', $user->getId())
            ->getQuery();
        return $query->getArrayResult();
    }

    public function getUserTemplateById($user,$tempId): float|int|array|string
    {
        $query = $this->createQueryBuilder('t')
            ->select(['t.id','t.title','t.html','t.css'])
            ->where('t.user = :idUser')
            ->andWhere('t.id = :id')
            ->setParameters(['idUser'=> $user->getId() ,'id' => $tempId ])
            ->getQuery();

         return $query->getArrayResult();
    }

    public function deleteUserTemplateById($user,$tempId){
        $query =  $this->createQueryBuilder('t')
            ->delete()
            ->where('t.user = :idUser')
            ->andWhere('t.id = :id')
            ->setParameters(['idUser'=> $user->getId() ,'id' => $tempId ])
            ->getQuery();
        return $query->getResult();
    }

    public function addNewTemplate(Request $request,$user): Template
    {
        $data = json_decode($request->getContent(), true);
        $template = new Template();
        $template->setUser($user)->setTitle($data['title']);
        $user->addTemplateId($template);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();

        return $template;
    }

    public function UpdateTemplate(Request $request,$tempId){
        $data = json_decode($request->getContent(), true);
        $query = $this->createQueryBuilder('t')
            ->update()
            ->set('t.title', ':title')
            ->set('t.html', ':html')
            ->set('t.css', ':css')
            ->where('t.id = :tempId')
            ->setParameter('title', $data['title'])
            ->setParameter('html', $data['html'])
            ->setParameter('css', $data['css'])
            ->setParameter('tempId', $tempId)
            ->getQuery();
        return $query->execute();
    }

//    /**
//     * @return Template[] Returns an array of Template objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Template
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
