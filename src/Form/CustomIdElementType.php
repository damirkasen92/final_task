<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomIdElementType extends AbstractType
{
    const int MAXIMUM_ZEROES_PREFIX = 20;

    public function __construct(
        private TranslatorInterface $translator
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices'     => [
                    'Fixed text'     => 'fixed',
                    '20-bit random'  => 'rand20',
                    '32-bit random'  => 'rand32',
                    '6-digit random' => 'rand6',
                    '9-digit random' => 'rand9',
                    'GUID'           => 'guid',
                    'Date/time'      => 'date',
                    'Sequence'       => 'seq',
                ],
                'constraints' => [
                    new Assert\Callback(fn($type, ExecutionContextInterface $context) =>
                        $this->validateForm($type, $context)
                    ),
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    return match ($value) {
                        'fixed'  => ['data-description' => $this->translator->trans('custom_id.static')],
                        'rand20' => ['data-description' => $this->translator->trans('custom_id.random')],
                        'rand32' => ['data-description' => $this->translator->trans('custom_id.random')],
                        'rand6'  => ['data-description' => $this->translator->trans('custom_id.random')],
                        'rand9'  => ['data-description' => $this->translator->trans('custom_id.random')],
                        'guid'   => ['data-description' => $this->translator->trans('custom_id.uuid')],
                        'date'   => ['data-description' => $this->translator->trans('custom_id.date')],
                        'seq'    => ['data-description' => $this->translator->trans('custom_id.seq')],
                        default  => [],
                    };
                },
            ])
            ->add('value', TextType::class, [
                'required' => false,
            ]);

    }

    private function validateForm($type, ExecutionContextInterface $context)
    {
        /** @var \Symfony\Component\Form\FormInterface $form */
        $form  = $context->getObject()->getParent();
        $value = $form->get('value')->getData();
        $label = array_search(
            $type,
            $form->get('type')->getConfig()->getOption('choices'),
            true
        );

        if (
            preg_match('/fixed/', $type)
        ) {
            if (empty($value)) {
                $context->buildViolation(
                    $this->translator->trans('custom_id.validation.fixed')
                )
                    ->addViolation();
            }

        } else if (
            preg_match('/guid|seq/', $type)
            && ! preg_match('/^([-_]?)$/', $value)
        ) {
            $context->buildViolation(
                $this->translator->trans('custom_id.validation.suffix', [
                    '%type%' => $label,
                ])
            )
                ->addViolation();
        } else if (
            preg_match('/date/', $type)
            && ! preg_match('/^(?:yyyy|dd|ddd|mm)+(?:[-_]{1}(?:yyyy|dd|ddd|mm))*([-_]?)$/', $value)
        ) {
            $context->buildViolation(
                $this->translator->trans('custom_id.validation.date', [
                    '%value%' => $value,
                    '%type%'  => $label,
                ])
            )
                ->addViolation();
        } else if (
            preg_match('/rand20|rand32|rand6|rand9/', $type)
        ) {
            preg_match('/^(?:X|D|O|B)(\d+)([-_]?)$/', $value, $m);

            if (\count($m) === 0) {
                $context->buildViolation(
                    $this->translator->trans('custom_id.validation.rand.format', [
                        '%value%' => $value,
                        '%type%'  => $label,
                    ])
                )
                    ->addViolation();
            }

            if (isset($m[1]) && $m[1] > self::MAXIMUM_ZEROES_PREFIX) {
                $context->buildViolation(
                    $this->translator->trans('custom_id.validation.rand.format', [
                        '%number%' => $m[1],
                        '%type%'   => $label,
                    ])
                )
                    ->addViolation();
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
