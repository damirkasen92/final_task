<?php
namespace App\Form;

use App\Entity\Item;
use App\Entity\ItemField;
use App\Repository\InventoryRepository;
use App\Repository\ItemFieldRepository;
use App\Service\Item\CustomIdGenerator;
use App\Service\Regexp\RegexpBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ItemType extends AbstractType
{
    public function __construct(
        private InventoryRepository $inventoryRepository,
        private ItemFieldRepository $itemFieldRepository,
        private CustomIdGenerator $customIdGenerator,
        private RegexpBuilder $regexpBuilder
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customId', TextType::class, [
                'data'        => $this->getCustomId($options),
                'constraints' => [
                    new Assert\Callback(function ($value, ExecutionContextInterface $context) use ($options) {
                        $elements = $this->getCustomIdElements($options);
                        $regex = $this->regexpBuilder->buildRegex($elements);

                        if (! preg_match($regex, $value)) {
                            $context->buildViolation('Неверный формат custom ID: ' . $value)->addViolation();
                        }
                    }),
                ],
            ]);

        $this->addDynamicFields($builder, $options);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Submit',
            'attr'  => ['class' => 'btn btn-primary'],
        ]);
    }

    private function getCustomIdElements(array $options)
    {
        return $this->inventoryRepository->find($options['inventory'])
            ->getCustomIdFormat();
    }

    private function getCustomId($options)
    {
        return $this->customIdGenerator->generate($this->getCustomIdElements($options));
    }

    private function addDynamicFields(FormBuilderInterface $builder, array $options)
    {
        $itemFields = $this->itemFieldRepository->findBy([
            'inventory' => $options['inventory'],
        ]);

        /** @var ItemField $itemField */
        foreach ($itemFields as $itemField) {
            $builder->add(
                $itemField->getSlot(),
                $this->getTypeFromItemFieldType($itemField->getType()->value),
                [
                    'required' => true,
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            );
        }
    }

    private function getTypeFromItemFieldType(string $type)
    {
        return match ($type) {
            'string'  => TextType::class,
            'text'    => TextareaType::class,
            'integer' => IntegerType::class,
            'link'    => UrlType::class,
            'bool'    => CheckboxType::class,
        };
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
            'inventory'  => null,
        ]);
    }
}
