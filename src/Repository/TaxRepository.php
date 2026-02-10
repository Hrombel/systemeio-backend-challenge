<?php namespace App\Repository;

use App\Entity\Tax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tax>
 */
class TaxRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Tax::class);
    }

    public function getTaxRules(): array {
        $taxFQN = Tax::class;

        $rules = $this->getEntityManager()->createQuery(
            "SELECT t.rule
            FROM $taxFQN t
        ")
            ->getSingleColumnResult()
        ;

        return $rules;
    }
}
