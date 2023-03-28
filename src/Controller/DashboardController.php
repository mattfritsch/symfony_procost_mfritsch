<?php

namespace App\Controller;

use App\Repository\EmployeeRepository;
use App\Repository\ProductionTimeRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private EmployeeRepository $employeeRepository,
        private ProductionTimeRepository $productionTimeRepository
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $deliveredProjects = $this->projectRepository->getDeliveredProject();

        $numberOfProjectNotDelivered = $this->projectRepository->countNotDeliveredProject();
        $numberOfProjectDelivered = count($deliveredProjects);


        $numberOfEmployee = $this->employeeRepository->countEmployee();
        $totalProductionTime = $this->productionTimeRepository->countProductionTime();

        $lastProjects = $this->projectRepository->getFiveLastProjects();
        $lastProductionsTimes = $this->productionTimeRepository->getTenLastProductionTime();

        $bestEmployee = $this->productionTimeRepository->bestEmployee();

        $ratioDeliver = $numberOfProjectDelivered/$numberOfProjectNotDelivered * 100;
        $deliveredData = [$ratioDeliver, 100-$ratioDeliver ];

        $rentabilityData = [$this->calculateRentability($deliveredProjects), 100 - $this->calculateRentability($deliveredProjects)];

        return $this->render('index.html.twig', [
            'numberOfEmployee' => $numberOfEmployee,
            'numberOfProjectDelivered' => $numberOfProjectDelivered,
            'numberOfProjectNotDelivered' => $numberOfProjectNotDelivered,
            'totalProductionTime' => $totalProductionTime,
            'lastProjects' => $lastProjects,
            'lastProductionsTimes' => $lastProductionsTimes,
            'deliveredData' => $deliveredData,
            'bestEmployee' => $bestEmployee,
            'rentabilityData' => $rentabilityData
        ]);
    }

    private function calculateRentability($projects): float|int
    {
        $nbProject = 0;
        $nbProjectRentability = 0;
        foreach ($projects as &$value) {
            if ($value['total'] === null) {
                $value['total'] = 0;
            } else {
                $value['total'] = intval($value['total']);
            }
            $nbProject++;
            if ($value['project']->getSellingPrice() < $value['total']) {
                $nbProjectRentability++;
            }
        }
        if ($nbProject === 0) {
            return 0;
        }
        return ((($nbProject - $nbProjectRentability) / $nbProject) * 100);
    }
}