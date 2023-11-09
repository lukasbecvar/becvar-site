<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/*
    Profile pic change form provides the profile picture change in the account settings
*/

class ProfilePicChangeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('profile-pic', FileType::class, [
            'label' => false,
            'multiple' => false,
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please add image/s',
                ])
            ],
            'attr' => [
                'class' => 'form-control bg-dark profile-pic-change',
                'placeholder' => 'Profile picture',
                'accept' => 'image/*',
                'image_property' => 'image'
            ],
            'translation_domain' => false
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
