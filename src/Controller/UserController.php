<?php

namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    /**
     * @Route("/my-profile", name="user_profil")
     * @IsGranted("ROLE_CONTRIBUTOR")
     */
    public function index(ProgramRepository $programRepository): Response
    {
        $user = $this->getUser();
        return $this->render('user/index.html.twig', [
            'programs' => $programRepository->findBy(['owner' => $user])
        ]);
    }
}
