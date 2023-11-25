<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * PasswordChangeFormType provides a form for changing the password in the account settings.
 *
 * @see AbstractType
 */
class PasswordChangeFormType extends AbstractType
{
    /**
     * Builds the password change form.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string, mixed> $options The options for building the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', PasswordType::class, [
            'label' => false,
            'attr' => [
                'class' => 'text-input',
                'autocomplete' => 'password',
                'placeholder' => 'password',
            ],
            'mapped' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    'max' => 50,
                ]),
            ],
            'translation_domain' => false
        ])
        ->add('repassword', PasswordType::class, [
            'label' => false,
            'attr' => [
                'class' => 'text-input',
                'autocomplete' => 'repassword',
                'placeholder' => 're password',
            ],
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a repassword',
                ]),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    'max' => 50,
                ]),
            ],
            'translation_domain' => false
        ])
        ;
    }

    /**
     * Configures the options for this form.
     *
     * @param OptionsResolver $resolver The resolver for the form options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
