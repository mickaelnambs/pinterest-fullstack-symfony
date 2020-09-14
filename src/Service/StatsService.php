<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class StatsService.
 * 
 * @author Mickael Nambinintsoa <mickael.nambinintsoa07081999@gmail.com>
 */
class StatsService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * StatsService constructeur.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getStats()
    {
        $ads        = $this->getAdsCount();
        $categories = $this->getCategoriesCount();
        $users      = $this->getUsersCount();

        return compact('ads', 'categories', 'users');
    }

    public function getAdsCount()
    {
        return $this->entityManager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    public function getCategoriesCount()
    {
        return $this->entityManager->createQuery('SELECT COUNT(ca) FROM App\Entity\Category ca')->getSingleScalarResult();
    }

    public function getUsersCount()
    {
        return $this->entityManager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }
}