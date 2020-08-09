<?php

declare(strict_types=1);

namespace MeiliSearchBundle\Metadata;

use MeiliSearchBundle\Exception\InvalidArgumentException;
use function array_key_exists;
use function sprintf;

/**
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
final class IndexMetadataRegistry
{
    /**
     * @var array<string,IndexMetadata>
     */
    private $indexes = [];

    public function add(string $index, IndexMetadata $configuration): void
    {
        if ($this->has($index)) {
            throw new InvalidArgumentException(sprintf(
                'This index is already configured, please consider using "%s::override()"',
                self::class
            ));
        }

        $this->indexes[$index] = $configuration;
    }

    public function override(string $index, IndexMetadata $newConfiguration): void
    {
        if (!$this->has($index)) {
            $this->add($index, $newConfiguration);

            return;
        }

        $this->indexes[$index] = $newConfiguration;
    }

    public function get(string $index): IndexMetadata
    {
        if (!array_key_exists($index, $this->indexes)) {
            throw new InvalidArgumentException('The desired index does not exist');
        }

        return $this->indexes[$index];
    }

    /**
     * @return array<string,IndexMetadata>
     */
    public function toArray(): array
    {
        return $this->indexes;
    }

    private function has(string $index): bool
    {
        return array_key_exists($index, $this->indexes);
    }
}