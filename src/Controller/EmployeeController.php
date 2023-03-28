<?php

namespace App\Controller;


use App\Entity\Employee;
use App\Entity\ProductionTime;
use App\Form\EmployeeType;
use App\Form\ProductionTimeType;
use App\Manager\Employee\EmployeeManager;
use App\Manager\ProductionTime\ProductionTimeManager;
use App\Repository\EmployeeRepository;
use App\Repository\ProductionTimeRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EmployeeController extends AbstractController
{
    public function __construct(
        private EmployeeManager $employeeManager,
        private EmployeeRepository $employeeRepository,
        private ProductionTimeManager $productionTimeManager,
        private ProductionTimeRepository $productionTimeRepository
    )
    {
    }

    #[Route('/employee', name: 'app_employee')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {

        $employees = $paginator->paginate(
            $this->employeeRepository->paginationQuery(),
            $request->query->get('page', 1),
            10
        );

        return $this->render('employee/employee-list.html.twig', [
            'employees' => $employees
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/employee/details/{id}', name: 'employee_show_details', requirements: ['id' => '\d+'])]
    public function showEmployee(int $id, Request $request, PaginatorInterface $paginator): Response
    {
        $productionTime = new ProductionTime();

        $employee = $this->employeeRepository->findOneById($id);

        $productionTimes = $paginator->paginate(
            $this->productionTimeRepository->findByEmployee($employee),
            $request->query->get('page', 1),
            10
        );

        $form = $this->createForm(ProductionTimeType::class, $productionTime);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->productionTimeManager->save($productionTime, $employee);
            return $this->redirectToRoute('employee_show_details', ['id' => $employee->getId()]);
        }

        return $this->render('employee/employee-details.html.twig', [
            'employee' => $employee,
            'form' => $form->createView(),
            'productionTimes' => $productionTimes
        ]);
    }

    #[Route('/employee/add', name: 'employee_add')]
    public function addEmployee(Request $request): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->employeeManager->save($employee);
            return $this->redirectToRoute('employee_add');
        }

        return $this->render('employee/employee-edition.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/employee/edit/{id}', name: 'employee_edit', requirements: ['id' => '\d+'])]
    public function editEmployee(int $id, Request $request): Response
    {
        $employee = $this->employeeRepository->findOneById($id);

        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->employeeManager->update($employee);
            return $this->redirectToRoute('employee_edit', ['id' => $employee->getId()]);
        }

        return $this->render('employee/employee-edition.html.twig', [
            'form' => $form
        ]);
    }
}