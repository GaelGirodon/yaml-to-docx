<?php

namespace YamlToDocx;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * YamlToDocx tests.
 * @package YamlToDocx
 */
final class YamlToDocxTest extends TestCase
{
    /**
     * Test the 'yamltodocx' command.
     */
    public function testCommand()
    {
        /* Test cases */
        $tests = [
            'err/args/0' => ['args' => [], 'error' => 'Not enough arguments'],
            'err/args/1' => ['args' => ['template' => 'un/known'], 'error' => 'Not enough arguments'],
            'err/args/2' => ['args' => ['template' => 'un/known', 'values' => 'un/known', 'output' => 'un/known'],
                'error' => 'The template file'],
            'err/args/3' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => 'un/known', 'output' => 'un/known'],
                'error' => 'The YAML file'],
            'err/args/4' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => self::data('tmpl.docx'),
                'output' => self::data('out.tmp.docx')], 'error' => 'The YAML value'],
            'err/args/5' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => self::data('val.yml'),
                'output' => 'un/known'], 'error' => 'The output path'],
            'err/overwrite' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => self::data('val.yml'),
                'output' => self::data('exists.tmp.docx')], 'files' => [self::data('exists.tmp.docx')], 'error' => 'The output file'],
            'ok' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => self::data('val.yml'),
                'output' => self::data('out.tmp.docx')], 'error' => false],
            'ok/overwrite' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => self::data('val.yml'),
                'output' => self::data('exists.tmp.docx'), '--overwrite' => true], 'files' => [self::data('exists.tmp.docx')]],
        ];
        /* Run tests */
        foreach ($tests as $name => $test) {
            echo $name, PHP_EOL;
            /* Initialize the application */
            $app = self::bootstrap();
            $command = $app->find('yamltodocx');
            $tester = new CommandTester($command);
            /* Initial files */
            if (array_key_exists('files', $test) && count($test['files']) > 0) {
                foreach ($test['files'] as $file) {
                    touch($file);
                }
            }
            /* Run and test the command */
            try {
                $tester->execute(array_merge(['command' => $command->getName()], $test['args']));
                $this->assertFileExists($test['args']['output']);
                unlink($test['args']['output']);
            } catch (\Exception $e) {
                if ($test['error']) {
                    $this->assertStringContainsStringIgnoringCase($test['error'], $e->getMessage());
                } else {
                    $this->fail('Unexpected error: ' . $e->getMessage());
                }
            }
            /* Clean up initial files */
            if (array_key_exists('files', $test) && count($test['files']) > 0) {
                foreach ($test['files'] as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }

    /**
     * Build the CLI application.
     * @return Application The CLI application.
     */
    public static function bootstrap(): Application
    {
        $application = new Application();
        $cmd = new YamlToDocxCommand();
        $application->add($cmd);
        $application->setDefaultCommand($cmd->getName());
        return $application;
    }

    /**
     * Build the absolute path to a test data file.
     * @param $filename string Test data file name.
     * @return string The absolute path to the test data file.
     */
    public static function data(string $filename)
    {
        return join(DIRECTORY_SEPARATOR, array(getcwd(), "tests", "data", $filename));
    }
}
