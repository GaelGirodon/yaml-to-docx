<?php

namespace YamlToDocx;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The 'yamltodocx' command.
 * @package YamlToDocx
 */
class YamlToDocxCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('yamltodocx')
            ->setDescription('Create a docx file from a template and a YAML file.')
            ->setHelp('Load a template file, populate placeholders with values '
                . 'from a YAML file and output a docx file.')
            ->addArgument('template', InputArgument::REQUIRED, 'Path to the docx template file')
            ->addArgument('values', InputArgument::REQUIRED, 'Path to the YAML file containing values to populate in the template')
            ->addArgument('output', InputArgument::REQUIRED, 'Path where to save the generated docx file')
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Overwrite the output file');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['', '<fg=white;bg=cyan;options=bold>' . str_repeat(' ', 19),
            '    YAML → Docx    ', str_repeat(' ', 19) . '</>', '']);
        (new YamlToDocxGenerator($output))->generate(
            $input->getArgument('template'),
            $input->getArgument('values'),
            $input->getArgument('output'),
            $input->getOption('overwrite') == 1);
    }
}
