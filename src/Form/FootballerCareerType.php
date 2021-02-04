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
        //Calcul des saisons
        $saisons = [];
        for ($i = 0; $i <= 30; $i++){
            $date = new \DateTime();
            $date2 = new \DateTime();
            $date3 = new \DateTime();
            $date4 = new \DateTime();
            $key = $date2->modify('-'.($i+1).' years')->format('Y').'-'.$date->modify('-'.$i.' years')->format('Y');
            $value = $date3->modify('-'.($i+1).' years')->format('Y').'-'.$date4->modify('-'.$i.' years')->format('Y');
            $saisons[$key] = $value;
        }

        $builder
            ->add('club', TextType::class, [
                'required' => true,
            ])
            ->add('saisonDate', ChoiceType::class, [
                'choices'  => $saisons,
                'required' => true
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
            ->add('goalNumber', TextType::class, [
                'required' => true,
            ])
            ->add('matchNumber', ChoiceType::class, [
                'choices'  => [
                    'Plus de 5' => '5',
                    'Plus de 15' => '10',
                    'Plus de 25' => '25',
                    'Toute la saison' => '40'
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
