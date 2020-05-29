<?php

namespace MeiliBundle\Command;

use MeiliBundle\Client\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
final class CreateIndexesCommand extends Command
{
    private $client;

    protected static $defaultName = 'meili:create-indexes';

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('index', InputArgument::REQUIRED)
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}