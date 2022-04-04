<?php

namespace FSProVendor\WPDesk\Composer\Codeception\Commands;

use FSProVendor\Symfony\Component\Console\Input\InputArgument;
use FSProVendor\Symfony\Component\Console\Input\InputInterface;
use FSProVendor\Symfony\Component\Console\Output\OutputInterface;
use FSProVendor\Symfony\Component\Yaml\Exception\ParseException;
use FSProVendor\Symfony\Component\Yaml\Yaml;
/**
 * Split test to multiple directories for parallel running in CI.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
class PrepareParallelCodeceptionTests extends \FSProVendor\WPDesk\Composer\Codeception\Commands\BaseCommand
{
    const NUMBER_OF_JOBS = 'number_of_jobs';
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('prepare-parallel-codeception-tests')->setDescription('Prepare parallel codeception tests.')->setDefinition(array(new \FSProVendor\Symfony\Component\Console\Input\InputArgument(self::NUMBER_OF_JOBS, \FSProVendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Number of jobs.', '4')));
    }
    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(\FSProVendor\Symfony\Component\Console\Input\InputInterface $input, \FSProVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $numberOfJobs = (int) $input->getArgument(self::NUMBER_OF_JOBS);
        $acceptanceTestsDir = \getcwd() . '/tests/codeception/tests/acceptance';
        for ($i = 1; $i <= $numberOfJobs; $i++) {
            $parallelDir = $acceptanceTestsDir . '/' . $i;
            if (!\file_exists($parallelDir)) {
                \mkdir($parallelDir);
            }
        }
        $currentIndex = 1;
        $files = \scandir($acceptanceTestsDir);
        foreach ($files as $fileName) {
            $fileFullPath = $acceptanceTestsDir . '/' . $fileName;
            if (!\is_dir($fileFullPath)) {
                $targetPath = $acceptanceTestsDir . '/' . $currentIndex . '/' . $fileName;
                \copy($fileFullPath, $targetPath);
                $currentIndex++;
                if ($currentIndex > $numberOfJobs) {
                    $currentIndex = 1;
                }
            }
        }
    }
}
