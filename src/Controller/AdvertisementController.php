<?php

namespace App\Controller;

use OpenApi\Annotations\Tag;
use App\Entity\Advertisement;
use App\Repository\InstrumentRepository;
use App\Repository\MusicGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Repository\AdvertisementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class AdvertisementController extends AbstractController
{
    #[Route('/advertisement', name: 'app_advertisement')]
    public function index(): Response
    {
        return $this->render('advertisement/index.html.twig', [
            'controller_name' => 'AdvertisementController',
        ]);
    }

    #[Route('/api/v1/advertisements', name: 'api_get_all_advertisements', methods: ['GET'])]
    public function getAll(
        AdvertisementRepository $advertisementRepository,
        TagAwareCacheInterface $cache,  
        SerializerInterface $serializer 
    ): Response
    {
        $cacheId = 'getAllAdvertisements';

        $jsonData = $cache->get($cacheId, function (ItemInterface $item) use ($advertisementRepository, $serializer) {  
            $item->tag('advertisementCache');  
            $advertisements = $advertisementRepository->findAll();  
            return $serializer->serialize($advertisements, 'json', ['groups' => ['advertisement']]);
        });  
        
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true); 
    }

    #[Route('api/v1/advertisement/{id}', name: 'api_get_advertisement', methods: ['GET'])]
    public function get(Advertisement $id, SerializerInterface $serializer): JsonResponse  
    {  
        $jsonData = $serializer->serialize($id, 'json', ['groups' => ["advertisement"]]);  
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);  
    }

    #[Route('/api/v1/advertisement', name: 'api_create_advertisement', methods: ['POST'])]
    public function create(
        ValidatorInterface $validator,
        Request $request,
        MusicGroupRepository $musicGroupRepository,
        InstrumentRepository $instrumentRepository,
        UrlGeneratorInterface $urlGenerator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cache
    ): JsonResponse {
        $content = $request->toArray();
    
        // Créer une nouvelle instance manuellement
        $advertisement = new Advertisement();
        $advertisement->setTitle($content['title'] ?? '');
        $advertisement->setDescription($content['description'] ?? '');
        $advertisement->setLocation($content['location'] ?? '');
        $advertisement->setRadius($content['radius'] ?? 0);
    
        $creatorId = $content['creatorId'] ?? null;
        if (!$creatorId) {
            throw new BadRequestHttpException('Creator ID is required');
        }
    
        $creator = $musicGroupRepository->find($creatorId);
        if (!$creator) {
            throw new NotFoundHttpException('Creator not found');
        }
        $advertisement->setCreator($creator);
    
        // Gestion des instruments par IDs
        if (!empty($content['instruments']) && is_array($content['instruments'])) {
            foreach ($content['instruments'] as $instrumentId) {
                $instrument = $instrumentRepository->find($instrumentId);
                if ($instrument) {
                    $advertisement->addInstrument($instrument);
                } else {
                    throw new NotFoundHttpException("Instrument with ID $instrumentId not found");
                }
            }
        }
    
        $advertisement->setCreatedAt(new \DateTime());
        $advertisement->setExpiresAt((new \DateTime())->modify('+30 days'));
        $advertisement->setStatus('ACTIVE');
    
        $errors = $validator->validate($advertisement);
        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST,
                [],
                true
            );
        }
    
        $entityManager->persist($advertisement);
        $entityManager->flush();
    
        $cache->invalidateTags(['advertisementCache']);
    
        $jsonData = $serializer->serialize($advertisement, 'json', ['groups' => ['advertisement']]);
        $location = $urlGenerator->generate('api_get_advertisement', ['id' => $advertisement->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    
        return new JsonResponse($jsonData, Response::HTTP_CREATED, ['Location' => $location], true);
    }
}
