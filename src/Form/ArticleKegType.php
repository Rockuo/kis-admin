<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleKegType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('empty_keg', ChoiceType::class, [
                'choices' => array_merge(['Nepřiřazeno'=>''], $options['empty_kegs']),
                'required' => false,
            ])
            ->add('inherit_products', ChoiceType::class, [
                'choices' => array_merge(['Nepřiřazeno'=>''], $options['inheritable_kegs']),
                'required' => false,
            ])
            ->add('volume', NumberType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'inheritable_kegs' => null,
            'empty_kegs' => null,
        ]);
    }
}
