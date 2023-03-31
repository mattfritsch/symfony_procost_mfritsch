<?php

namespace App\Controller;


use App\Entity\Job;
use App\Form\JobType;
use App\Manager\Job\JobManager;
use App\Repository\JobRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class JobController extends AbstractController
{
    public function __construct(
        private JobRepository $jobRepository,
        private JobManager $jobManager
    )
    {
    }

    #[Route('/job', name: 'app_job')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $jobs = $paginator->paginate(
          $this->jobRepository->paginationQuery(),
          $request->query->get('page', 1),
          10
        );

        return $this->render('job/job-list.html.twig', [
            'jobs' => $jobs
        ]);
    }

    #[Route('/job/add', name: 'job_add')]
    public function addJob(Request $request): Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->jobManager->save($job);
            return $this->redirectToRoute('job_add');
        }

        return $this->render('job/job-edition.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/job/edit/{id}', name: 'job_edit', requirements: ['id' => '\d+'])]
    public function editJob(int $id, Request $request): Response
    {
        $job = $this->jobRepository->findOneById($id);

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->jobManager->update($job);
            return $this->redirectToRoute('job_edit', ['id' => $job->getId()]);
        }

        return $this->render('job/job-edition.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/job/remove/{id}', name: 'job_remove', requirements: ['id' => '\d+'])]
    public function removeJob(int $id): Response
    {
        $this->jobRepository->remove($this->jobRepository->findOneById($id), true);

        return $this->redirectToRoute('app_job');
    }
}
