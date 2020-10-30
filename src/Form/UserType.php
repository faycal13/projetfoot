<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
            ])
            ->add('dateOfBirth', DateType::class, [
                // renders it as a single text box
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('sexe', ChoiceType::class, array(
                'choices' => array(
                    'Homme' => 'H',
                    'Femme' => 'F',
                ),
                'expanded' => true,
                'required' => true,
            ))
            ->add('account',AccountType::class, array(
                'label' => ' ',
                'required' => false,
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
