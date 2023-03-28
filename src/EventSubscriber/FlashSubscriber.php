<?php

namespace App\EventSubscriber;

use App\Entity\ProductionTime;
use App\Event\Employee\EmployeeCreated;
use App\Event\Employee\EmployeeUpdated;
use App\Event\Job\JobCreated;
use App\Event\Job\JobUpdated;
use App\Event\ProductionTime\ProductionTimeCreated;
use App\Event\Project\ProjectCreated;
use App\Event\Project\ProjectUpdated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class FlashSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ){}

    public static function getSubscribedEvents() : array
    {
        return [
          EmployeeCreated::class => [
              ['onEmployeeCreated']
          ],
          EmployeeUpdated::class => [
              ['onEmployeeUpdated']
          ],
          JobCreated::class => [
              ['onJobCreated']
          ],
          JobUpdated::class => [
              ['onJobUpdated']
          ],
          ProjectCreated::class => [
              ['onProjectCreated']
          ],
          ProjectUpdated::class => [
              ['onProjectUpdated']
          ],
          ProductionTimeCreated::class => [
              ['onProductionTimeCreated']
          ]
        ];
    }

    public function onEmployeeCreated(EmployeeCreated $event): void
    {
        $this->addFlash('success', 'L\'employé a bien été créé');
    }

    public function onEmployeeUpdated(EmployeeUpdated $event): void
    {
        $this->addFlash('success', 'L\'employé a bien été modifié');
    }

    public function onJobCreated(JobCreated $event): void
    {
        $this->addFlash('success', 'Le métier a bien été créé');
    }

    public function onJobUpdated(JobUpdated $event): void
    {
        $this->addFlash('success', 'Le métier a bien été modifié');
    }

    public function onProjectCreated(ProjectCreated $event): void
    {
        $this->addFlash('success', 'Le projet a bien été créé');
    }

    public function onProjectUpdated(ProjectUpdated $event): void
    {
        $this->addFlash('success', 'Le projet a bien été modifié');
    }

    public function onProductionTimeCreated(ProductionTimeCreated $event): void
    {
        $this->addFlash('success', 'Le temps de production a bien été ajouté');
    }

    private function addFlash(string $type, string $message): void
    {
        $session =$this->requestStack->getSession();

        $session->getFlashBag()->add($type, $message);
    }
}
