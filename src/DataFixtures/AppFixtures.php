<?php

namespace App\DataFixtures;

use App\Entity\Song;
use App\Entity\Pool;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{

    private Generator $faker;
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $pools = [];
        for ($i = 0; $i < 10; $i++) {
            # code...
            $pool = new Pool();
            $pool->setName($this->faker->name($i % 2 ? "male" : "female"));
            $pool->setCode('toto' . $i);
            $pool->setStatus('on');
            $manager->persist($pool);
            $pools[] = $pool;
        }
        for ($i = 0; $i < 100; $i++) {
            # code...
            $song = new Song();
            $song->setName($this->faker->name($i % 2 ? "male" : "female"));
            $song->setArtiste("Kiss Husky" . $i);
            $song->setStatus("on");
            $song->addPool($pools[array_rand($pools, 1)]);
            $manager->persist($song);
        }
        $manager->flush();
    }
}
