<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Inventory;
use App\Entity\Tag;
use App\Service\Google\GoogleStorageService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryFormType extends AbstractType
{
    public function __construct(
        private GoogleStorageService $googleStorageService,
        private TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['create']) {
            $builder
                ->add('title', TextType::class, [
                    'label' => $this->translator->trans('inventory.create_inventory.title'),
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                        new Type('string'),
                        new Length(min: 1, max: 255),
                    ],
                ])
                ->add('description', TextareaType::class, [
                    'label' => $this->translator->trans('inventory.create_inventory.description'),
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control markdown-editor',
                        'rows' => 10,
                    ],
                    'constraints' => [
                        new Type('string'),
                    ],
                ])
                ->add('category', EntityType::class, [
                    'label' => $this->translator->trans('inventory.create_inventory.category'),
                    'required' => true,
                    'choice_label' => 'title',
                    'class' => Category::class,
                    'autocomplete' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
                ->add('imageUrl', GoogleFileType::class, [
                    'label' => $this->translator->trans('inventory.create_inventory.image_url'),
                    'required' => false,
                    'mapped' => true,
                    'data_class' => null,
                    'default_image' => $options['default_image'],
                ])
                ->add('tags', EntityType::class, [
                    'label' => $this->translator->trans('inventory.create_inventory.tags'),
                    'class' => Tag::class,
                    'autocomplete' => true,
                    'multiple' => true,
                    'tom_select_options' => [
                        'create' => true,
                        'createOnBlur' => true,
                        'delimiter' => ',',
                    ],
                    'required' => false,
                ]);
        }

        if ($options['update']) {
            $builder
                ->add('writers', UserType::class, [
                    'label' => $this->translator->trans('inventory.create_inventory.writers'),
                    'row_attr' => [
                        'class' => 'mb-3 row-writers',
                    ],
                ])
                ->add('isPublic', CheckboxType::class, [
                    'label' => $this->translator->trans('inventory.create_inventory.is_public'),
                    'required' => false,
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ],
                ]);
        }

        $builder
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('forms.submit'),
                'attr' => [
                    'class' => 'btn-dark',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inventory::class,
            'update' => true,
            'create' => true,
            'default_image' => null,
        ]);
    }
}
