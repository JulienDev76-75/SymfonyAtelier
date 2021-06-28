<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Subject;

class ForumController extends AbstractController
{
    #[Route('/forum', name: 'forum')]
    #[Route('/', name: 'index')]
    //on peut faire deux routes pour renvoyer la même page
    public function index(): Response
    {
        //va me chercher le manager de la classe Subject qu'on stock dans $subjectRepository pour indiquer la table (manière de procéder)
        //https://symfony.com/doc/current/profiler.html pour avoir la toolbar de debug et voir les infos de larray
        //on va maper le resultat dans index.html.twig
        $subjectRepository = $this->getDoctrine()->getRepository(Subject::class);
        $subjects = $subjectRepository->findAll();
        dump($subjects);
        //comment faire remonter le $exemple dans le twig ? passe le dans le render
        $exemple = "coucou";
        //c'est le subjects variable de mon template, j'injecte dans index.html.twig une variable subject qui a pour valeur la variable subject de mon controller
        return $this->render('forum/index.html.twig', [
            'subjects' => $subjects,
            'coucoudanstwig' => $exemple,
        ]);
    }

    #[Route('/forum/rules', name: 'rules')]
    //faudra faire une VIEW spécifique pour rules
    public function rules(): Response
    {
        return $this->render('forum/rules.html.twig', [
            'controller_name' => 'ForumController',
        ]);
    }
}
