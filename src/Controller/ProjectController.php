<?php

namespace App\Controller;


use App\Entity\Project;
use App\Form\ProjectType;
use App\Manager\ProductionTime\ProductionTimeManager;
use App\Manager\Project\ProjectManager;
use App\Repository\ProductionTimeRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProjectController extends AbstractController
{
    public function __construct(
        private ProjectManager $projectManager,
        private ProjectRepository $projectRepository,
        private ProductionTimeRepository $productionTimeRepository
    )
    {
    }

    #[Route('/project', name: 'app_project')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $projects = $paginator->paginate(
            $this->projectRepository->paginationQuery(),
            $request->query->get('page', 1),
            10
        );

        return $this->render('project/project-list.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/project/details/{id}', name: 'project_show_details', requirements: ['id' => '\d+'])]
    public function showProject(int $id, Request $request, PaginatorInterface $paginator): Response
    {
        $project = $this->projectRepository->findOneById($id);

        $productionTimes = $paginator->paginate(
            $this->productionTimeRepository->findByProject($project),
            $request->query->get('page', 1),
            10
        );

        $numberEmployee = $this->productionTimeRepository->getNumberEmployee($project);

        return $this->render('project/project-details.html.twig', [
            'project' => $project,
            'productionTimes' => $productionTimes,
            'numberEmployee' => $numberEmployee,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/project/deliver/{id}', name: 'project_deliver', requirements: ['id' => '\d+'])]
    public function deliverProject(int $id): Response
    {
        $project = $this->projectRepository->findOneById($id);
        $this->projectRepository->deliverProject($project);

        return $this->redirectToRoute('app_project');
    }

    #[Route('/project/add', name: 'project_add')]
    public function addProject(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->projectManager->save($project);
            return $this->redirectToRoute('project_add');
        }

        return $this->render('project/project-edition.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/project/edit/{id}', name: 'project_edit', requirements: ['id' => '\d+'])]
    public function editProject(int $id, Request $request): Response
    {
        $project = $this->projectRepository->findOneById($id);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->projectManager->update($project);
            return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
        }

        return $this->render('project/project-edition.html.twig', [
            'form' => $form
        ]);
    }
}
