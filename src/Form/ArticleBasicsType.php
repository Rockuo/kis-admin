<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleBasicsType extends AbstractType
{

    /*
     * {
          "beer_keg": {
            "empty_keg": 0,
            "inherit_products": 0,
            "volume": 0
          },
          "name": "string",
          "unit": "string"
       }
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('unit')
            //->add('beer_keg', ArticleKegType::class) TODO
        ;

        if ($options['submit']) {
            $builder->add('save', SubmitType::class, ['label' => 'Vytvořit']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'submit' => false,
        ]);
    }
}
