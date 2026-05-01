<?php

namespace App\Form;

use App\Entity\OperatingSystem;
use App\Entity\Product;
use App\Entity\ProductType;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ProductName', TextType::class, ['label' => 'Product Name'])
            ->add('TechnicalName', TextType::class, ['label' => 'Technical Name'])
            ->add('ProductType', EntityType::class, [
                'class'        => ProductType::class,
                'choice_label' => 'type',
                'label'        => 'Category',
            ])
            ->add('ReleaseDate', DateType::class, [
                'label'  => 'Release Date',
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'required' => false,
            ])
            ->add('DiscontinuedYear', DateType::class, [
                'label'  => 'Year Discontinued',
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'required' => false,
            ])
            ->add('LaunchOS', EntityType::class, [
                'class'        => OperatingSystem::class,
                'choice_label' => fn(OperatingSystem $os) => $os->getFamily()->value . ' ' . $os->getVersion(),
                'group_by'     => fn(OperatingSystem $os) => $os->getFamily()->value,
                'label'        => 'Launch OS',
                'required'     => false,
                'placeholder'  => '— select —',
            ])
            ->add('LastSupportedOS', EntityType::class, [
                'class'        => OperatingSystem::class,
                'choice_label' => fn(OperatingSystem $os) => $os->getFamily()->value . ' ' . $os->getVersion(),
                'group_by'     => fn(OperatingSystem $os) => $os->getFamily()->value,
                'label'        => 'Last Supported OS',
                'required'     => false,
                'placeholder'  => '— select —',
            ])
            ->add('Description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
            ])
            ->add('OriginalPrice', IntegerType::class, ['label' => 'Original Price ($)'])
            ->add('Sources', TextareaType::class, [
                'label'    => 'Sources',
                'required' => false,
            ])
            ->add('tags', EntityType::class, [
                'class'        => Tag::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'expanded'     => true,
                'label'        => 'Tags',
                'required'     => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
