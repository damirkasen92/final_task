<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Inventory;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\DataTransformer\FileToStringTransformer;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryConflictResolvingType extends AbstractType
{
    public function __construct(
        private FileToStringTransformer $fileToStringTransformer,
        private TagRepository $tagRepository
    ) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('imageUrl')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'id',
            ])
            ->add('tags', EntityType::class, [
                'multiple' => true,
                'class' => Tag::class,
                'by_reference' => false,
            ])
            ->add('submit', SubmitType::class);

        $builder->get('imageUrl')->addModelTransformer($this->fileToStringTransformer);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            fn(FormEvent $event) => $this->preSubmit($event)
        );
    }

    private function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        if (isset($data['tags'])) {
            $ids = array_map('trim', explode(',', $data['tags']));
            $data['tags'] = $ids;
        }

        $event->setData($data);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inventory::class,
        ]);
    }
}
