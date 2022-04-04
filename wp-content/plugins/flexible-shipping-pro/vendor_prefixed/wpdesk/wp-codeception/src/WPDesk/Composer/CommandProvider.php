<?php

namespace FSProVendor\WPDesk\Composer\Codeception;

use FSProVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests;
use FSProVendor\WPDesk\Composer\Codeception\Commands\PrepareCodeceptionDb;
use FSProVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTests;
use FSProVendor\WPDesk\Composer\Codeception\Commands\PrepareParallelCodeceptionTests;
use FSProVendor\WPDesk\Composer\Codeception\Commands\PrepareWordpressForCodeception;
use FSProVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests;
use FSProVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTests;
/**
 * Links plugin commands handlers to composer.
 */
class CommandProvider implements \FSProVendor\Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return [new \FSProVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests(), new \FSProVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests(), new \FSProVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTests(), new \FSProVendor\WPDesk\Composer\Codeception\Commands\PrepareCodeceptionDb(), new \FSProVendor\WPDesk\Composer\Codeception\Commands\PrepareWordpressForCodeception(), new \FSProVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTests(), new \FSProVendor\WPDesk\Composer\Codeception\Commands\PrepareParallelCodeceptionTests()];
    }
}
