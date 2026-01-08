<?php
namespace App\Service\FileStorage\Google;

use App\Service\FileStorage\FileStorageInterface;
use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GoogleStorageService implements FileStorageInterface
{
    private const string UPLOADS = 'uploads';
    private const int EXPIRE_TIME = 3600 * 2;
    private Bucket $bucket;
    private string $uid;

    public function __construct(
        private string $googleKey,
        private string $bucketName,
        private CacheInterface $cache,
        private KernelInterface $kernel,
    ) {
        $this->bucket = new StorageClient([
            'keyFile' => json_decode($googleKey, true),
        ])->bucket($this->bucketName);

        $this->uid = Uuid::v4();
    }

    public function upload(UploadedFile $file): string
    {
        $filename = $this->uid . '.' . $file->getClientOriginalExtension();

        $this->bucket->upload(
            file_get_contents($file->getPathname()),
            [
                'name' => self::UPLOADS . '/' . $filename,
            ]
        );

        return $filename;
    }

    public function getFileUrl(string $filename): string
    {
        $object = $this->bucket->object(self::UPLOADS . '/' . $filename);

        return $this->cache->get($filename, function (ItemInterface $item) use ($object) {
            $item->expiresAfter(self::EXPIRE_TIME);

            return $object->signedUrl(
                new \DateTime()
                    ->setTimestamp(new \DateTime()->getTimestamp() + self::EXPIRE_TIME),
                ['version' => 'v4']
            );
        });
    }
}
