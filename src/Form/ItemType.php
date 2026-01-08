<?php
namespace App\Form;

use App\Entity\Item;
use App\Entity\ItemField;
use App\Repository\InventoryRepository;
use App\Repository\ItemFieldRepository;
use App\Service\FileStorage\FileStorageInterface;
use App\Service\Item\CustomIdGenerator;
use App\Service\Regexp\RegexpBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ItemType extends AbstractType
{
    private $customIdElements;

    public function __construct(
        private InventoryRepository $inventoryRepository,
        private ItemFieldRepository $itemFieldRepository,
        private CustomIdGenerator $customIdGenerator,
        private RegexpBuilder $regexpBuilder,
        private FileStorageInterface $fileStorage,
        private TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->customIdElements = $this->getCustomIdElements($options);

        $builder
            ->add('customId', TextType::class, [
                'data' => $this->getCustomId($this->customIdElements),
                'constraints' => [
                    new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                        $regex = $this->regexpBuilder->buildRegex($this->customIdElements);

                        if (!preg_match($regex, $value)) {
                            $context->buildViolation($this->translator->trans('custom_id.check') . ': ' . $value)->addViolation();
                        }
                    }),
                ],
            ]);

        $this->addDynamicFields($builder, $options);

        $builder->add('submit', SubmitType::class, [
            'label' => $this->translator->trans('forms.submit'),
            'attr' => ['class' => 'btn btn-dark'],
        ]);
    }

    private function getCustomIdElements(array $options): ?array
    {
        if ($options['inventory'] === null) {
            return [];
        }

        return $this->inventoryRepository->find($options['inventory'])
            ->getCustomIdFormat();
    }

    private function getCustomId($elements): string
    {
        return $this->customIdGenerator->generate($elements);
    }

    private function addDynamicFields(FormBuilderInterface $builder, array $options): void
    {
        $itemFields = $this->itemFieldRepository->findBy([
            'inventory' => $options['inventory'],
        ], [
            'orderIndex' => 'asc',
        ]);

        $item = $options['data'] ?? [];

        /** @var ItemField $itemField */
        foreach ($itemFields as $itemField) {
            $builder->add(
                $itemField->getSlot(),
                $this->getTypeFromItemFieldType($itemField->getType()->value),
                $this->getOptionsForItemField($itemField->getType()->value, $item, $itemField),
            );
        }
    }

    private function getOptionsForItemField($type, ?Item $item, ?ItemField $itemField): array
    {
        return match ($type) {
            'text' => [
                'required' => false,
                'attr' => [
                    'data-controller' => 'ui--markdown',
                    'data-ui--markdown-target' => 'textarea',
                ],
            ],
            'link' => [
                'required' => false,
                'attr' => [
                    'data-preview-url' => $this->getLink($item, $itemField),
                ],
            ],
            default => [
                'required' => false,
            ],
        };
    }

    private function getLink(?Item $item, ?ItemField $itemField): string|null
    {
        if (!$item || !$itemField) {
            return null;
        }

        $link = $item->{'get' . ucfirst($itemField->getSlot())}();

        if (\is_null($link)) {
            return null;
        }

        return $this->fileStorage->getFileUrl($link);
    }

    private function getTypeFromItemFieldType(string $type): string
    {
        return match ($type) {
            'string' => TextType::class,
            'text' => TextareaType::class,
            'integer' => IntegerType::class,
            'link' => GoogleFileType::class,
            'bool' => CheckboxType::class,
        };
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
            'inventory' => null,
        ]);
    }
}
