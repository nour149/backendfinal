<?php

namespace App\DataFixtures;

use App\Entity\Projet;
use App\Entity\Section;
use App\Entity\Task;
use App\Entity\Tier;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {  // Création d'un user "normal"
        $user = new User();
        $user->setEmail("nour@systeo.com");
        $user->setUsername("user@bookapi.com");

        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
      //  $user->setRole("ROLE_USER");
        $manager->persist($user);
        
    
        // Création d'un user "normal"
        $user = new User();
        $user->setEmail("user@bookapi.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);

        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("admin@bookapi.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);
        // Load Tier fixtures
        $tierFixtureData = [
            [
                'name' => 'Example Company',
                'tel' => '123456789',
                'email' => 'example@example.com',
                'rne' => 'ExampleRNE123',
                'created_at' => new \DateTime('2024-02-22T12:00:00', new \DateTimeZone('UTC')),
                'updated_at' => new \DateTime('2024-02-22T12:00:00', new \DateTimeZone('UTC')),
                'relation' => 'example_relation', // Add a non-null value for 'relation'
            ],
            [
                'name' => 'Another Company',
                'tel' => '987654321',
                'email' => 'another@example.com',
                'rne' => 'AnotherRNE456',
                'created_at' => new \DateTime('2024-02-23T08:30:00', new \DateTimeZone('UTC')),
                'updated_at' => new \DateTime('2024-02-23T08:30:00', new \DateTimeZone('UTC')),
                'relation' => 'another_relation', // Add a non-null value for 'relation'
            ],
        ];

        foreach ($tierFixtureData as $data) {
            try {
                $tier = new Tier();

                // Check if the required properties are set
                if (!isset($data['name'], $data['email'], $data['rne'])) {
                    // Handle the case where some required data is missing
                    continue;
                }

                $tier
                    ->setName($data['name']);

                // Check if 'tel' is set and not null before setting it
                if ($data['tel'] !== null) {
                    $tier->setTel($data['tel']);
                }
                // Check if 'email' is set and not null before setting it
                if (isset($data['email']) && $data['email'] !== null) {
                    $tier->setEmail($data['email']);
                }

                if (isset($data['rne']) && $data['rne'] !== null) {
                    $tier->setRne($data['rne']);
                }

                if (isset($data['created_at']) && $data['created_at'] !== null) {
                    $tier->setcreated_at($data['created_at']);
                }
                if (isset($data['updated_at']) && $data['updated_at'] !== null) {
                    $tier->setupdated_at($data['updated_at']);
                }

                if (isset($data['relation']) && $data['relation'] !== null) {
                    $tier->setRelation($data['relation']);
                }

                $manager->persist($tier);
            } catch (\Exception $e) {
                // Dump the data for the record causing the error
                var_dump($data);

                // Re-throw the exception to see the full stack trace
                throw $e;
            }
        }
        // Load Project fixtures
        $projectFixtureData = [
            [
                'name' => 'Project 1',
                'color' => '#FF0000',
                'archived' => false,
                'created_at' => new \DateTime('2024-02-24T12:00:00', new \DateTimeZone('UTC')),
                'updated_at' => new \DateTime('2024-02-24T13:00:00', new \DateTimeZone('UTC')),
            ],
            [
                'name' => 'Project 2',
                'color' => '#00FF00',
                'archived' => true,
                'created_at' => new \DateTime('2024-02-25T10:00:00', new \DateTimeZone('UTC')),
                'updated_at' => new \DateTime('2024-02-25T11:00:00', new \DateTimeZone('UTC')),
            ],
            // Add more projects as needed
        ];

        foreach ($projectFixtureData as $data) {
            try {
                $project = new Projet();

                // Check if the required properties are set
                if (!isset($data['name'], $data['color'])) {
                    // Handle the case where some required data is missing
                    continue;
                }

                $project
                    ->setName($data['name'])
                    ->setColor($data['color'])
                    ->setArchived($data['archived'])
                    ->setCreatedAt($data['created_at'] ?? null)
                    ->setUpdatedAt($data['updated_at'] ?? null);

                $manager->persist($project);
            } catch (\Exception $e) {
                // Handle exceptions as needed
            }
        }

        // Load Task fixtures
        $taskFixtureData = [
            [
                'name' => 'Task 1',
                'description' => 'Description for Task 1',
                'status' => 'Pending',
                'date' => new \DateTime('2024-03-01'),
                'start' => new \DateTime('2024-03-01 10:00:00'),
                'endf' => new \DateTime('2024-03-01 12:00:00'),
                'duration' => new \DateTime('2024-03-01 02:00:00'),
                'allTheDay' => 'No',
                'priority' => 'High',
            ],
            [
                'name' => 'Task 2',
                'description' => 'Description for Task 2',
                'status' => 'Completed',
                'date' => new \DateTime('2024-03-02'),
                'start' => new \DateTime('2024-03-02 14:00:00'),
                'endf' => new \DateTime('2024-03-02 16:00:00'),
                'duration' => new \DateTime('2024-03-02 02:00:00'),
                'allTheDay' => 'No',
                'priority' => 'Medium',
            ],
            // Add more tasks as needed
        ];

        foreach ($taskFixtureData as $data) {
            try {
                $task = new Task();

                // Check if the required properties are set
                if (!isset($data['name'])) {
                    // Handle the case where some required data is missing
                    continue;
                }

                $task
                    ->setName($data['name'])
                    ->setDescription($data['description'] ?? null)
                    ->setStatus($data['status'] ?? null)
                    ->setDate($data['date'] ?? null)
                    ->setStart($data['start'] ?? null)
                    ->setEndf($data['endf'] ?? null)
                    ->setDuration($data['duration'] ?? null)
                    ->setAllTheDay($data['allTheDay'] ?? null)
                    ->setPriority($data['priority'] ?? null);

                // Add more property assignments as needed

                $manager->persist($task);
            } catch (\Exception $e) {
                // Handle exceptions as needed
            }
        }

        // Load Section fixtures
        $sectionFixtureData = [
            [
                'name' => 'Section 1',
                'created_at' => new \DateTime('2024-02-22T12:00:00', new \DateTimeZone('UTC')),
                'updated_at' => new \DateTime('2024-02-22T12:00:00', new \DateTimeZone('UTC')),
            ],
            [
                'name' => 'Section 2',
                'created_at' => new \DateTime('2024-02-23T08:30:00', new \DateTimeZone('UTC')),
                'updated_at' => new \DateTime('2024-02-23T08:30:00', new \DateTimeZone('UTC')),
            ],
            // Add more sections as needed
        ];

        foreach ($sectionFixtureData as $data) {
            try {
                $section = new Section();

                // Check if the required properties are set
                if (!isset($data['name'])) {
                    // Handle the case where some required data is missing
                    continue;
                }

                $section
                    ->setName($data['name'])
                    ->setCreatedAt($data['created_at'] ?? null)
                    ->setUpdatedAt($data['updated_at'] ?? null);

                $manager->persist($section);
            } catch (\Exception $e) {
                // Handle exceptions as needed
            }
        }

        // Flush outside the loop
        $manager->flush();

        // ... Existing code for User fixtures





    }



}
