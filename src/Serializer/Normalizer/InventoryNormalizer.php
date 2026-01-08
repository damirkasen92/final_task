<?php
namespace App\Serializer\Normalizer;

use App\Entity\Inventory;
use App\Service\FileStorage\FileStorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class InventoryNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private FileStorageInterface $fileStorage
    ) {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        if ($data['id']) {
            unset($data['id']);
        }

        if ($data['imageUrl']) {
            $data['imageUrl'] = [$data['imageUrl'], $this->fileStorage->getFileUrl($data['imageUrl'])];
        }

        if ($data['tags']) {
            $ids = array_map(fn($tag) => $tag['id'], $data['tags']);
            $names = array_map(fn($tag) => $tag['name'], $data['tags']);

            $data['tags'] = [
                'ids' => implode(',', $ids),
                'names' => implode(',', $names),
            ];
        }

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Inventory;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Inventory::class => true];
    }
}
