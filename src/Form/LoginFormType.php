<?php

namespace App\Form;

use App\Entity\User;
use App\Util\SecurityUtil;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class LoginFormType
 * 
 * Login form provides an admin accounts authenticator.
 *
 * @see AbstractType
 * 
 * @package App\Form
 */
class LoginFormType extends AbstractType
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
     * Builds the login form.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string> $options The options for building the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'text-input',
                'placeholder' => 'Username',
            ],
            'mapped' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a username',
                ])
            ],
            'translation_domain' => false
        ])
        ->add('password', PasswordType::class, [
            'label' => false,
            'attr' => [
                'class' => 'text-input',
                'placeholder' => 'Password',
            ],
            'mapped' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ])
            ],
            'translation_domain' => false
        ])
        ->add('remember', CheckboxType::class, [
            'label' => 'Remember me',
            'attr' => [
                'class' => 'checkbox',
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
        if (isset($formData['username'])) {
            $formData['username'] = $this->securityUtil->escapeString($formData['username']);
        }

        if (isset($formData['password'])) {
            $formData['password'] = $this->securityUtil->escapeString($formData['password']);
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
            'data_class' => User::class,
        ]);
    }
}
