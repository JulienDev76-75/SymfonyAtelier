{# sera crée quand tu auras fait un make Entity #}

<?php

class CommentType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('content', null, [
            "label" =>"votre commentaire"
        ])
        ->add('commenter', SubmitType::class, [
            "attr" =>  ["class" => "bg-danger text-white"]
        ]);
    }
}

