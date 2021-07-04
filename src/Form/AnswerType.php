<?php

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//à ajouter à la main
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', null, [
                "label" => "votre réponse :"
            ])
            ->add('repondre', SubmitType::class, [
                //dans l'attribut, créez moi une class "bg-danger", on peut le faire même pour le content ou title
                //attr est une option qui existe sur tous les champs de tout les formulaires
                "attr" =>  ["class" => "bg-danger text-white"]
                // 'row-attr' => ['class' => 'text-center']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);
    }
}
