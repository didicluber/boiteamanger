<?php

namespace AppBundle\Repository;
use AppBundle\AppBundle;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * PlatRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlatRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByArrondissement($arr){
        $conn = $this->getEntityManager();
        $qb = $conn->createQueryBuilder();

// this returns an array

        $plats = $qb->select(array('p'))
            ->from('AppBundle:Plat', 'p')
            ->join('AppBundle:Reservation', 'r')
            ->where('p.id = r.plat')
            ->andWhere('r.cp = :arr')
            ->setParameter('arr', $arr)
            ->orderBy('p.creeA', 'ASC')
            ->getQuery()
            ->getResult();
    return $plats;
    }

    public function findByPagine($numPage, $nbMaxParPage, $userId){
        $conn = $this->getEntityManager();
        $qb = $conn->createQueryBuilder();

        if (!is_numeric($numPage)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $page est incorrecte (valeur : ' . $numPage . ').'
            );
        }

        if ($numPage < 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }

        if (!is_numeric($nbMaxParPage)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $nbMaxParPage est incorrecte (valeur : ' . $nbMaxParPage . ').'
            );
        }

        $qb = $this->createQueryBuilder('p')
            ->where('p.user_id = :userId')
            ->setParameter('p.userId', $userId)
            ->orderBy('p.creeA', 'DESC');

        $query = $qb->getQuery();

        $premierResultat = ($numPage - 1) * $nbMaxParPage;
        $query->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
        $paginator = new Paginator($query);

        if ( ($paginator->count() <= $premierResultat) && $numPage != 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
        }

        return $paginator;
    }



    public function findByPage($page = 1, $max = 10, $userId)
    {
        $dql = $this->createQueryBuilder('plat')
        ->where(':userId = plat.userPoste')
        ->setParameter('userId', $userId);
        $dql->orderBy('plat.creeA', 'DESC');

        $firstResult = ($page - 1) * $max;

        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);

        $paginator = new Paginator($query);

        if(($paginator->count() <=  $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }

    public function findByTopFour($max = 10, $userId)
    {
        $dql = $this->createQueryBuilder('plat')
        ->where(':userId = plat.userPoste')
        ->setParameter('userId', $userId);
        $dql->orderBy('plat.creeA', 'DESC');


        $query = $dql->getQuery();
        $query->setMaxResults($max);



        return $query;
    }




}
