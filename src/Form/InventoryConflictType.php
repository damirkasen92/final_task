<?php
namespace App\Form;

use App\Entity\Inventory;
use App\Entity\Tag;
use App\Form\DataTransformer\TagsTransformer;
use App\Service\FileStorage\FileStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Choice;

class InventoryConflictType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $em,
        private FileStorageInterface $fileStorage,
        private RouterInterface $router
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addDynamicFields($options['currentData'], $options['dbData'], $builder);
        $builder->add('submit', SubmitType::class, [
            'label' => 'forms.submit',
            'attr' => ['class' => 'btn btn-dark'],
        ])
            ->setAction(
                $this->router->generate('inventory_conflict', [
                    'id' => $options['currentData']->getId()
                ])
            )
            ->setMethod('POST');
    }

    private function addDynamicFields(Inventory $current, Inventory $db, FormBuilderInterface $builder)
    {
        foreach (['title', 'description', 'category', 'imageUrl', 'tags'] as $property) {
            $getter = 'get' . ucfirst($property);

            $currentValue = $current->{$getter}();
            $dbValue = $db->{$getter}();

            if (!$this->checkForEquality($property, $currentValue, $dbValue)) {

                if ($property === 'imageUrl') {

                    $builder->add($property, ChoiceType::class, [
                        'choices' => [
                            $currentValue => $currentValue,
                            $dbValue => $dbValue,
                        ],
                        'choice_attr' => [
                            $currentValue => [
                                'data-image-url' => $this->fileStorage->getFileUrl($currentValue),
                            ],
                            $dbValue => [
                                'data-image-url' => $this->fileStorage->getFileUrl($dbValue),
                            ]
                        ],
                        'expanded' => true
                    ]);

                } else if ($property === 'tags') {

                    $builder->add($property, ChoiceType::class, [
                        'choices' => [
                            ...$this->transformTags($currentValue->toArray()),
                            ...$this->transformTags($dbValue->toArray()),
                        ],
                        'expanded' => true,
                    ]);

                    // $builder->get($property)->addModelTransformer(new TagsTransformer($this->em));
                } else if ($property === 'category') {

                    $builder->add($property, ChoiceType::class, [
                        'choices' => [
                            $currentValue->getTitle() => $currentValue->getId(),
                            $dbValue->getTitle() => $dbValue->getId(),
                        ],
                        'expanded' => true
                    ]);

                } else {
                    $builder->add($property, ChoiceType::class, [
                        'choices' => [
                            $currentValue => $currentValue,
                            $dbValue => $dbValue,
                        ],
                        'expanded' => true
                    ]);
                }
            }
        }
    }

    private function transformTags(array $tags)
    {
        $labels = implode(', ', array_map(fn(Tag $tag) => $tag->getName(), $tags));
        $ids = implode(', ', array_map(fn(Tag $tag) => $tag->getId(), $tags));
        return [$labels => $ids];
    }

    private function checkForEquality($type, $a, $b)
    {
        return match ($type) {
            'tags' =>
            array_map(fn($e) => $e->getId(), $a->toArray())
            == array_map(fn($e) => $e->getId(), $b->toArray()),
            'category' => $a == $b,
            default => $a === $b,
        };
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inventory::class,
            'currentData' => null,
            'dbData' => null,
        ]);
    }
}
