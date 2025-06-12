<?php

namespace App\Controller;

use App\Repository\InstrumentRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class InstrumentController extends AbstractController
{
    #[Route('/instrument', name: 'app_instrument')]
    public function index(): Response
    {
        return $this->render('instrument/index.html.twig', [
            'controller_name' => 'InstrumentController',
        ]);
    }

    #[Route('/api/v1/instruments', name: 'api_get_all_instruments', methods: ['GET'])]  
    public function getAll(  
        InstrumentRepository $instrumentRepository,  
        TagAwareCacheInterface $cache,  
        SerializerInterface $serializer  
    ): JsonResponse {  
        $cacheId = 'getAllInstruments';  
  
        $jsonData = $cache->get($cacheId, function (ItemInterface $item) use ($instrumentRepository, $serializer) {  
            $item->tag('instrumentsCache');  
            $instruments = $instrumentRepository->findAll();  
            return $serializer->serialize($instruments, 'json', ['groups' => ['instrument']]);  
        });  
  
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);  
    } 
}
