<?php

namespace FSProVendor\WPDesk\Composer\Codeception\Commands;

use FSProVendor\Composer\Command\BaseCommand as CodeceptionBaseCommand;
use FSProVendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * Base for commands - declares common methods.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
abstract class BaseCommand extends \FSProVendor\Composer\Command\BaseCommand
{
    /**
     * @param string $command
     * @param OutputInterface $output
     */
    protected function execAndOutput($command, \FSProVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        \passthru($command);
    }
}
