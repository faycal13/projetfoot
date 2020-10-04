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

            ])
            ->add('height', NumberType::class, [

            ])
            ->add('position', ChoiceType::class, [
                'choices'  => [
                    'Gardien' => 'Gardien',
                    'Défenseur' => 'Défenseur',
                    'Milieu' => 'Milieu',
                    'Attaquant' => 'Attaquant',
                ],
                'required' => true
            ])
            ->add('betterFoot', ChoiceType::class, [
                'choices'  => [
                    'Droit' => 'Droit',
                    'Gauche' => 'Gauche',
                    'Les deux' => 'Les deux',
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
