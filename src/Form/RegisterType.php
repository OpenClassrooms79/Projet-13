<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use function mb_strtolower;

class RegisterType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // récupération de la définition de l'entité User
        $metadata = $this->entityManager->getClassMetadata($builder->getDataClass());

        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Length([
                        'max' => $metadata->getFieldMapping('firstname')->length,
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Length([
                        'max' => $metadata->getFieldMapping('lastname')->length,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new Length([
                        'max' => $metadata->getFieldMapping('email')->length,
                    ]),
                    new Email(),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'max' => $metadata->getFieldMapping('password')->length,
                    ]),
                    new NotCompromisedPassword(),
                    new Callback([$this, 'validatePassword']),
                ],
            ])->add('cguAccepted', CheckboxType::class, [
                'mapped' => false,
                'label' => "J'accepte les CGU de GreenGoodies",
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les Conditions Générales d\'Utilisation.',
                    ]),
                ],
            ])
            ->add('registerSubmit', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => [
                    'class' => 'submit-button',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function validatePassword($password, ExecutionContextInterface $context): void
    {
        $user = $context->getRoot()->getData(); // récupère l'objet User

        if (!$user instanceof User) {
            return; // s'assurer que l'on traite bien un objet User
        }

        $firstname = mb_strtolower($user->getFirstName());
        $lastname = mb_strtolower($user->getLastName());
        $email = mb_strtolower($user->getEmail());
        $password = mb_strtolower($password);

        if (in_array($password, [$email, $firstname, $lastname], true)) {
            $context
                ->buildViolation('Le mot de passe ne peut pas être identique à votre prénom, votre nom ou votre adresse e-mail.')
                ->atPath('password')
                ->addViolation();
        }
    }
}
