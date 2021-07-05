<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
//ainbsi avec ça, on va attendre une instanciation de userpasswdencoderinterface
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

//oublie pas de charger tes entités pour pouvoir les instancier
use App\Entity\User;
use App\Entity\Subject;
use App\Entity\Answer;

class AppFixtures extends Fixture
{

    private $encoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    //on crée 4 utilisateurs
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i < 5; $i++) {

            $user = new User();
            //va donner usermail1@exemple.com etc.
            $user->setEmail("useremail". $i ."@exemple.com");
            $password = $this->encoder->encodePassword($user, 'password' . $i);
            $user->setPassword($password);
            $user->setFirstname("Firstname". $i);
            $user->setLastName("Lastname". $i);

            //on veut générer un nombre de sujets aléatoire pour l'user, il aura entre 0 et 5 sujets d'ouverts
            //on utilise mt_rand, qu'elle tourne 0 et/ou jusqu'à 5 fois, voir paramètre dans la doc PHP
            for ($j=1; $j < mt_rand(1, 6); $j++) {
                $subject = new Subject();
                //on appelle les setters qui vont donner valeurs à mon entité subject, on met l'user pour vérifier s'il est bien lié
                $subject->setTitle("Title". $j ."User" . $i);
                $subject->setContent("some written long content". $j ."User" . $i);
                //génère une fausse date qui seraà celle d'aujourd'hui mais aléatoire possible avec des math random
                $subject->setPublished(new \DateTime());
                //je fais réfé à mon utilisateur actuel lié à ce sujet
                $subject->setUser($user);
                $manager->persist($subject);

                //On génère les réponses liées aux sujets
                //donc si on se co, on va sur "vos sujets", on a bien tous les sujets liés à l'utilsiateur
                for ($k=1; $k < mt_rand(1, 11); $k++) {
                    $answer = new Answer();
                    $answer->setContent("some SUPER short answer". $k ."User" . $i);
                    $answer->setPublished(new \DateTime());
                    $answer->setUser($user);
                    $answer->setSubject($subject);
                    //génère un user aléatoire au reponse, sauf que là problème, l'user n'es pas enregistré en bdd avant les questions soient crées
                    $answer->setUser($users[mt_rand(0, count($users) -1)])
                    $manager->persist($answer);
                }
            }

            $manager->persist($user);
        }
        //ne pas oublier le persist sinon le flush va rien enregistrer en bdd et ensuite on tire la chasse
        $manager->flush();
    }
}
