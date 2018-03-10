<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 12:28 PM
 */

namespace emPHPasis\Commands;

use emPHPasis\Pipelines;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Initialize
 * @package emPHPasis\Commands
 */
class Initialize extends Command
{
    /**
     * This function registers the console command and its signature
     *
     * @return void
     */
    protected function configure() : void
    {
        $this->setName('emPHPasis:initialize')
            ->setDescription('Generate default config file.')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'The path in which to put the config file. Default "./".'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Generating default config");

        $pipeline = new Pipelines\Initialize();
        $pipeline->fill();

        // refill the pipe if the path is provided
        if ($input->hasArgument('path')) {
            $pipeline->fill($input->getArgument('path'));
        }

        $result = $pipeline->flush();

        //TODO figure out what to do with the result
        $output->write(print_r($result,true));
    }
}
