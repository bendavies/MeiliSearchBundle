<?php

declare(strict_types=1);

namespace Tests\MeiliSearchBundle\Index;

use MeiliSearch\Endpoints\Indexes;
use MeiliSearchBundle\Index\IndexOrchestratorInterface;
use MeiliSearchBundle\Index\TraceableIndexOrchestrator;
use PHPUnit\Framework\TestCase;

/**
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
final class TraceableIndexOrchestratorTest extends TestCase
{
    public function testIndexCanBeCreated(): void
    {
        $orchestrator = $this->createMock(IndexOrchestratorInterface::class);
        $orchestrator->expects(self::once())->method('addIndex');

        $traceableOrchestrator = new TraceableIndexOrchestrator($orchestrator);
        $traceableOrchestrator->addIndex('foo', 'id');

        static::assertNotEmpty($traceableOrchestrator->getData()['createdIndexes']);
    }

    public function testIndexesCanBeRetrieved(): void
    {
        $index = $this->createMock(Indexes::class);
        $index->expects(self::once())->method('show')->willReturn([
            'uid' => 'movies',
            'primaryKey' => 'movie_id',
            'createdAt' => '2019-11-20T09:40:33.711324Z',
            'updatedAt' => '2019-11-20T10:16:42.761858Z',
        ]);

        $secondIndex = $this->createMock(Indexes::class);
        $secondIndex->expects(self::once())->method('show')->willReturn([
            'uid' => 'bar',
            'primaryKey' => 'id',
            'createdAt' => '2019-11-20T09:40:33.711324Z',
            'updatedAt' => '2019-11-20T10:16:42.761858Z',
        ]);

        $orchestrator = $this->createMock(IndexOrchestratorInterface::class);
        $orchestrator->expects(self::once())->method('getIndexes')->willReturn([
            $index,
            $secondIndex
        ]);

        $traceableOrchestrator = new TraceableIndexOrchestrator($orchestrator);

        static::assertNotEmpty($traceableOrchestrator->getIndexes());
        static::assertNotEmpty($traceableOrchestrator->getData()['fetchedIndexes']);
    }

    public function testSingleIndexCanBeRetrieved(): void
    {
        $index = $this->createMock(Indexes::class);
        $index->expects(self::once())->method('show')->willReturn([
            "uid" => "movies",
            "primaryKey" => "movie_id",
            "createdAt" => "2019-11-20T09:40:33.711324Z",
            "updatedAt" => "2019-11-20T10:16:42.761858Z",
        ]);

        $orchestrator = $this->createMock(IndexOrchestratorInterface::class);
        $orchestrator->expects(self::once())->method('getIndex')->willReturn($index);

        $traceableOrchestrator = new TraceableIndexOrchestrator($orchestrator);
        $fetchedIndex = $traceableOrchestrator->getIndex('foo');

        static::assertInstanceOf(Indexes::class, $fetchedIndex);
        static::assertSame($index, $fetchedIndex);
        static::assertNotEmpty($traceableOrchestrator->getData()['fetchedIndexes']);
    }

    public function testIndexesCanBeRemoved(): void
    {
        $orchestrator = $this->createMock(IndexOrchestratorInterface::class);
        $orchestrator->expects(self::once())->method('removeIndexes');

        $traceableOrchestrator = new TraceableIndexOrchestrator($orchestrator);
        $traceableOrchestrator->removeIndexes();

        static::assertEmpty($traceableOrchestrator->getData()['deletedIndexes']);
    }

    public function testSingleIndexCanBeRemoved(): void
    {
        $orchestrator = $this->createMock(IndexOrchestratorInterface::class);
        $orchestrator->expects(self::once())->method('removeIndex')->with(self::equalTo('foo'));

        $traceableOrchestrator = new TraceableIndexOrchestrator($orchestrator);
        $traceableOrchestrator->removeIndex('foo');

        static::assertNotEmpty($traceableOrchestrator->getData()['deletedIndexes']);
        static::assertCount(1, $traceableOrchestrator->getData()['deletedIndexes']);
        static::assertArrayHasKey('uid', $traceableOrchestrator->getData()['deletedIndexes'][0]);
    }
}
