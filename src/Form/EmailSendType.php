<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class EmailSendType
 *
 * Form type for sending emails.
 *
 * @see AbstractType
 *
 * @package App\Form
 */
class EmailSendType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string> $options The options for this form
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email recipient',
                    'class' => 'text-input',
                    'maxlength' => 255
                ],
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                    new Length(['max' => 255]),
                ],
            ])
            ->add('subject', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Subject',
                    'class' => 'text-input',
                    'maxlength' => 255
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Message',
                    'class' => 'text-input',
                    'maxlength' => 10000
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 10000]),
                ],
            ]);
    }
}
