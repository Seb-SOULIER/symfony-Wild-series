<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Form\SearchProgramFormType;
use App\Repository\ProgramRepository;
use App\Service\Mailer;
use App\Service\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    public function index(Request $request, ProgramRepository $programRepository):Response
    {
        $form = $this->createForm(SearchProgramFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository-> findAll();
        }

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'form'=>$form->createView()
        ]);
    }

    /**
     * The controller for the category add form
     * Display the form or deal with it
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify, Mailer $mailer) : Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Category Object
            $entityManager->persist($program);
            // Flush the persisted object
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());
            $entityManager->flush();

            $this->addFlash('success','Le programme a bien été enregistré');
            try {
                $mailer->sendMail($program, 'Program/newProgramEmail.html.twig');
                return $this->redirectToRoute('program_index');
            } catch (\Exception $e){
            };
                // Finally redirect to categories list

        }
        // Render the form
        return $this->render('program/new.html.twig', ["form" => $form->createView()]);
    }

    /**
     * Getting a program by id
     *
     * @Route("/show/{slug}", name="show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
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
    @Route("/{slug}/season/{season_id<^[0-9]+$>}/", name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season_id": "id"}})
     * @return Response
     */
    public function showSeason(Program $program,Season $season):Response
    {
        $episode = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $season]);

        if (!$episode) {
            throw $this->createNotFoundException(
                'No program with id : '.$season.' found in program\'s table.'
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episode
        ]);
    }
    /**
     *@Route("/{slug}/season/{season_id<^[0-9]+$>}/episode/{slug_episode}", name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season_id": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"slug_episode": "slug"}})
     * @return Response
     */
    public function showEpisode(Program $program,Season $season,Episode $episode,Request $request):Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['episode' => $episode]);

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form->createView(),
            'comments' => $comments
        ]);
    }
    /**
     * @Route("/{slug}/edit", name="edit")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @return Response
     */
    public function edit(Request $request, Program $program): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && (!($this->getUser() == $program->getOwner()))) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Tu as mis a jours ton programme');

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'season' => $program,
            'form' => $form->createView(),
        ]);
    }
    /**
     *@Route("/comment/{comment_id}", name="comment_delete")
     * @ParamConverter("comment", class="App\Entity\Comment", options={"mapping": {"comment_id": "id"}})
     * @return Response
     */
    public function comment_delete(Comment $comment, Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));

//        return $this->redirectToRoute('program_episode_show',[
//            'slug' => $program->getSlug(),
//            'season_id' => $season->getId(),
//            'slug_episode'=>$episode->getSlug()
//        ]);
    }

    /**
     * @Route("/{program}/watchlist", name="watchlist", methods={"GET","POST"})
     * @return Response
     */
    public function addToWatchList(Request $request, Program $program, EntityManagerInterface $manager):Response
    {
        if ($this->getUser()->isInWatchList($program)) {
            $this->getUser()->removeFromWatchlist($program);
            $this->addFlash('warning', 'Série supprimé des favoris');

        } else {
            $this->getUser()->addToWatchList($program);
            $this->addFlash('success', 'Série ajouté en favori');
        }
        $manager->flush();

        return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);

    }
}
