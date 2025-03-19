<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiAccessType extends AbstractType
{
    public function __construct(private Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        if ($user->isApiEnabled()) {
            $label = 'Désactiver mon accès API';
        } else {
            $label = 'Activer mon accès API';
        }
        $builder
            ->add('apiAccess', SubmitType::class, [
                'label' => $label,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
