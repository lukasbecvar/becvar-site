<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordChangeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', PasswordType::class, [
            'label' => false,
            'attr' => [
                'class' => 'text-input',
                'autocomplete' => 'username',
                'placeholder' => 'Username',
            ],
            'mapped' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a username',
                ]),
                new Length([
                    'min' => 4,
                    'minMessage' => 'Your username should be at least {{ limit }} characters',
                    'max' => 50,
                ]),
            ],
        ])
        ->add('repassword', PasswordType::class, [
            'label' => false,
            'attr' => [
                'class' => 'text-input',
                'autocomplete' => 'username',
                'placeholder' => 'Username',
            ],
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a username',
                ]),
                new Length([
                    'min' => 4,
                    'minMessage' => 'Your username should be at least {{ limit }} characters',
                    'max' => 50,
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
