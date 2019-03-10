<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CashboxType extends AbstractType
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
                $this->router->generate('cashbox_new'):
                $this->router->generate('cashbox_edit', ['cashboxId' => $options['data']['id']])

            )
            ->add('name', TextType::class, ['label' => false])
            ->add('balance', TextType::class, [
                'label' => false,
                'attr' => [
                    'readonly' => true,
                ],
            ])
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
