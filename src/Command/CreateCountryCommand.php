<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\Country;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

class CreateCountryCommand extends Command
{

    protected static $defaultName = 'app:create-country';
    private $container;

    protected function configure()
    {
        $this->setDescription('Creates a new country.')
        ->setHelp('This command allows you to create a country...')
        ->addArgument('name', InputArgument::REQUIRED, 'Name of country')
    ;
    }

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->container->get('doctrine')->getManager();

        $country = new Country();

        $country->setName($input->getArgument('name'));
        $canonicalName = preg_replace('/[^a-z]/i', '', $input->getArgument('name'));
        $canonicalName = strtolower($canonicalName);
        $country->setCanonicalName($canonicalName);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($country);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        $output->writeln('You added '. $input->getArgument('name') .' to database.');

        return Command::SUCCESS;

    }
}

?>