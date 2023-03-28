<?php

namespace App\Manager\Project;

use App\Entity\Project;
use App\Event\Project\ProjectCreated;
use App\Event\Project\ProjectUpdated;
use App\Repository\ProjectRepository;
use Psr\EventDispatcher\EventDispatcherInterface;

final class ProjectManager
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }

    public function update(Project $project): void
    {
        $this->projectRepository->update();
        $this->eventDispatcher->dispatch(new ProjectUpdated($project));
    }

    public function save(Project $project): void
    {
        $this->projectRepository->save($project, true);
        $this->eventDispatcher->dispatch(new ProjectCreated($project));
    }
}