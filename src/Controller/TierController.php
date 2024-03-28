<?php

namespace App\Controller;

use App\Entity\Tier;
use App\Repository\TierRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TierController extends AbstractController
{
    #[Route('/tier', name: 'app_tier')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TierController.php',
        ]);
    }

    #[Route('/tiers', name: 'tiers', methods: ['GET'])]
    public function getTierList(
        TierRepository $tierRepository,
        SerializerInterface $serializer,
        AuthorizationCheckerInterface $authorizationChecker
    ): JsonResponse {
      
        try {
            $tierList = $tierRepository->findAll();
    
            // Serialize the tier list
            $jsonTierList = $serializer->serialize($tierList, 'json');
    
            return new JsonResponse($jsonTierList, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            // Handle exceptions
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    #[Route('/api/tiers/{id}', name: 'detailTier', methods: ['GET'])]
    public function getDetailTier(int $id, SerializerInterface $serializer, TierRepository $tierRepository): JsonResponse
    {

        $tier = $tierRepository->find($id);
        if ($tier) {
            $jsonTier = $serializer->serialize($tier, 'json');
            return new JsonResponse($jsonTier, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/tiers/{id}', name: 'deleteTier', methods: ['DELETE'])]
    public function deleteTier(Tier $tier, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($tier);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/tiers', name: "createTier", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous navez pas les droits suffisants pour créer un livre')]
    public function createTier(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {

        $tier = $serializer->deserialize($request->getContent(), Tier::class, 'json');
        $em->persist($tier);
        $em->flush();

        $jsonTier = $serializer->serialize($tier, 'json', ['groups' => 'getTiers']);

        $location = $urlGenerator->generate('detailTier', ['id' => $tier->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonTier, Response::HTTP_CREATED, ["Location" => $location], true);


    }


    #[Route('/api/tiers/{id}', name: 'updateTier', methods: ['PUT'])]
    public function updateTier(Request $request, SerializerInterface $serializer, Tier $currentTier, EntityManagerInterface $em, TierRepository $tierRepository): JsonResponse
    {
        // Deserialize the request content into the existing $currentTier
        $serializer->deserialize(
            $request->getContent(),
            Tier::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentTier,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['projects'], // Ignore "projects" during deserialization
            ]
        );

        // If you want to update the "projects" property separately based on the request payload, you can do it like this:
        $content = $request->toArray();

        // Assuming "projects" is an array in the request payload
        if (isset($content['projects'])) {
            // Convert the array to a Doctrine\Common\Collections\Collection
            $updatedProjects = new ArrayCollection($content['projects']);

            // Update the related projects for the tier
            $currentTier->setProjects($updatedProjects);
        }

        // Persist and flush the changes
        $em->persist($currentTier);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

}
