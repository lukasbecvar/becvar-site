<?php

namespace App\Form;

use App\Entity\Message;
use App\Util\SecurityUtil;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class ContactFormType
 * 
 * Contact form provides a contact message form.
 *
 * @see AbstractType
 * 
 * @package App\Form
 */
class ContactFormType extends AbstractType
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
     * Builds the form for contacting.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string> $options The options for building the form.
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

        // escape inputs
        if (isset($formData['name'])) {
            $formData['name'] = $this->securityUtil->escapeString($formData['name']);
        }

        if (isset($formData['email'])) {
            $formData['email'] = $this->securityUtil->escapeString($formData['email']);
        }

        if (isset($formData['message'])) {
            $formData['message'] = $this->securityUtil->escapeString($formData['message']);
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
            'data_class' => Message::class,
        ]);
    }
}
