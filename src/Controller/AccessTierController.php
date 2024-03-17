<?php

namespace App\Controller;

use App\Entity\AccessTier;
use App\Repository\AccessTierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AccessTierController extends AbstractController
{
    #[Route('/access/tier', name: 'app_access_tier')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AccessTierController.php',
        ]);
    }


    #[Route('/access-tiers', name: 'access_tiers', methods: ['GET'])]
    public function getAccessTierList(AccessTierRepository $accessTierRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $accessTiers = $accessTierRepository->findAll();

            // Extract only the necessary information from each AccessTier entity
            $formattedAccessTiers = [];
            foreach ($accessTiers as $accessTier) {
                $formattedAccessTiers[] = [
                    'status' => $accessTier->getStatus(),
                    'role' => $accessTier->getRole(),
                    'created_at' => $accessTier->getCreatedAt()?->format('Y-m-d H:i:s'),
                    'updated_at' => $accessTier->getUpdatedAt()?->format('Y-m-d H:i:s'),
                    // Add more fields as needed
                ];
            }

            return new JsonResponse($formattedAccessTiers);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/access-tiers/{id}', name: 'detailAccessTier', methods: ['GET'])]
    public function getDetailAccessTier(int $id, SerializerInterface $serializer, AccessTierRepository $accessTierRepository): JsonResponse
    {
        $accessTier = $accessTierRepository->find($id);
        if ($accessTier) {
            $jsonAccessTier = $serializer->serialize($accessTier, 'json');
            return new JsonResponse($jsonAccessTier, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/access-tiers', name: "createAccessTier", methods: ['POST'])]
    public function createAccessTier(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $accessTier = $serializer->deserialize($request->getContent(), AccessTier::class, 'json');
        $em->persist($accessTier);
        $em->flush();

        $jsonAccessTier = $serializer->serialize($accessTier, 'json', ['groups' => 'getAccessTiers']);

        $location = $urlGenerator->generate('detailAccessTier', ['id' => $accessTier->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonAccessTier, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/access-tiers/{id}', name: 'updateAccessTier', methods: ['PUT'])]
    public function updateAccessTier(Request $request, SerializerInterface $serializer, AccessTier $currentAccessTier, EntityManagerInterface $em): JsonResponse
    {
        $serializer->deserialize(
            $request->getContent(),
            AccessTier::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentAccessTier,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['guests', 'hosts', 'tier'],
            ]
        );

        $content = $request->toArray();

        $em->persist($currentAccessTier);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/access-tiers/{id}', name: 'deleteAccessTier', methods: ['DELETE'])]
    public function deleteAccessTier(int $id, EntityManagerInterface $em, AccessTierRepository $accessTierRepository): JsonResponse
    {
        $accessTier = $accessTierRepository->find($id);

        if (!$accessTier) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $em->remove($accessTier);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }










}
