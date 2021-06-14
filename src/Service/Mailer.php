<?php


namespace App\Service;

use App\Entity\Episode;
use App\Entity\Program;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class Mailer
{
    private $mailer;
    private $parameterBag;
    private $twig;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $parameterBag, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->parameterBag=$parameterBag;
        $this->twig=$twig;
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function sendMail($program, string $template): void
    {
        $email = (new Email())
            ->from($this->parameterBag->get('mailer_from'))
            ->to('soulier.sebastien.ss@gmail.com')
            ->subject('Une nouvelle série vient d\'être publiée !')
            ->html($this->twig->render($template,['program'=>$program]));
        $this->mailer->send($email);
    }
}