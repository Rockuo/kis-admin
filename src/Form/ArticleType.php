<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('basics', ArticleBasicsType::class, [
                'label' => false,
                'data' => [
                    'name' => $options['data']['name'],
                    'unit' => $options['data']['unit'],
                    'beer_keg' => isset($options['data']['beer_keg']) && $options['data']['beer_keg'] ? $options['data']['beer_keg'] : null,
                ],
                'inheritable_kegs' => $options['inheritable_kegs'],
                'empty_kegs' => $options['empty_kegs']
            ])
            ->add('image', FileType::class, ['required' => false])
            ->add('labels', ChoiceType::class, [
                'choices' => $options['labelsAll'],
                'multiple' => true,
                'required' => false,
                'expanded' => true,
            ])
            ->add('components', CollectionType::class, [
                'entry_type' => ArticleComponentType::class,
                'allow_add' => true,
                'entry_options' => ['label' => false, 'allArticles' => $options['allArticles'], 'attr' => ['data-selector' => 'collectionInput']],
            ])
            ->add('tariffs', CollectionType::class, [
                'entry_type' => ArticleTariffType::class,
                'allow_add' => true,
                'entry_options' => ['label' => false, 'attr' => ['data-selector' => 'collectionInput']],
            ])
            ->add('save', SubmitType::class, ['label' => 'Upravit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'labelsAll' => [],
            'allArticles' => [],
            'inheritable_kegs' => null,
            'empty_kegs' => null,
        ]);
    }
}
