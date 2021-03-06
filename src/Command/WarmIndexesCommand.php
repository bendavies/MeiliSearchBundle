<?php

declare(strict_types=1);

namespace MeiliSearchBundle\Command;

use MeiliSearchBundle\Index\IndexOrchestratorInterface;
use MeiliSearchBundle\Messenger\AddIndexMessage;
use MeiliSearchBundle\Metadata\IndexMetadata;
use MeiliSearchBundle\Metadata\IndexMetadataRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;
use function array_filter;
use function array_key_exists;
use function sprintf;

/**
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
final class WarmIndexesCommand extends Command
{
    private const ASYNC = 'async';
    private const PRIMARY_KEY = 'primaryKey';
    private const SYNONYMS = 'synonyms';

    /**
     * @var array<string, array>
     */
    private $indexes;

    /**
     * @var IndexMetadataRegistryInterface
     */
    private $indexMetadataRegistry;

    /**
     * @var IndexOrchestratorInterface
     */
    private $indexOrchestrator;

    /**
     * @var MessageBusInterface|null
     */
    private $messageBus;

    /**
     * @var string|null
     */
    private $prefix;

    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'meili:warm-indexes';

    /**
     * @param array<string, array>           $indexes
     * @param IndexMetadataRegistryInterface $indexMetadataRegistry
     * @param IndexOrchestratorInterface     $indexOrchestrator
     * @param MessageBusInterface|null       $messageBus
     * @param string|null                    $indexPrefix
     */
    public function __construct(
        array $indexes,
        IndexMetadataRegistryInterface $indexMetadataRegistry,
        IndexOrchestratorInterface $indexOrchestrator,
        ?MessageBusInterface $messageBus = null,
        ?string $indexPrefix = null
    ) {
        $this->indexes = $indexes;
        $this->indexMetadataRegistry = $indexMetadataRegistry;
        $this->indexOrchestrator = $indexOrchestrator;
        $this->messageBus = $messageBus;
        $this->prefix = $indexPrefix;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Allow to warm the indexes defined in the configuration')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (empty($this->indexes)) {
            $io->warning('No indexes found, please define at least a single index');

            return 1;
        }

        $asyncIndexes = $asyncIndexes = array_filter($this->indexes, function (array $index): bool {
            return array_key_exists(self::ASYNC, $index) && $index['async'];
        });

        if (!empty($asyncIndexes) && null === $this->messageBus) {
            $io->error([
                'The "async" attribute cannot be used when Messenger is not installed',
                'Consider using "composer require symfony/messenger"'
            ]);

            return 1;
        }

        try {
            foreach ($this->indexes as $indexName => $configuration) {
                $indexName = null !== $this->prefix ? sprintf('%s_%s', $this->prefix, $indexName) : $indexName;
                $primaryKey = $configuration[self::PRIMARY_KEY];
                $configuration[self::SYNONYMS] = $this->handleSynonyms($configuration[self::SYNONYMS]);

                $metadata = new IndexMetadata(
                    $indexName,
                    $configuration[self::ASYNC] ?? false,
                    $primaryKey ?? null,
                    $configuration['rankingRules'] ?? [],
                    $configuration['stopWords'] ?? [],
                    $configuration['distinctAttribute'] ?? null,
                    $configuration['facetedAttributes'] ?? [],
                    $configuration['searchableAttributes'] ?? [],
                    $configuration['displayedAttributes'] ?? [],
                    $configuration[self::SYNONYMS] ?? []
                );

                if ($this->indexMetadataRegistry->has($indexName)) {
                    $this->indexMetadataRegistry->override($indexName, $metadata);
                    $this->indexOrchestrator->update($indexName, $metadata->toArray());

                    continue;
                }

                $this->indexMetadataRegistry->add($indexName, $metadata);

                if (\array_key_exists(self::ASYNC, $configuration) && $configuration['async']) {
                    unset($configuration[self::ASYNC], $configuration[self::PRIMARY_KEY]);

                    $this->messageBus->dispatch(new AddIndexMessage($indexName, $primaryKey, $configuration));

                    continue;
                }

                $this->indexOrchestrator->addIndex($indexName, $configuration[self::PRIMARY_KEY], $configuration);
            }
        } catch (Throwable $throwable) {
            $io->error([
                'The indexes cannot be warmed!',
                sprintf('Error: "%s"', $throwable->getMessage())
            ]);

            return 1;
        }

        $io->success('The indexes has been warmed, feel free to query them!');

        return 0;
    }

    private function handleSynonyms(array $synonyms): array
    {
        if (empty($synonyms)) {
            return [];
        }

        $filteredSynonyms = [];
        foreach ($synonyms as $synonym => $values) {
            $filteredSynonyms[$synonym] = $values['values'];
        }

        return $filteredSynonyms;
    }
}
