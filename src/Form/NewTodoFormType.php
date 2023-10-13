<?php

namespace App\Form;

use App\Entity\Todo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewTodoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('text', TextareaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'todo-area feedback-input',
                'placeholder' => 'Todo',
                'maxlength' => 120
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a todo text',
                ])
            ],
            'required' => true,
            'mapped' => true,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Todo::class,
        ]);
    }
}
