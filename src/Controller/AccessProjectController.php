<?php

namespace App\Controller;

use App\Entity\AccessProject;
use App\Repository\AccessProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AccessProjectController extends AbstractController
{
    #[Route('/access/project', name: 'app_access_project')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AccessProjectController.php',
        ]);
    }


    #[Route('/access-projects', name: 'access_projects', methods: ['GET'])]
    public function getAccessProjectList(AccessProjectRepository $accessProjectRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $accessProjects = $accessProjectRepository->findAll();

            // Extract only the necessary information from each AccessProject entity
            $formattedAccessProjects = [];
            foreach ($accessProjects as $accessProject) {
                $formattedAccessProjects[] = [
                    'status' => $accessProject->getStatus(),
                    'role' => $accessProject->getRole(),
                    'created_at' => $accessProject->getCreatedAt()?->format('Y-m-d H:i:s'),
                    'updated_at' => $accessProject->getUpdatedAt()?->format('Y-m-d H:i:s'),
                    // Add more fields as needed
                ];
            }

            return new JsonResponse($formattedAccessProjects);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/access-projects/{id}', name: 'detailAccessProject', methods: ['GET'])]
    public function getDetailAccessProject(int $id, SerializerInterface $serializer, AccessProjectRepository $accessProjectRepository): JsonResponse
    {
        $accessProject = $accessProjectRepository->find($id);
        if ($accessProject) {
            $jsonAccessProject = $serializer->serialize($accessProject, 'json');
            return new JsonResponse($jsonAccessProject, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/access-projects', name: "createAccessProject", methods: ['POST'])]
    public function createAccessProject(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $accessProject = $serializer->deserialize($request->getContent(), AccessProject::class, 'json');
        $em->persist($accessProject);
        $em->flush();

        $jsonAccessProject = $serializer->serialize($accessProject, 'json', ['groups' => 'getAccessProjects']);

        $location = $urlGenerator->generate('detailAccessProject', ['id' => $accessProject->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonAccessProject, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/access-projects/{id}', name: 'updateAccessProject', methods: ['PUT'])]
    public function updateAccessProject(Request $request, SerializerInterface $serializer, AccessProject $currentAccessProject, EntityManagerInterface $em): JsonResponse
    {
        $serializer->deserialize(
            $request->getContent(),
            AccessProject::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentAccessProject,
            ]
        );

        $em->persist($currentAccessProject);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/access-projects/{id}', name: 'deleteAccessProject', methods: ['DELETE'])]
    public function deleteAccessProject(int $id, EntityManagerInterface $em, AccessProjectRepository $accessProjectRepository): JsonResponse
    {
        $accessProject = $accessProjectRepository->find($id);

        if (!$accessProject) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $em->remove($accessProject);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}