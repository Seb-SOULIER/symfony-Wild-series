<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPassordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        // $user = new User();
        // $user->setPassword($this->passwordEncoder->hashPassword($user,'the_new_password'));
        // Création d’un utilisateur de type “contributeur” (= auteur)
        $contributor = new User();
        $contributor->setEmail('contributor@monsite.com');
        $contributor->setRoles(['ROLE_CONTRIBUTOR']);
        $contributor->setPassword($this->passwordEncoder->encodePassword(
            $contributor,
            'contributorpassword'
        ));
        $this->addReference('user1',$contributor);
        $manager->persist($contributor);

        $contributor1 = new User();
        $contributor1->setEmail('seb@seb.fr');
        $contributor1->setRoles(['ROLE_CONTRIBUTOR']);
        $contributor1->setPassword($this->passwordEncoder->encodePassword(
            $contributor1,
            '1234'
        ));
        $this->addReference('user2',$contributor1);
        $manager->persist($contributor1);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));
        $manager->persist($admin);
        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();
    }
}
