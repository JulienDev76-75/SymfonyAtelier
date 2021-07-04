<?php

namespace App\Form;

use App\Entity\Subject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//obligé de charger un submit type, pas de base dans le formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

//on a deux methodes : celle qui construit qui gère le html, l'autre qui configure

class SubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //par défaut tu mets le title a null pour le disable et passer le label en français, tu réecris option label en titre 
            ->add('title', null, [
                "label" => "Votre Titre :",
            ])
            ->add('content', null, [
                "label" => "Posez votrer question :"
            ])
            // ->add('published');on retire le publisehd car le mec va pas dire quand il a posté son com
            // ajoute moi un champs de type submit et met lui pour valeur 'enregristrer', mais le button sera en primary qu'on pourra changer via le twig ou le formbuilder
            ->add('enregistrer', SubmitType::class, [
                //dans l'attribut, créez moi une class "bg-danger", on peut le faire même pour le content ou title
                //attr est une option qui existe sur tous les champs de tout les formulaires
                "attr" =>  ["class" => "bg-danger text-white"]
                // 'row-attr' => ['class' => 'text-center']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //les formulaires sont liés à une entité, ici Subject, et le formulaire va hydrater l'entité Subject
            'data_class' => Subject::class,
        ]);
    }
}
