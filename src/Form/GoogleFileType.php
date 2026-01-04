<?php

namespace App\Form;

use App\Service\Google\GoogleStorageService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GoogleFileType extends AbstractType
{
    public function __construct(private GoogleStorageService $storage, private ValidatorInterface $validator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            fn ($modelValue): null => null,
            fn ($formValue): ?string => match (true) {
                $formValue instanceof UploadedFile => $this->validateUploadedFile($formValue, $options),
                \is_string($formValue) => $formValue,
                \is_null($formValue) => $options['default_image'],
                default => null,
            }));
    }

    private function validateUploadedFile($formValue, $options) {
        $violations = $this->validator->validate($formValue, $options['file_constraints']);

        if ($violations->count() > 0) {
            throw new TransformationFailedException((string) $violations);
        }

        return $this->storage->upload($formValue);
    }

    public function getParent(): string
    {
        return FileType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'default_image' => null,
            'file_constraints' => [
                new File(
                    maxSize: '5M',
                    mimeTypes: ['image/jpeg', 'image/png', 'image/webp']
                ),
            ],
        ]);
    }
}
