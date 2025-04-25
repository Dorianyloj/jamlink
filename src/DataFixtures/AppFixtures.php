<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Pool;
use App\Entity\Song;
use App\Entity\User;
use App\Entity\Instrument;
use App\Entity\MusicGroup;
use App\Entity\MusicStyle;
use App\Entity\Advertisement;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

    $musicStyles = $this->loadMusicStyles($manager);
    
    $instruments = $this->loadInstruments($manager);

    $allUsers = $this->loadUsers($manager, $instruments);

    $allGroups = $this->loadMusicGroups($manager, $allUsers, $musicStyles);

    $this->loadAdvertisements($manager, $allGroups, $instruments);

    $manager->flush();
}

    private function loadMusicStyles(ObjectManager $manager): array
    {
        $stylesData = [
            'Rock',
            'Metal',
            'Jazz',
            'Blues',
            'Pop',
            'Funk',
            'Reggae',
            'Classical',
            'Electronic',
            'Hip Hop',
            'Folk',
            'Country'
        ];

        $styles = [];
        foreach ($stylesData as $styleName) {
            $style = new MusicStyle();
            $style->setName($styleName);
            $manager->persist($style);
            $styles[] = $style;
        }

        return $styles;
    }

    private function loadUsers(ObjectManager $manager, array $instruments): array
    {
        $users = [];
        
        // Admin user
        $admin = new User();
        $password = $this->userPasswordHasher->hashPassword($admin, "password");
        $admin->setUsername('admin')
            ->setFirstName('Admin')
            ->setLastName('System')
            ->setPassword($password)
            ->setRoles(["ROLE_ADMIN"])
            ->setLocation('Paris')
            ->setExperience(rand(1, 20))
            ->setLevel(['BEGINNER', 'INTERMEDIATE', 'EXPERT'][rand(0, 2)]);

        $randomInstruments = array_rand($instruments, 2);
        foreach ($randomInstruments as $instrumentIndex) {
            $admin->addInstrument($instruments[$instrumentIndex]);
        }

        $manager->persist($admin);
        $users[] = $admin;

        // Regular users
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $password = $this->faker->password(2, 6);
            $firstName = $this->faker->firstName();
            $lastName = $this->faker->lastName();

            $user->setFirstName($firstName)
                ->setLastName($lastName)
                ->setUsername($firstName . "." . $lastName)
                ->setPassword($this->userPasswordHasher->hashPassword($user, $password))
                ->setRoles(["ROLE_USER"])
                ->setLocation($this->faker->city)
                ->setExperience(rand(1, 20))
                ->setLevel(['BEGINNER', 'INTERMEDIATE', 'EXPERT'][rand(0, 2)]);

            // Attribution d'instruments aléatoires
            $randomInstruments = array_rand($instruments, rand(1, 3));
            if (!is_array($randomInstruments)) {
                $randomInstruments = [$randomInstruments];
            }
            foreach ($randomInstruments as $instrumentIndex) {
                $user->addInstrument($instruments[$instrumentIndex]);
            }

            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }

    private function loadMusicGroups(ObjectManager $manager, array $users, array $musicStyles): array
{
    $groups = [];
    $groupNames = [
        'The Rolling Stones',
        'Led Zeppelin',
        'Pink Floyd',
        'The Beatles',
        'Queen',
        'Metallica',
        'AC/DC',
        'Black Sabbath',
        'Deep Purple',
        'Iron Maiden'
    ];

    $statusChoices = ['ACTIVE', 'SEARCHING_MEMBERS', 'INACTIVE'];

    foreach ($groupNames as $index => $name) {
        $group = new MusicGroup();
        
        // Sélection aléatoire d'un leader parmi les utilisateurs
        $leader = $users[array_rand($users)];
        $group->addUser($leader);
        
        $group->setName($name)
            ->setDescription($this->faker->paragraph(2))
            ->setUserLeader($leader)
            ->setLocation($this->faker->city)
            ->setLevel(['BEGINNER', 'INTERMEDIATE', 'EXPERT'][rand(0, 2)])
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setStatus($statusChoices[array_rand($statusChoices)])
            ->setMaxMembers(rand(3, 8));

        // Ajout de styles de musique aléatoires
        $randomStyles = array_rand($musicStyles, rand(1, 3));
        if (!is_array($randomStyles)) {
            $randomStyles = [$randomStyles];
        }
        foreach ($randomStyles as $styleIndex) {
            $group->addMusicStyle($musicStyles[$styleIndex]);
        }

        // Ajout de membres aléatoires
        $numMembers = rand(1, $group->getMaxMembers() - 1); // -1 pour le leader
        $shuffledUsers = $users;
        shuffle($shuffledUsers);
        
        for ($i = 0; $i < $numMembers; $i++) {
            if ($shuffledUsers[$i] !== $leader) {
                $group->addUser($shuffledUsers[$i]);
            }
        }

        $manager->persist($group);
        $groups[] = $group;
    }

    return $groups;
}

    private function loadInstruments(ObjectManager $manager): array
    {
        // Votre code existant pour les instruments
        $instrumentsData = [
            ['Guitare électrique', 'STRINGS'],
            ['Guitare acoustique', 'STRINGS'],
            ['Basse', 'STRINGS'],
            ['Violon', 'STRINGS'],
            ['Violoncelle', 'STRINGS'],
            ['Saxophone', 'WIND'],
            ['Flûte traversière', 'WIND'],
            ['Clarinette', 'WIND'],
            ['Trompette', 'BRASS'],
            ['Trombone', 'BRASS'],
            ['Batterie', 'PERCUSSION'],
            ['Djembé', 'PERCUSSION'],
            ['Cajon', 'PERCUSSION'],
            ['Piano', 'KEYS'],
            ['Synthétiseur', 'KEYS'],
            ['Chant lead', 'VOICE'],
            ['Chœurs', 'VOICE'],
            ['DJ/Platines', 'ELECTRONIC'],
            ['Boîte à rythmes', 'ELECTRONIC']
        ];

        $instruments = [];
        foreach ($instrumentsData as [$name, $category]) {
            $instrument = new Instrument();
            $instrument->setName($name);
            $instrument->setCategory($category);
            $manager->persist($instrument);
            $instruments[] = $instrument;
        }

        return $instruments;
    }

    private function loadAdvertisements(ObjectManager $manager, array $musicGroups, array $instruments): void
{
    $advertisementTitles = [
        'Groupe cherche guitariste',
        'Batteur expérimenté recherche groupe',
        'Cherche bassiste pour groupe metal',
        'Groupe jazz recherche saxophoniste',
        'Pianiste cherche formation',
        'Cherche chanteur/chanteuse rock',
        'Groupe funk cherche section cuivre',
        'Cherche guitariste rythmique',
        'Groupe pop cherche claviériste',
        'Batteur disponible pour projet'
    ];

    $descriptions = [
        'Nous recherchons un musicien motivé et disponible pour répétitions hebdomadaires.',
        'Musicien avec expérience scénique, disponible pour projets sérieux.',
        'Groupe établi cherche à compléter sa formation pour concerts et enregistrements.',
        'Projet musical ambitieux en cours de formation.',
        'Recherche musiciens pour création groupe de reprises.',
    ];

    foreach ($advertisementTitles as $index => $title) {
        $ad = new Advertisement();
        
        $group = $musicGroups[array_rand($musicGroups)];
        $creator = $group;

        // Sélectionne des instruments aléatoires recherchés
        $randomInstruments = array_rand($instruments, rand(1, 3));
        if (!is_array($randomInstruments)) {
            $randomInstruments = [$randomInstruments];
        }

        $ad->setTitle($title)
           ->setDescription($descriptions[array_rand($descriptions)])
           ->setCreator($creator)
           ->setLocation($this->faker->city)
           ->setRadius(rand(5, 50))
           ->setCreatedAt(new \DateTime())
           ->setExpiresAt(new \DateTime('+' . rand(1, 3) . ' months'))
           ->setStatus(['ACTIVE', 'CLOSED'][rand(0, 1)]);

        // Ajout des instruments recherchés
        foreach ($randomInstruments as $instrumentIndex) {
            $ad->addInstrument($instruments[$instrumentIndex]);
        }

        $manager->persist($ad);
    }
}
}