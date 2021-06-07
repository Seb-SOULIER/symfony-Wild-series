<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASONS = [
            ['number' => '1',
                'year' => '2018',
                'description' => 'Contenu de la saison 1',
            ],
            ['number' => '2',
                'year' => '2019',
                'description' => 'Contenu de la saison 2',
            ],
            ['number' => '3',
                'year' => '2020',
                'description' => 'Contenu de la saison 3',
            ],
            ['number' => '4',
                'year' => '2021',
                'description' => 'Contenu de la saison 4',
            ],
            ['number' => '5',
                'year' => '2022',
                'description' => 'Contenu de la saison 5',
            ],
        ];

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < count(ProgramFixtures::PROGRAMS); $i++) {

            foreach (self:: SEASONS as $key => $seasonTous) {
                $season = new Season();
                $season->setnumber($seasonTous['number']);
                $season->setyear($seasonTous['year']);
                $season->setdescription($seasonTous['description']);
                $season->setProgram($this->getReference('program_' . $i));
                $manager->persist($season);
                $this->addReference('season_' . $key. $i, $season);
                $manager->flush();
            }
        }
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}