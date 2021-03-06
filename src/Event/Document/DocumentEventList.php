<?php

declare(strict_types=1);

namespace MeiliSearchBundle\Event\Document;

use Countable;
use function array_filter;
use function count;

/**
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
final class DocumentEventList implements DocumentEventListInterface, Countable
{
    /**
     * @var array<int, DocumentEventInterface>
     */
    private $events = [];

    public function add(DocumentEventInterface $event): void
    {
        $this->events[] = $event;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostDocumentCreationEvent(): array
    {
        return array_filter($this->events, function (DocumentEventInterface $event): bool {
            return $event instanceof PostDocumentCreationEvent;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getPostDocumentDeletionEvent(): array
    {
        return array_filter($this->events, function (DocumentEventInterface $event): bool {
            return $event instanceof PostDocumentDeletionEvent;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getPostDocumentRetrievedEvent(): array
    {
        return array_filter($this->events, function (DocumentEventInterface $event): bool {
            return $event instanceof PostDocumentRetrievedEvent;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getPostDocumentUpdateEvent(): array
    {
        return array_filter($this->events, function (DocumentEventInterface $event): bool {
            return $event instanceof PostDocumentUpdateEvent;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->events);
    }
}
