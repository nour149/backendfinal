<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TaskController.php',
        ]);
    }

    #[Route('/tasks', name: 'tasks', methods: ['GET'])]
    public function getTaskList(TaskRepository $taskRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $taskList = $taskRepository->findAll();

            // Extract only the necessary information from each Task entity
            $formattedTaskList = [];
            foreach ($taskList as $task) {
                $formattedTaskList[] = [
                    'name' => $task->getName(),
                    'description' => $task->getDescription(),
                    'status' => $task->getStatus(),
                    'date' => $task->getDate()?->format('Y-m-d H:i:s'),
                    'start' => $task->getStart()?->format('Y-m-d H:i:s'),
                    'endf' => $task->getEndf()?->format('Y-m-d H:i:s'),
                    'duration' => $task->getDuration()?->format('Y-m-d H:i:s'),
                    'allTheDay' => $task->getAllTheDay(),
                    'priority' => $task->getPriority(),
                    'project' => $task->getproject() ? $task->getProject()->getId() : null,
                    'user' => $task->getUser() ? $task->getUser()->getId() : null,
                    'section' => $task->getSection() ? $task->getSection()->getId() : null,
                    'tier' => $task->getTier() ? $task->getTier()->getId() : null,
                ];
            }

            return new JsonResponse($formattedTaskList);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        }

        #[Route('/api/tasks/{id}', name: 'detailTask', methods: ['GET'])]
         public function getDetailTask(int $id, SerializerInterface $serializer, TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->find($id);
        if ($task) {
            $jsonTask = $serializer->serialize($task, 'json');
            return new JsonResponse($jsonTask, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/tasks', name: 'createTask', methods: ['POST'])]
    public function createTask(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Create a new Task entity
        $task = $serializer->deserialize($request->getContent(), Task::class, 'json');

        // Check if the 'comment' field is provided in the JSON payload
        if (isset($data['comment'])) {
            // If the 'comment' field is already set, append the new message
            if ($task->getComment() !== null) {
                $task->setComment($task->getComment() . ' | New task added');
            } else {
                // If the 'comment' field is not set, set it to the new message
                $task->setComment('New task added');
            }
        }

        // Persist the task to the database
        $em->persist($task);
        $em->flush();

        $jsonTask = $serializer->serialize($task, 'json', ['groups' => 'getTasks']);
        $taskArray = json_decode($jsonTask, true);

        $location = $urlGenerator->generate('detailTask', ['id' => $task->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse([
            'message' => 'Task created successfully',
            'task' => $taskArray,
            'location' => $location
        ], Response::HTTP_CREATED, ['Location' => $location]);
    }
    #[Route('/api/tasks/{id}', name: 'updateTask', methods: ['PUT'])]
    public function updateTask(Request $request, SerializerInterface $serializer, Task $currentTask, EntityManagerInterface $em): JsonResponse
    {
        $serializer->deserialize(
            $request->getContent(),
            Task::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentTask,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['subtasks'],
            ]
        );

        $content = $request->toArray();



        $em->persist($currentTask);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }


#[Route('/api/last_comment', name: 'last_comment', methods: ['GET'])]
public function getLastComment(TaskRepository $taskRepository): JsonResponse
{
    try {
        // Retrieve the last task added to the database
        $lastTask = $taskRepository->findOneBy([], ['id' => 'DESC']);

        if ($lastTask) {
            $lastComment = $lastTask->getComment();

            // Return the last comment as JSON response
            return new JsonResponse(['last_comment' => $lastComment]);
        } else {
            // If no task found, return an empty response or an appropriate message
            return new JsonResponse(['message' => 'No tasks found'], JsonResponse::HTTP_NOT_FOUND);
        }
    } catch (\Exception $e) {
        // Handle any exceptions
        return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
}