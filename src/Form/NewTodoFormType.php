<?php

namespace App\Form;

use App\Entity\Todo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class NewTodoFormType
 * 
 * NewTodo form provides a form for saving a new todo item.
 *
 * @see AbstractType
 * 
 * @package App\Form
 */
class NewTodoFormType extends AbstractType
{
    /**
     * Builds the new todo form.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string> $options The options for building the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('text', TextareaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'todo-area feedback-input',
                'placeholder' => 'Todo',
                'maxlength' => 240
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a todo text',
                ])
            ],
            'required' => true,
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
            'data_class' => Todo::class,
        ]);
    }
}
