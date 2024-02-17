<?php

namespace App\Form;

use App\Entity\User;
use App\Util\SecurityUtil;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class PasswordChangeFormType
 * 
 * PasswordChangeFormType provides a form for changing the password in the account settings.
 *
 * @see AbstractType
 * 
 * @package App\Form
 */
class PasswordChangeFormType extends AbstractType
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
        if (isset($formData['password'])) {
            $formData['password'] = $this->securityUtil->escapeString($formData['password']);
        }

        if (isset($formData['repassword'])) {
            $formData['repassword'] = $this->securityUtil->escapeString($formData['repassword']);
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
