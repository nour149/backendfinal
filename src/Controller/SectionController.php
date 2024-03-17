<?php

namespace App\Controller;

use App\Entity\Section;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


class SectionController extends AbstractController
{
    #[Route('/section', name: 'app_section')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SectionController.php',
        ]);
    }

    #[Route('/sections', name: 'sections', methods: ['GET'])]
    public function getSectionList(SectionRepository $sectionRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $sectionList = $sectionRepository->findAll();

            $formattedSectionList = [];
            foreach ($sectionList as $section) {
                $formattedSectionList[] = [
                    'id' => $section->getId(),
                    'name' => $section->getName(),
                    'created_at' => $section->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $section->getUpdatedAt()->format('Y-m-d H:i:s'),
                    // Add more properties as needed
                ];
            }

            $jsonSectionList = $serializer->serialize($formattedSectionList, 'json');
            return new JsonResponse($jsonSectionList, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    #[Route('/api/sections/{id}', name: 'detailSection', methods: ['GET'])]
    public function getDetailSection(int $id, SerializerInterface $serializer, SectionRepository $sectionRepository): JsonResponse
    {
        $section = $sectionRepository->find($id);
        if ($section) {
            $jsonSection = $serializer->serialize($section, 'json');
            return new JsonResponse($jsonSection, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/sections', name: "createSection", methods: ['POST'])]
    public function createSection(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $section = $serializer->deserialize($request->getContent(), Section::class, 'json');
        $em->persist($section);
        $em->flush();

        $jsonSection = $serializer->serialize($section, 'json', ['groups' => 'getSections']);

        return new JsonResponse($jsonSection, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/sections/{id}', name: 'updateSection', methods: ['PUT'])]
    public function updateSection(Request $request, SerializerInterface $serializer, Section $currentSection, EntityManagerInterface $em): JsonResponse
    {
        $serializer->deserialize(
            $request->getContent(),
            Section::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $currentSection,
            ]
        );

        $em->persist($currentSection);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/sections/{id}', name: 'deleteSection', methods: ['DELETE'])]
    public function deleteSection(Section $section, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($section);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

































































}
