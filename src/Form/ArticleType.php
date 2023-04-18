<?php
namespace App\Form;
use App\Entity\Article;
use App\Entity\User;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class,[
                'attr'=>['class'=>'form'],
                'required'=>true
            ])
            ->add('contenu', TextareaType::class)
            ->add('date', DateType::class)
            ->add('categories', EntityType::class,
                [
                // looks for choices from this entity
                'class' => Categorie::class,
                'label' => 'Sélectionner des Catégories :',
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
                'required' => true
                ]
            )
            ->add('user', EntityType::class, 
                [
                // looks for choices from this entity
                'class' => User::class,
                'label' => 'Utilisateurs :'
                ]
                )
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
