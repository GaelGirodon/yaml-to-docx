<?php

namespace YamlToDocx;

use Exception;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * A tool to generate a docx file from a docx template
 * and a YAML file containing values to populate.
 * @package YamlToDocx
 */
class YamlToDocxGenerator
{
    /**
     * Console output.
     * @var OutputInterface
     */
    private $console;

    /**
     * Initialize the generator.
     * @param OutputInterface $console Console output.
     */
    public function __construct(OutputInterface $console)
    {
        $this->console = $console;
    }

    /**
     * Generate a docx file from a template with placeholders
     * and a YAML file containing values to populate.
     * @param string $tmpl Path to the Word template with placeholders.
     * @param string $yaml Path to the YAML file containing values to populate.
     * @param string $output Path to the output Word file.
     * @param bool $overwrite Overwrite the output file.
     */
    public function generate(string $tmpl, string $yaml, string $output, bool $overwrite)
    {
        /* Arguments */
        $tmpl = self::cleanpath($tmpl);
        $yaml = self::cleanpath($yaml);
        $output = self::cleanpath($output);

        /* Validation */
        if (strlen($tmpl) == 0 || !file_exists($tmpl) || strrpos($tmpl, ".docx") != strlen($tmpl) - 5) {
            throw new RuntimeException("The template file '$tmpl' doesn't exist or is invalid.", 10);
        }
        if (strlen($yaml) == 0 || !file_exists($yaml)) {
            throw new RuntimeException("The YAML file '$yaml' doesn't exist.", 11);
        }
        if (strlen($output) == 0 || !file_exists(dirname($output))
            && !file_exists(dirname($output = self::cleanpath(getcwd() . '/' . $output)))) {
            throw new RuntimeException("The output path '$output' is invalid.", 12);
        }
        if (!$overwrite && is_file($output)) {
            throw new RuntimeException("The output file '$output' already exists (overwrite it with -o option).", 13);
        }

        /* Parse variables from YAML file */
        $this->console->writeln("<comment>Loading YAML file '$yaml'</comment>");
        $variables = Yaml::parseFile($yaml);
        $this->console->writeln("<info>Done</info>");

        /* Load template */
        try {
            $this->console->writeln("<comment>Loading template file '$tmpl'</comment>");
            $templateProcessor = new TemplateProcessor($tmpl);
            $this->console->writeln("<info>Done</info>");
        } catch (Exception $e) {
            throw new RuntimeException("Unable to load the template file '$tmpl'.", 20);
        }

        /* Populate values */
        $this->console->writeln("<comment>Populating template values</comment>");
        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                try {
                    $templateProcessor->cloneRow($key, count($value));
                    /* Loop through array values */
                    foreach ($value as $rowIndex => $row) {
                        /* Loop through row cells */
                        foreach ($row as $cellKey => $cellValue) {
                            $rowNum = $rowIndex + 1;
                            $templateProcessor->setValue("$cellKey#$rowNum", $cellValue);
                        }
                    }
                } catch (Exception $e) {
                    throw new RuntimeException("Unable to populate the array '$key'.", 30);
                }
            } else {
                $templateProcessor->setValue($key, $value);
            }
        }
        $this->console->writeln("<info>Done</info>");

        /* Save the document */
        $this->console->writeln("<comment>Saving to '$output'</comment>");
        $templateProcessor->saveAs($output);
        $this->console->writeln("<info>Done</info>");
    }

    /**
     * Clean a path.
     * @param string $path The path to clean.
     * @return string The clean path.
     */
    private static function cleanpath(string $path)
    {
        /* Clean directory separators */
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        /* Try to get the canonicalized absolute path */
        $rp = realpath($path);
        $name = basename($path);
        if ($rp && strrpos($rp, $name) == strlen($rp) - strlen($name)) {
            /* $rp seems to be right */
            return $rp;
        }
        return $path;
    }
}
