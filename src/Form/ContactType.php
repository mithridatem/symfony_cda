<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet', TextType::class,[
                'attr'=>['class'=>'form'],
                'label'=> 'Saisir l\'objet',
                'required'=>false
            ])
            ->add('date', DateType::class,[
                'widget' => 'single_text',
                'attr'=>['class'=>'form'],
                'label'=> 'Saisir la date',
                'required'=>false
            ])
            ->add('contenu', TextareaType::class,[
                'attr'=>['class'=>'form'],
                'label'=> 'Saisir le contenu',
                'required'=>false
            ])
            ->add('nom', TextType::class,[
                'attr'=>['class'=>'form'],
                'label'=> 'Saisir votre nom',
                'required'=>false
            ])
            ->add('prenom', TextType::class,[
                'attr'=>['class'=>'form'],
                'label'=> 'Saisir votre prénom',
                'required'=>false
            ])
            ->add('mail', EmailType::class,[
                'attr'=>['class'=>'form'],
                'label'=> 'Saisir votre mail',
                'required'=>false
            ])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
