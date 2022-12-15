<?php

namespace App\Form;

use App\Entity\Topic;
use App\Entity\Auteur;
use App\Entity\Categorie;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TopicType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('titre',TextType::class)
      ->add('verouille', ChoiceType::class, [
        'required' => true,
        'label' => 'Voulez-vous verrouiller ce topic ?',
        'choices' => [
            'oui' => true,
            'non' => false
        ],
        'multiple' => false,
        'expanded' => true,
      ])
      ->add('resolu', ChoiceType::class, [
        'required' => true,
        'label' => 'Voulez-vous signifier ce topic comme étant résolu ?',
        'choices' => [
            'oui' => true,
            'non' => false
        ],
        'multiple' => false,
        'expanded' => true,
      ])
      ->add('date_publication',DateType::class, [
        'widget' => 'single_text'
      ])
      ->add('submit', SubmitType::class, [
        'attr' => ['class' => 'btn']
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Topic::class,
    ]);
  }
}
