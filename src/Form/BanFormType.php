<?php

namespace App\Form;

use App\Entity\Visitor;
use App\Util\SecurityUtil;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * VisitorManagerController constructor.
     *
     * @param SecurityUtil    $securityUtil
     */
    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Builds the form for banning a visitor.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string, mixed> $options The options for building the form.
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
        ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'preSubmitData'])
        ;
    }

    /**
     * Handles form submission, escaping insecure characters in inputs field.
     *
     * @param FormEvent $event The form event.
     */
    public function preSubmitData(FormEvent $event): void
    {
        $formData = $event->getData();

        // escape input
        if (isset($formData['ban_reason'])) {
            $formData['ban_reason'] = $this->securityUtil->escapeString($formData['ban_reason']);
        }

        // set the updated data back to the form
        $event->setData($formData);
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
