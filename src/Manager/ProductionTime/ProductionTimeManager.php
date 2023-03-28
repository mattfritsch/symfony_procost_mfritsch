<?php

namespace App\Manager\ProductionTime;

use App\Entity\Employee;
use App\Entity\ProductionTime;
use App\Event\ProductionTime\ProductionTimeCreated;
use App\Repository\ProductionTimeRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

final class ProductionTimeManager
{
    public function __construct(
        private ProductionTimeRepository $productionTimeRepository,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }

    public function save(ProductionTime $productionTime, Employee $employee): void
    {
        $this->productionTimeRepository->save($productionTime, $employee, true);
        $this->eventDispatcher->dispatch(new ProductionTimeCreated($productionTime));
    }
}