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
 * Class Initialize
 * @package emPHPasis\Commands
 */
class Initialize extends Command
{
    /**
     * This function registers the console command and its signature
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('emPHPasis:initialize')
            ->setDescription('Generate default config file.')
            ->addOption(
                '--path',
                '-p',
                InputOption::VALUE_REQUIRED,
                'The path in which to put the config file. Default "./".'
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
        $output->writeln(Carbon::now() . " Generating default config");

        $pipeline = new Pipelines\Initialize();
        $pipeline->fill();

        // refill the pipe if the path is provided
        if ($input->hasOption('path')) {
            $pipeline->fill($input->getOption('path'));
        }

        $passable = $pipeline->flush();

        if ($passable->getCode() == $passable::SUCCESS_CODE) {
            $output->writeln(Carbon::now() . ' Configuration file generated successfully: ' . $passable->getFilePath());
        }
    }
}
