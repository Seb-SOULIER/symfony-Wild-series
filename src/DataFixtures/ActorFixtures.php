<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActorFixtures extends Fixture
{
    const ACTOR = [
        'Andrew Lincoln',
        'Norman Reedus',
        'Lauren Cohan',
        'Danai Gurira'
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::ACTOR as $key => $ActorName) {
            $actor = new Actor();
            $actor->setName($ActorName);
            $manager->persist($actor);
        }
        $manager->flush();
    }
}