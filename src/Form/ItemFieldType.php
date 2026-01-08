<?php
namespace App\Form;

use App\Entity\ItemField;
use App\Enum\ItemFieldTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class ItemFieldType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'       => $this->translator->trans('item_field.create.title'),
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Type('string'),
                    new Length(min: 1, max: 255),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'       => $this->translator->trans('item_field.create.description'),
                'required'    => false,
                'attr'        => [
                    'class' => 'form-control markdown-editor',
                    'rows'  => 10,
                ],
                'constraints' => [
                    new Type('string'),
                ],
            ])
            ->add('isDisplayed', CheckboxType::class, [
                'label'      => $this->translator->trans('item_field.create.is_displayed'),
                'required'   => false,
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
            ])
            ->add('type', EnumType::class, [
                'label' => $this->translator->trans('item_field.create.type'),
                'class' => ItemFieldTypes::class,
            ])
            ->add('Submit', SubmitType::class, [
                'label' => $this->translator->trans('forms.submit'),
                'attr'  => [
                    'class' => 'btn-dark',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemField::class,
        ]);
    }
}
