<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddToCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $current_quantity = $options['current_quantity'] ?? 1;
        $builder
            ->add('quantity', ChoiceType::class, [
                'choices' => range(0, 10), // menu déroulant 0 à 10
                'data' => ($current_quantity === 0) ? 1 : $current_quantity, // valeur présélectionnée
                'label' => 'Quantité',
            ])
            ->add('addToCart', SubmitType::class, [
                'label' => ($current_quantity === 0) ? 'Ajouter au panier' : 'Mettre à jour',
                'attr' => [
                    'class' => 'submit-button',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'current_quantity' => 1, // Valeur par défaut
        ]);
    }
}
