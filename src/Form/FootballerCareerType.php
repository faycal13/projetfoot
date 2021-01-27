<?php

namespace App\Form;

use App\Entity\FootballerCarrer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FootballerCareerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('club', TextType::class, [
                'required' => true,
            ])
            ->add('startDate', TextType::class, [
                'required' => true,
            ])
            ->add('endDate', TextType::class, [
                'required' => true,
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
            ->add('categorie', ChoiceType::class, [
                'choices'  => [
                    'Ligue 1' => 'Ligue 1',
                    'Ligue 2' => 'Ligue 2',
                    'National' => 'National',
                    'National 2' => 'National 2',
                    'National 3' => 'National 3',
                    'Régional' => 'Régional',
                    'Départemental' => 'Départemental',
                    'Vétéran' => 'Vétéran',
                    'U19' => 'U19',
                    'U17' => 'U17',
                    'U15' => 'U15',
                    'U14' => 'U14',
                    'Autre' => 'Autre',
                ],
                'required' => true
            ])
            ->add('city', TextType::class, [
                'required' => true,
            ])
            ->add('latitude', HiddenType::class, [
                'required' => false,
            ])
            ->add('longitude', HiddenType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FootballerCarrer::class,
        ]);
    }
}
