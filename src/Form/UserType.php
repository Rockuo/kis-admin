<?php

namespace App\Form;

use App\Service\ApiMiddleware;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('gamification_consent', CheckboxType::class, ['required' => false])
            ->add('name')
            ->add('nickname', TextType::class,['required' => false])
            ->add('role', ChoiceType::class, ['choices' => array_flip(ApiMiddleware::USER_ROLES)])
            ->add('save', SubmitType::class, ['label' => 'Upravit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
