<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Job;
use App\Entity\ProductionTime;
use App\Entity\Project;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $manager;
    private array $employees = [];
    private array $projects = [];

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadJobs();

        $this->loadEmployees();

        $this->loadProjects();

        $this->loadProductionTime();

        $manager->flush();
    }

    /**
     * @param float $min
     * @param float $max
     * @param int $digit
     * @return float|int
     */
    public function randomDecimal(float $min, float $max, int $digit = 1): float|int
    {
        return mt_rand($min * 10, $max * 10) / pow(10, $digit);
    }


    private function loadJobs(): void
    {
        $jobs = [
            ['Web developer'],
            ['SEO manager'],
            ['Lead developer'],
            ['Web designer'],
            ['Dev Ops'],
            ['Database administrator'],
            ['Network administrator'],
            ['Web analyst'],
            ['Database architect'],
            ['Project manager'],
            ['Software developer'],
            ['Community manager'],
            ['Development engineer'],
            ['Technical support engineer'],
        ];

        foreach($jobs as $key => [$name]){
            $job = new Job();
            $job->setName($name);
            $this->manager->persist($job);
            $this->addReference(Job::class . $key, $job);
        }

    }

    /**
     * @throws \Exception
     */
    private function loadEmployees(): void
    {
        $firstNames = [
            'Matthieu',
            'Henry',
            'Emma',
            'Arthur',
            'Adrien',
            'Lucie',
            'Nicolas',
            'Chloé',
            'Jean',
            'Elisabeth',
            'Charles',
            'John',
            'Manon'
        ];

        $lastNames = [
            'Fritsch',
            'Ruan',
            'Bordelais',
            'Attardo',
            'Galan',
            'Wunderlich',
            'Becker',
            'Destremont',
            'Doe',
            'Martin',
            'Michel',
            'Florentin',
            'Belloche'
        ];

        for($i = 1; $i < 50; $i++){
            $employee = new Employee();

            $firstName = array_rand($firstNames);
            $lastName = array_rand($lastNames);

            $employee->setFirstName($firstNames[$firstName]);
            $employee->setLastName($lastNames[$lastName]);
            $employee->setEmail($firstNames[$firstName] . '.' . $lastNames[$lastName] . '@procost.com');

            /** @var Job $job */
            $job = $this->getReference(Job::class . random_int(0, 13));
            $employee->setJob($job);
            $employee->setDailyCost($this->randomDecimal(300.00, 800.00));
            $employee->setHiringDate(new DateTimeImmutable());

            $this->manager->persist($employee);
            array_push($this->employees, $employee);
        }
    }

    private function loadProjects() : void
    {
        for($i = 1; $i < 50; $i++){
            $project = new Project();
            $project->setName('project' . $i);
            $project->setDescription('Voici la description de project' . $i);
            $project->setSellingPrice($this->randomDecimal(15000.00, 150000.00));
            $project->setCreatedAt(new DateTimeImmutable());
            $project->setDeliveryDate(null);

            $this->manager->persist($project);
            array_push($this->projects, $project);
        }
    }

    /**
     * @throws \Exception
     */
    private function loadProductionTime(): void
    {
        for($i = 1; $i < 75; $i++){
            $productionTime = new ProductionTime();

            $employee = rand(count($this->employees) - count($this->employees), count($this->employees) - 1);
            $project = rand(count($this->projects) - count($this->projects), count($this->projects) - 1);

            $productionTime->setProductionTime(random_int(15,60));
            $productionTime->setEmployee($this->employees[$employee]);
            $productionTime->setProject($this->projects[$project]);
            $productionTime->setEntryDate(new DateTimeImmutable());

            $this->manager->persist($productionTime);
        }
    }

}
