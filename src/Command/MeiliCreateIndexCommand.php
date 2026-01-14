<?php

namespace App\Command;

use App\Enum\IndexesEnum;
use Meilisearch\Client;
use Meilisearch\Meilisearch;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:meili:create-index',
    description: 'Add a short description for your command',
)]
class MeiliCreateIndexCommand extends Command
{
    public function __construct(private Client $client)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var array<\Meilisearch\Endpoints\Indexes> $indexes */
        $indexes = $this->client->getIndexes()->getResults();

        if (!array_any($indexes, fn($index) => $index->getUid() === IndexesEnum::inventories->value)) {
            $this->client->createIndex(IndexesEnum::inventories->value, ['primaryKey' => 'id']);

            $index = $this->client->index(IndexesEnum::inventories->value);
            $index->updateSettings([
                'filterableAttributes' => ['user_id'],
                'searchableAttributes' => ['title', 'description']
            ]);
        }

        if (!array_any($indexes, fn($index) => $index->getUid() === IndexesEnum::items->value)) {
            $this->client->createIndex(IndexesEnum::items->value, ['primaryKey' => 'id']);

            $index = $this->client->index(IndexesEnum::items->value);
            $index->updateSettings([
                'filterableAttributes' => ['inventory_id'],
                'searchableAttributes' => ['custom_id']
            ]);
        }

        return Command::SUCCESS;
    }
}
