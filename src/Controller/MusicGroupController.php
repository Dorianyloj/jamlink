<?php

namespace App\Controller;

use App\Entity\MusicGroup;
use App\Repository\UserRepository;
use App\Repository\MusicGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Repository\PoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MusicGroupController extends AbstractController
{
    #[Route('/music/group', name: 'app_music_group')]
    public function index(): Response
    {
        return $this->render('music_group/index.html.twig', [
            'controller_name' => 'MusicGroupController',
        ]);
    }

    #[Route('api/v1/musicgroups', name: 'api_get_all_music_group', methods: ['GET'])]
    public function getAll(
        MusicGroupRepository $musicGroupRepository,
        TagAwareCacheInterface $cache,
        SerializerInterface $serializer
    ): JsonResponse {


        $idCache = "getAllMusicGroups";
        $jsonData = $cache->get($idCache, function (ItemInterface $item) use ($musicGroupRepository, $serializer) {
            $data = $musicGroupRepository->findAll();
            return $serializer->serialize($data, 'json', ['groups' => ["music_group"]]);
        });

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);

    }

    #[Route('api/v1/musicgroups/{id}', name: 'api_get_music_group', methods: ['GET'])]
    public function get(MusicGroup $id, SerializerInterface $serializer): JsonResponse
    {
        $jsonData = $serializer->serialize($id, 'json', ['groups' => ["music_group"]]);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('api/v1/musicgroups', name: 'api_create_music_group', methods: ['POST'])]

    public function create(
        ValidatorInterface $validator,
        Request $request,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cache
    ): JsonResponse {

        $musicGroup = $serializer->deserialize($request->getContent(), MusicGroup::class, 'json');
        $content = $request->toArray();
        $leaderId = $content['leaderId'] ?? null;
        
        if (!$leaderId) {
            throw new BadRequestHttpException('Leader ID is required');
        }
        $leader = $userRepository->find($leaderId);
        
        if (!$leader) {
            throw new NotFoundHttpException('Leader not found');
        }

        $musicGroup->setUserLeader($leader);
        $musicGroup->setCreatedAt(new \DateTime());
        $musicGroup->setUpdatedAt(new \DateTime());
        $musicGroup->setStatus('ACTIVE');

        $errors = $validator->validate($musicGroup);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), 
                JsonResponse::HTTP_BAD_REQUEST, 
                [], 
                true
            );
        }

        $entityManager->persist($musicGroup);
        $entityManager->flush();

        $cache->invalidateTags(['musicGroupsCache']);

        $jsonData = $serializer->serialize($musicGroup, 'json', [
            'groups' => ['music_group']
        ]);
        $location = $urlGenerator->generate(
            'api_get_music_group', 
            ['id' => $musicGroup->getId()], 
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse(
            $jsonData, 
            Response::HTTP_CREATED, 
            ["Location" => $location], 
            true
        );
    }

    #[Route('api/v1/musicgroups/{id}', name: 'api_update_music_group', methods: ['PATCH'])]
    public function update(
        MusicGroup $id,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cache
    ): JsonResponse {

        $musicGroup = $serializer->deserialize(
            $request->getContent(), 
            MusicGroup::class, 
            'json', 
            [AbstractNormalizer::OBJECT_TO_POPULATE => $id]
        );

        $entityManager->persist($musicGroup);
        $entityManager->flush();

        $cache->invalidateTags(['musicGroupsCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
