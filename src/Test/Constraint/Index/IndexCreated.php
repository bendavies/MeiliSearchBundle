<?php

declare(strict_types=1);

namespace MeiliSearchBundle\Test\Constraint\Index;

use MeiliSearchBundle\Event\Index\IndexEventListInterface;
use PHPUnit\Framework\Constraint\Constraint;
use function count;
use function sprintf;

/**
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
final class IndexCreated extends Constraint
{
    private $expectedCount;

    public function __construct(int $expectedCount)
    {
        $this->expectedCount = $expectedCount;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return sprintf('%s index%s %s been created', $this->expectedCount, $this->expectedCount > 1 ? 'es' : '', $this->expectedCount > 1 ? 'have' : 'has');
    }

    /**
     * @param IndexEventListInterface $eventsList
     *
     * {@inheritdoc}
     */
    protected function matches($eventsList): bool
    {
        return $this->expectedCount === count($eventsList->getIndexCreatedEvents());
    }
}
