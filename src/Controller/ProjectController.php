<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'app_project')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProjectController.php',
        ]);
    }

    #[Route('/projects', name: 'projects', methods: ['GET'])]
    public function getProjetList(ProjetRepository $projetRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $projetList = $projetRepository->findAll();

            // Extract only the necessary information from each Projet entity
            $formattedProjetList = [];
            foreach ($projetList as $projet) {
                $formattedProjetList[] = [
                    'id'=>$projet->getId(),
                    'name' => $projet->getName(),
                    'color' => $projet->getColor(),
                    'archived' => $projet->isArchived(),
                    'created_at' => $projet->getCreatedAt()?->format('Y-m-d H:i:s'),
                    'updated_at' => $projet->getUpdatedAt()?->format('Y-m-d H:i:s'),
                    // Add more fields as needed
                ];
            }

            return new JsonResponse($formattedProjetList);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/projects/{id}', name: 'detailProject', methods: ['GET'])]
    public function getDetailProject(int $id, SerializerInterface $serializer, ProjetRepository $projetRepository): JsonResponse
    {
        $projet = $projetRepository->find($id);
        if ($projet) {
            $jsonProjet = $serializer->serialize($projet, 'json');
            return new JsonResponse($jsonProjet, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/projects', name: "createProject", methods: ['POST'])]
    public function createProject(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $projet = $serializer->deserialize($request->getContent(), Projet::class, 'json');
        $em->persist($projet);
        $em->flush();

        $jsonProjet = $serializer->serialize($projet, 'json', ['groups' => 'getProjets']);

        $location = $urlGenerator->generate('detailProject', ['id' => $projet->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonProjet, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/projects/{id}', name: 'updateProject', methods: ['PUT'])]
    public function updateProject(Request $request, SerializerInterface $serializer, Projet $currentProject, EntityManagerInterface $em): JsonResponse
    {
        $serializer->deserialize(
            $request->getContent(),
            Projet::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentProject,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['accessProjects', 'sections', 'tasks'],
            ]
        );

        $content = $request->toArray();

        $em->persist($currentProject);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/projects/{id}', name: 'deleteProject', methods: ['DELETE'])]
    public function deleteProject(int $id, EntityManagerInterface $em, ProjetRepository $projetRepository): JsonResponse
    {
        $projet = $projetRepository->find($id);

        if (!$projet) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $em->remove($projet);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }





}
