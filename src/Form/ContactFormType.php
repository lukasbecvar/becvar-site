<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control mt-3',
                'placeholder' => 'Your name',
            ],
            'mapped' => true
        ])
        ->add('email', EmailType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control mt-3',
                'placeholder' => 'Your Email',
            ],
            'mapped' => true
        ])
        ->add('message', TextareaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control resize-disable mt-3',
                'placeholder' => 'Message',
                'maxlength' => 1024
            ],
            'mapped' => true
        ])
        ->add('websiteIN', TextareaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'websiteIN',
                'placeholder' => 'Website',
            ],
            'mapped' => false,
            'required' => false
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
