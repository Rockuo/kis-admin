<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleBasicsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('unit');
        if ($options['submit']) {
            $builder->add('keg', CheckboxType::class, ['required' => false]);
            $builder->add('save', SubmitType::class, ['label' => 'Vytvořit']);
        }
        elseif ($options['empty_kegs'] !== null && $options['inheritable_kegs'] !== null) {
            $builder
                ->add('beer_keg', ArticleKegType::class, [
                    'inheritable_kegs' => $options['inheritable_kegs'],
                    'empty_kegs' => $options['empty_kegs']
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'submit' => false,
            'inheritable_kegs' => null,
            'empty_kegs' => null,
        ]);
    }
}
