<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
//traite moi l'object requete actuel, faut instancier $request et injecter dans new Subject l'object de classe request qui s'appelle $request, ma mérthode newsubject attend en param $request qui doit être une instance de la classe Request
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Subject;
//on charge le formulaire dans le controller
//objectif du formulaire est d'hydrater l'entité -ici Subject- donc faut créer un objet pour réceptionner données du formulaire et ensuitre le controller a deux params (le form et le $ que le form dotit hydrater)
use App\Form\SubjectType;
use App\Entity\Answer;
use App\Entity\AnswerType;
use App\Repository\SubjectRepository;

class ForumController extends AbstractController
{
    //COUPLE URL ROUTE - METHODE
    #[Route('/forum', name: 'forum')]
    #[Route('/', name: 'index')]
    //on peut faire deux routes pour renvoyer la même page
    public function index(): Response
    {
        //va me chercher le manager de la classe Subject qu'on stock dans $subjectRepository pour indiquer la table (manière de procéder)
        //https://symfony.com/doc/current/profiler.html pour avoir la toolbar de debug et voir les infos de larray
        //on va maper le resultat dans index.html.twig, get repo car un simple select
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

    #[Route('/forum/subject/new', name: 'newSubject')]
    //faudra faire une VIEW spécifique pour rules
    public function newSubject(Request $request): Response
    {
        //$subject doit recup données du form, $subject est un objet vide
        $subject = new Subject();
        //createform() ->creer form va hydrater la $subject crée avec données du form sur base de la class Subject Type
        //STYPE type de formulaire à créer, et second param est cible à hydrater
        $form = $this->createForm(SubjectType::class, $subject);
        //mais pour l'instant aucun enregistrement en BDD :
        //$request n'existe pas encore, cependant elle est dans la classe Request chargée dans le controller
        $form->handleRequest($request);
        //formule de base pour le form c'est dans la cond que tu pourras rajouter des conditions de verif
        if ($form->isSubmitted() && $form->IsValid()) {
            //instancie datetime pour avoir la date du jour lorsque tu postes, le \ est dans la racine de php (objet php qui existe deja dans le langage), pas le notre
            $subject->setPublished(new \DateTime());
            // dump($subject);
            //check avec la petite roue si ca a bien été hydraté puis enregistre en bdd
            //comment récupérer l'utilisateur connecté 
            $subject->setUser($this->GetUser()); //le $this fait référence au controller, sur mon sujet, associe lui comme utilisateur l'utilisateur actuellement connecté
            $entityManager = $this->getDoctrine()->getManager();
            //ENTITY MANAGER (de doctrine) ME PERMET DE GERER CRUD dans doctrine
            //select -> getrepository car pas de modif de la bdd à la place de getdoctrine
            $entityManager->persist($subject);
            //persist prépare enregistrement en bdd et valider au moment du flush donc entre les deux persist et le flush comme le prepare/execute
            $entityManager->flush();
            //une fois la requête passée, renvoi moi sur index
            return $this->redirectToRoute('index');
        }

        return $this->render('forum/newsubject.html.twig', [
            "form" => $form->createView()
        ]);
    }

    //on peut enchainer les URL ex : /subject/{name}/forum/{variable}/etc.
    //revient à faire subject?name= etc.
    //là on dit, dans la on dit que le parazmète dans URL est forcément un chiffre et sera id, ma route est "sécurisée"
    //comment récupérer paramètre "id" int ? bah lmets le en paramètre crétin
    #[Route('/forum/subject/{id}', name: 'single', requirements: ["id" => "\d+"])]
    //mettre un paramètre par défaut -> $id=1 dans le paramètre
    //Quand tu appelles la méthode single, charge un second paramètre qui est une instance de subjectrepository et qu'on va appeler $subjectrepo 
    public function single(int $id = 1, SubjectRepository $subjectRepository, Request $request): Response
    {
        dump($id);
        //méthode find trouver correspondance par la clé primaire, revient à 
        //$subjectRepository = $this->getDoctrine()->getRepository(Subject::class);
        //$subject = $subjectRepository->findAll(); le second param est une autre méthode, mais le find est équivalent du fetch, findall = fetchAll
        $subject = $subjectRepository->find($id);

        $answer = new Answer();
        //notre objet va hydrater l'objet answer
        $form = $this->createForm(AnswerType::class, $answer);

        $form->$form->handleRequest($request);
        if ($form->isSubmitted() && $form->IsValid()) {
            
            $answer->setPublished(new \DateTime());
            $answer->setUser($this->GetUser()); 
            $answer->setSubject($answer);
            $entityManager = $this->getDoctrine()->getManager()
            $entityManager->persist($answer);
            $entityManager->flush();

            return $this->redirectToRoute('index');

        return $this->render('forum/single.html.twig', [
            "subject" => $subject,
            "form" => $form->createview()

        ]);
        //comme si tu faisais $formcontroller = new forumcontroller () et ensuite $formcontroller ->single($_Get["id"])
    }

    #[Route('/user/subjects', name: 'userSubjects')]
    public function userSubjects(): Response
    {

        //je veux QUE les sujets de l'utilisateur connecté
        //findby recherche par sujet et user OU alors :
        $subjects = $this->getUser()->getSubjects(); //on accède à la session de l'User, donc ce sont SES sujets à lui, et après on récupére les sujets qui seront liés à lui
        return $this->render('forum/index.html.twig', [
            "subjects" => $subjects
        ]);
        //$this->getUser = on accède à l'user en session
    }
}

//relation entre table : clé primaire et FK, relation 0 to many(n), 0 quand c'est nullable
//la fk est tjrs du côté many, un user est peut écrire plusieurs sujets
//je veux relier mon objet subject à l'objet user (et non user.id car on bosse avec des objets) : on stock dazns subject l'objet user associé à CE sujet
//Tu vois relation association : quand tu fais make:entity -> subject -> user
//donc côté 1 (user) to many (subject) ou Many to 1
//tu relies à la classe User (faut que tu crées l'entity)
//la relation doit être bidirectionnelle ? -> est-ce que vous voulez être capable d'écrire $user -> getSubject
//si c'est mono directionnel, depuis user je n'aurais pas accès aux sujets
//manytoone->no->yes->yes->
//@ORM\ManyToOne(target:Entity=User::Class, inversedby:subjects) -> QUAND tu fais la relation tout ça se crée

//une entity user peut avoir plusieurs sujets, si je veux ajouter un sujet -> je veux ajouteru n sujet dans le tableau sujet -> addsubject qui push dans subject du sujet à ajouter
//vérifie si le sujet n'existe pas deja, et comme la relation est bidirectionnelle, j'associe un sujet à un user, mais comme la relation dans les deux sens, j'associe le sujet à l'utlisateur que je viens d'ajouter
//quand migration affiche erreur -> parce qu'une fois que l'user id est mis etc. y'aura une erreur sur la migration
//violation d'intégrité car user_id ne correspond à aucun sujet, FAUT TOUT VIRER DONC doctrine:database:drop --force
// ensuite php bin/console doctrine:migrations:migrate et la fixture contient tes données donc tu t'en fous, tu peux tout basarder
//va reprendre à la version de la dernière migration


//EntityTypeField : champs de choix destiné à relation entre entités

//récupérer object user stocké en session, getUser

//réponse lié à un user et lié à un sujet avec une date


































//Service : Votre application regorge d'objets utiles : un objet « Mailer » peut vous aider à envoyer des e-mails tandis qu'un autre objet peut vous aider à enregistrer des choses dans la base de données.
// Presque tout ce que votre application « fait » est en fait fait par l'un de ces objets. Et chaque fois que vous installez un nouveau bundle, vous en avez encore plus ! Le fichier bundle sert justement a injecter service dans le container, bundle appelé dès le début ->container dispo direct. Injection par les bundles ou Injection de dépendance (dans le sens injection de package direct en param), src fait partie du container d'ou le fait de pouvoir injecter directement dans les param
// Dans Symfony, ces objets utiles sont appelés services et chaque service vit à l'intérieur d'un objet très spécial appelé le conteneur de services . 
// Dans votre contrôleur, vous pouvez « demander » un service du conteneur en tapant un argument avec le nom de la classe ou de l'interface du service.
// Liste des services et utilités : php bin/console debug:autowiring --all ou spécifique -> php bin/console debug:container 'App\Service\Mailer' pour savoir à quoi ça sert.
// grâce à ça, on reçoit directement les paramètres dans les fonctions de nos controllers, Symfony les reconnait de suite publc fonction Service(Mailer $mailer) [=>injection de dépendances] et le container de service va directement nous les filer ->autowiring
