<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\PoolRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SongController extends AbstractController
{
    #[Route('api/v1/song', name: 'get_all_song', methods: ['GET'])]
    public function getAll(SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    {

        $data = $songRepository->findAll();
        $jsonData = $serializer->serialize($data, 'json', ['groups' => ["song"]]);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);

    }

    #[Route('api/v1/song/{id}', name: 'get_song', methods: ['GET'])]
    public function get(Song $id, SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    {
        $jsonData = $serializer->serialize($id, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('api/v1/song', name: 'create_song', methods: ['POST'])]
    public function create(Request $request, PoolRepository $poolRepository, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {

        $song = $serializer->deserialize($request->getContent(), Song::class, 'json');
        $idPool = $request->toArray()['idPool'] ?? null;
        $pool = $poolRepository->find($idPool);
        $song->addPool($pool);
        $song->setName($song->getName() ?? "Non Defini");
        $song->setStatus('on');
        $entityManager->persist($song);
        $entityManager->flush();
        $jsonData = $serializer->serialize($song, 'json', ['groups' => ['song']]);
        $location = $urlGenerator->generate('get_song', ["id" => $song->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonData, Response::HTTP_CREATED, ["Location" => $location], true);
    }


    #[Route('api/v1/song/{id}', name: 'update_song', methods: ['PATCH'])]
    public function update(Song $id, Request $request, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {


        $song = $serializer->deserialize($request->getContent(), Song::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $id]);
        $entityManager->persist($song);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/v1/song/{id}', name: 'delete_song', methods: ['DELETE'])]
    public function delete(Song $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {


        if ('' !== $request->getContent() && true === $request->toArray()['hard']) {
            $entityManager->remove($id);

        } else {
            $id->setStatus('off');
            $entityManager->persist($id);
        }
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}
