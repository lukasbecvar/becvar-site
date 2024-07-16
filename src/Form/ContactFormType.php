<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class ContactFormType
 *
 * Contact form provides a contact message form
 *
 * @see AbstractType
 *
 * @package App\Form
 */
class ContactFormType extends AbstractType
{
    /**
     * Builds the form for contacting
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string> $options The options for building the form
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control mt-3',
                'placeholder' => 'Your name',
            ],
            'mapped' => true,
            'translation_domain' => false
        ])
        ->add('email', EmailType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control mt-3',
                'placeholder' => 'Your Email',
            ],
            'mapped' => true,
            'translation_domain' => false
        ])
        ->add('message', TextareaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control resize-disable mt-3',
                'placeholder' => 'Message',
                'maxlength' => 1024
            ],
            'mapped' => true,
            'translation_domain' => false
        ])
        ->add('websiteIN', TextareaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'websiteIN',
                'placeholder' => 'Website',
            ],
            'mapped' => false,
            'required' => false,
            'translation_domain' => false
        ])
        ;
    }

    /**
     * Configures the options for this form
     *
     * @param OptionsResolver $resolver The resolver for the form options
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
