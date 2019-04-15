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
            'no-args' => ['args' => [], 'error' => 'Not enough arguments'],
            'args/err/1' => ['args' => ['template' => 'un/known'], 'error' => 'Not enough arguments'],
            'args/err/2' => ['args' => ['template' => 'un/known', 'values' => 'un/known', 'output' => 'un/known'], 'error' => 'The template file'],
            'args/err/3' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => 'un/known', 'output' => 'un/known'], 'error' => 'The YAML file'],
            'args/err/4' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => self::data('tmpl.docx'), 'output' => self::data('out.tmp.docx')], 'error' => 'The YAML value'],
            'args/err/5' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => self::data('val.yml'), 'output' => 'un/known'], 'error' => 'The output path'],
            'ok' => ['args' => ['template' => self::data('tmpl.docx'), 'values' => self::data('val.yml'), 'output' => self::data('out.tmp.docx')], 'error' => false],
        ];
        /* Run tests */
        foreach ($tests as $name => $test) {
            echo $name, PHP_EOL;
            /* Initialize the application */
            $app = self::bootstrap();
            $command = $app->find('yamltodocx');
            $tester = new CommandTester($command);
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
