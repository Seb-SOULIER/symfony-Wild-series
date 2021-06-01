<?php
namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProgramController
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * Correspond à la route /programs/ et au name "program_index"
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index():Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        return $this->render('program/index.html.twig', [
            'programs' => $programs
        ]);
    }

    /**
     * Getting a program by id
     *
     * @Route("/show/{program}", name="show")
     * @return Response
     */
    public function show(Program $program):Response
    {
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$program.' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons
        ]);
    }
    /**
     * @Route("/{program}/seasons/{season}", name="season_show")
     * @return Response
     */
    public function showSeason(Program $program,Season $season):Response
    {
        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $season]);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes
        ]);
    }
    /**
     * @Route("/{program}/seasons/{season}/episodes/{episode}", name="episode_show")
     * @return Response
     */
    public function showEpisode(Program $program,Season $season,Episode $episode):Response
    {
//        $program = $this->getDoctrine()
//            ->getRepository(Program::class)
//            ->findOneBy(['id' => $program]);
//
//        $season = $this->getDoctrine()
//            ->getRepository(Season::class)
//            ->findOneBy(['id' => $season]);
//
//        $episodes = $this->getDoctrine()
//            ->getRepository(Episode::class)
//            ->findBy(['season' => $season]);
//
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }
}