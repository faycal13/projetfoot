<?php

namespace App\Form;

use App\Entity\Footballer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FootballerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
                'required' => true,
            ])
            ->add('goal', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
                'required' => true
            ])
            ->add('weight', NumberType::class, [
                'label' => false
            ])
            ->add('height', NumberType::class, [
                'label' => false
            ])
            ->add('position', ChoiceType::class, [
                'choices'  => [
                    'Gardien' => 'Gardien',
                    'Défenseur' => 'Defenseur',
                    'Milieu' => 'Milieu',
                    'Attaquant' => 'Attaquant',
                ],
                'required' => true
            ])
            ->add('betterFoot', ChoiceType::class, [
                'choices'  => [
                    'Droit' => 'D',
                    'Gauche' => 'G',
                    'Les deux' => 'DG',
                ],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Footballer::class,
        ]);
    }
}
