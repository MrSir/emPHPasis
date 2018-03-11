<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 12:28 PM
 */

namespace emPHPasis\Commands;

use Carbon\Carbon;
use emPHPasis\Pipelines;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Generate
 * @package emPHPasis\Commands
 */
class Generate extends Command
{
    /**
     * This function registers the console command and its signature
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('emPHPasis:generate')
            ->setDescription('Generate the rich static analysis dashboard.')
            ->addOption(
                '--config',
                '-c',
                InputOption::VALUE_REQUIRED,
                'The path in which config file is found. Default "./emPHPasis.json".'
            )
            ->addOption(
                '--path',
                '-p',
                InputOption::VALUE_REQUIRED,
                'The path in which to put the generated files. Default "./build/emPHPasis/".'
            );
    }

    /**
     * This function executes the command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $output->writeln(Carbon::now() . " Generating dashboard");

        // instantiate the pipeline
        $pipeline = new Pipelines\Generate();

        // preset the variables
        $path = null;
        $config = null;

        // set the variable if provided
        if ($input->hasOption('path')) {
            $path = $input->getOption('path');
        }

        // set the variable if provided
        if ($input->hasOption('config')) {
            $config = $input->getOption('config');
        }

        // fill the pipe
        $pipeline->fill($output, $config, $path);

        // flush the pipeline
        $passable = $pipeline->flush();

        // print out the success message
        if ($passable->getCode() == $passable::SUCCESS_CODE) {
            $output->writeln(Carbon::now() . ' Generated dashboard successfully: ' . $passable->getPath());
        }
    }
}
