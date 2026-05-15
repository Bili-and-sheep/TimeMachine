<?php

namespace App\Form;

use App\Entity\OperatingSystem;
use App\Entity\Product;
use App\Entity\ProductType;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productName', TextType::class, ['label' => 'Product Name'])
            ->add('technicalName', TextType::class, ['label' => 'Technical Name'])
            ->add('productType', EntityType::class, [
                'class'        => ProductType::class,
                'choice_label' => 'type',
                'label'        => 'Category',
            ])
            ->add('releaseDate', DateType::class, [
                'label'    => 'Release Date',
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'required' => false,
            ])
            ->add('discontinuedYear', DateType::class, [
                'label'    => 'Year Discontinued',
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'required' => false,
            ])
            ->add('launchOS', EntityType::class, [
                'class'        => OperatingSystem::class,
                'choice_label' => fn(OperatingSystem $os) => $os->getFamily()->value . ' ' . $os->getVersion(),
                'group_by'     => fn(OperatingSystem $os) => $os->getFamily()->value,
                'label'        => 'Launch OS',
                'required'     => false,
                'placeholder'  => '— select —',
            ])
            ->add('lastSupportedOS', EntityType::class, [
                'class'        => OperatingSystem::class,
                'choice_label' => fn(OperatingSystem $os) => $os->getFamily()->value . ' ' . $os->getVersion(),
                'group_by'     => fn(OperatingSystem $os) => $os->getFamily()->value,
                'label'        => 'Last Supported OS',
                'required'     => false,
                'placeholder'  => '— select —',
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
            ])
            ->add('originalPrice', IntegerType::class, ['label' => 'Original Price ($)'])
            ->add('sources', TextareaType::class, [
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
            ->add('imageFiles', FileType::class, [
                'label'       => 'Images',
                'multiple'    => true,
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new All(constraints: [
                        new Image(
                            maxSize: '15M',
                            mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                            mimeTypesMessage: 'Please upload a valid image (JPEG, PNG, or WebP).',
                        ),
                    ]),
                ],
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
