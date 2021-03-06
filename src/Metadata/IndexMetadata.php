<?php

declare(strict_types=1);

namespace MeiliSearchBundle\Metadata;

/**
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
final class IndexMetadata implements IndexMetadataInterface
{
    /**
     * @var string
     */
    private $uid;

    /**
     * @var bool
     */
    private $async;

    /**
     * @var string|null
     */
    private $primaryKey;

    /**
     * @var array<int, string>
     */
    private $rankingRules;

    /**
     * @var array<int, string>
     */
    private $stopWords;

    /**
     * @var string|null
     */
    private $distinctAttribute;

    /**
     * @var array<int, string>
     */
    private $facetedAttributes;

    /**
     * @var array<int, string>
     */
    private $searchableAttributes;

    /**
     * @var array<int, string>
     */
    private $displayedAttributes;

    /**
     * @var array<string, array>
     */
    private $synonyms;

    public function __construct(
        string $uid,
        ?bool $async = false,
        ?string $primaryKey = null,
        ?array $rankingRules = [],
        ?array $stopWords = [],
        ?string $distinctAttribute = null,
        ?array $facetedAttributes = [],
        ?array $searchableAttributes = [],
        ?array $displayedAttributes = [],
        ?array $synonyms = []
    ) {
        $this->uid = $uid;
        $this->async = $async;
        $this->primaryKey = $primaryKey;
        $this->rankingRules = $rankingRules;
        $this->stopWords = $stopWords;
        $this->distinctAttribute = $distinctAttribute;
        $this->facetedAttributes = $facetedAttributes;
        $this->searchableAttributes = $searchableAttributes;
        $this->displayedAttributes = $displayedAttributes;
        $this->synonyms = $synonyms;
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function getPrimaryKey(): ?string
    {
        return $this->primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getRankingRules(): array
    {
        return $this->rankingRules;
    }

    /**
     * {@inheritdoc}
     */
    public function getStopWords(): array
    {
        return $this->stopWords;
    }

    public function getDistinctAttribute(): ?string
    {
        return $this->distinctAttribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetedAttributes(): array
    {
        return $this->facetedAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableAttributes(): array
    {
        return $this->searchableAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayedAttributes(): array
    {
        return $this->displayedAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'primaryKey' => $this->primaryKey,
            'rankingRules' => $this->rankingRules,
            'stopWords' => $this->stopWords,
            'distinctAttribute' => $this->distinctAttribute,
            'facetedAttributes' => $this->facetedAttributes,
            'searchableAttributes' => $this->searchableAttributes,
            'displayedAttributes' => $this->displayedAttributes,
            'synonyms' => $this->synonyms,
        ];
    }
}
