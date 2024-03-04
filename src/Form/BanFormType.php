<?php

namespace App\Form;

use App\Entity\Visitor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class BanFormType
 * 
 * Ban form provides a visitor ban form with a reason value.
 *
 * @see AbstractType
 * 
 * @package App\Form
 */
class BanFormType extends AbstractType
{
    /**
     * Builds the form for banning a visitor.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string> $options The options for building the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('ban_reason', TextareaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'todo-area feedback-input',
                'placeholder' => 'Reason',
                'maxlength' => 120
            ],
            'required' => false,
            'mapped' => true,
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
            'data_class' => Visitor::class,
        ]);
    }
}
