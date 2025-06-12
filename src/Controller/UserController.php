<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\InstrumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/api/v1/users', name: 'api_get_all_user', methods: ['GET'])]  
    public function getAll(  
        UserRepository $userRepository,  
        TagAwareCacheInterface $cache,  
        SerializerInterface $serializer  
    ): JsonResponse {  
        $idCache = "getAllUsers";  
        $jsonData = $cache->get($idCache, function (ItemInterface $item) use ($userRepository, $serializer) {  
            $data = $userRepository->findAll();  
            return $serializer->serialize($data, 'json', ['groups' => ["user"]]);  
        });  
  
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);  
    }

    #[Route('api/v1/users/{id}', name: 'api_get_user', methods: ['GET'])]  
    public function get(User $id, SerializerInterface $serializer): JsonResponse  
    {  
        $jsonData = $serializer->serialize($id, 'json', ['groups' => ["user"]]);  
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);  
    }

    #[Route('/api/v1/user/profile', name: 'api_get_user_profile', methods: ['GET'])]  
    public function getProfile(SerializerInterface $serializer): JsonResponse  
    {  
        // Récupérer l'utilisateur connecté  
        $user = $this->getUser();  
          
        if (!$user) {  
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);  
        }  
          
        // Sérialiser les données utilisateur  
        $jsonData = $serializer->serialize($user, 'json', ['groups' => ["user"]]);  
          
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);  
    } 
    
    #[Route('/api/v1/users', name: 'api_create_user', methods: ['POST'])]
    public function addUser(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');
            
            if (!$user->getUsername() || !$user->getPassword()) {
                return new JsonResponse(['error' => 'Username and password are required'], Response::HTTP_BAD_REQUEST);
            }
            
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            
            if (empty($user->getRoles())) {
                $user->setRoles(['ROLE_USER']);
            }
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            $jsonData = $serializer->serialize($user, 'json', ['groups' => ['user']]);
            
            $location = $urlGenerator->generate('api_get_user', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            
            return new JsonResponse($jsonData, Response::HTTP_CREATED, ['Location' => $location], true);
            
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid data provided'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/v1/users/{userId}/instruments', name: 'api_add_instrument_to_user', methods: ['POST'])]  
    public function addInstrumentToUser(  
        int $userId,  
        Request $request,  
        UserRepository $userRepository,  
        InstrumentRepository $instrumentRepository,  
        EntityManagerInterface $entityManager  
    ): JsonResponse {  
        $user = $userRepository->find($userId);  
        if (!$user) {  
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);  
        }  
  
        $data = json_decode($request->getContent(), true);  
        if (!isset($data['instrumentId'])) {  
            return new JsonResponse(['error' => 'Instrument ID is required'], Response::HTTP_BAD_REQUEST);  
        }  
  
        $instrumentId = $data['instrumentId'];  
        $instrument = $instrumentRepository->find($instrumentId);  
        if (!$instrument) {  
            return new JsonResponse(['error' => 'Instrument not found'], Response::HTTP_NOT_FOUND);  
        }  
  
        if ($user->getInstruments()->contains($instrument)) {  
            return new JsonResponse(['message' => 'User already has this instrument'], Response::HTTP_CONFLICT);  
        }  
  
        $user->addInstrument($instrument);  
        $entityManager->persist($user);  
        $entityManager->flush();  
  
        return new JsonResponse(['message' => 'Instrument added to user successfully'], Response::HTTP_OK);  
    }  
}
