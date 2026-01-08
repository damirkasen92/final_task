<?php
namespace App\Form;

use App\Form\DataTransformer\FileToStringTransformer;
use App\Service\FileStorage\FileStorageInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GoogleFileType extends AbstractType
{
    public function __construct(
        private FileStorageInterface $storage,
        private ValidatorInterface $validator,
        private FileToStringTransformer $transformer

    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function getParent(): string
    {
        return FileType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choose_file_text' => 'Choose file',
        ]);
    }
}
