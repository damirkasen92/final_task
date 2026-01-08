<?php
namespace App\Form;

use App\Entity\Inventory;
use App\Form\DataTransformer\TagsTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryConflictType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageUrl', TextType::class, [
                'required' => false,
            ])
            // ->add('writers', EntityType::class, [
            //     'class'        => User::class,
            //     'choice_label' => 'id',
            //     'multiple'     => true,
            // ])
            ->add('tags', TextType::class, [
                'required' => false,
            ])
        ;

        $builder->get('tags')->addModelTransformer(new TagsTransformer($this->em));
    }

    public function getParent(): string
    {
        return InventoryType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inventory::class,
        ]);
    }
}
