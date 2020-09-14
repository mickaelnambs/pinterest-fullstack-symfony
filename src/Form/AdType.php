<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Category;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class , $this->getConfiguration("Titre", "Titre de votre annonce ..."))
            ->add('category', EntityType::class, $this->getConfiguration("Catégorie", false, ['class' => Category::class, 'choice_label' => 'title']))
            ->add('images', FileType::class, $this->getConfiguration("Photos", "Les photos ...", ['mapped' => false, 'multiple' => true, 'required' => false]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
