<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uuid')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User'     => Role::User->value,
                    'Reviewer' => Role::Reviewer->value,
                    'Barista'  => Role::Barista->value,
                    'Admin'    => Role::Admin->value,
                ],
                'expanded' => true,
                'multiple' => true,
                'label'    => 'Role',
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
