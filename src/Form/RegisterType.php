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

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gamification_consent', CheckboxType::class, ['required' => false])
            ->add('nickname', TextType::class,['required' => false])
            ->add('load', ButtonType::class, ['label' => 'Přečíst ID', 'attr' => ['style' => 'width:200px;height:200px;']])

            ->add('rfid', HiddenType::class,['required' => false])
            ->add('session_id', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => 'Registrovat']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
