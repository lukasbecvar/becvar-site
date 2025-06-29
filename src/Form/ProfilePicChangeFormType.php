<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class ProfilePicChangeFormType
 *
 * Form provides changing profile picture in the account settings
 *
 * @extends AbstractType<User>
 *
 * @package App\Form
 */
class ProfilePicChangeFormType extends AbstractType
{
    /**
     * Build profile picture change form
     *
     * @param FormBuilderInterface<User|null> $builder The form builder
     * @param array<string> $options The options for building the form
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('profile-pic', FileType::class, [
            'label' => false,
            'multiple' => false,
            'mapped' => false,
            'constraints' => new NotBlank(message: 'Please add image/s'),
            'attr' => [
                'class' => 'file-input-control profile-pic-change',
                'placeholder' => 'Profile picture',
                'accept' => 'image/*',
                'image_property' => 'image'
            ],
            'translation_domain' => false
        ]);
    }

    /**
     * Configure options for profile picture change form
     *
     * @param OptionsResolver $resolver The resolver for the form options
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
