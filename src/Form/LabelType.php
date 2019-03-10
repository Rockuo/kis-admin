<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LabelType extends AbstractType
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }




    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($options['create']?
                $this->router->generate('label_new'):
                $this->router->generate('label_edit', ['labelId' => $options['data']['id']])

            )
            ->add('name', TextType::class, ['label' => false])
            ->add('color', TextType::class, ['label' => '#',])
            ->add('save', SubmitType::class, ['label' => $options['create']?'Vytvořit':'Upravit'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'create' => false
        ]);
    }
}
