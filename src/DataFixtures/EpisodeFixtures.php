<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Entity\Season;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private $slug;

    public function __construct(Slugify $slug)
    {
        $this->slug = $slug;
    }

    public const EPISODES = [
        ['title' => 'episode 1',
            'number' => '1',
            'synopsis' => "Contenu de l'episode 1",
        ],
        ['title' => 'episode 2',
            'number' => '2',
            'synopsis' => "Contenu de l'episode 2",
        ],
        ['title' => 'episode 3',
            'number' => '3',
            'synopsis' => "Contenu de l'episode 3",
        ],
        ['title' => 'episode 4',
            'number' => '4',
            'synopsis' => "Contenu de l'episode 4",
        ],
        ['title' => 'episode 5',
            'number' => '5',
            'synopsis' => "Contenu de l'episode 5",
        ],
    ];

    public function load(ObjectManager $manager)
    {
        for ($a = 0; $a < count(ProgramFixtures::PROGRAMS); $a++) {
            for ($i = 0; $i < count(SeasonFixtures::SEASONS); $i++) {
                foreach (self:: EPISODES as $key => $episodeTous) {
                    $episode = new Episode();
                    $episode->setTitle($episodeTous['title']);
                    $episode->setNumber($episodeTous['number']);
                    $episode->setSynopsis($episodeTous['synopsis']);
                    $episode->setSeason($this->getReference('season_' . $i . $a));
                    $episode->setSlug($this->slug->generate($episodeTous['title']));
                    $manager->persist($episode);
                    $manager->flush();
                }
            }
        }
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}