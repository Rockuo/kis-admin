<?php

namespace App\Form;

use App\Service\ApiMiddleware;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
//            ->add('role', ChoiceType::class, ['choices' => array_flip(ApiMiddleware::USER_ROLES)])
//            ->add('load', ButtonType::class, ['label' => 'Přečít ID'])
            ->add('rfid', HiddenType::class,['required' => false]);

        if($options['me'])
        {
            $builder->add('pin', TextType::class, ['required' => false]);
        }
        $builder->add('save', SubmitType::class, ['label' => 'Upravit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'me' => false
        ]);
    }
}
