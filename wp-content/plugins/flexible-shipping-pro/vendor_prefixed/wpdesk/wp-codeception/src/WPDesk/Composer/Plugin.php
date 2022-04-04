<?php

namespace FSProVendor\WPDesk\Composer\Codeception;

use FSProVendor\Composer\Composer;
use FSProVendor\Composer\IO\IOInterface;
use FSProVendor\Composer\Plugin\Capable;
use FSProVendor\Composer\Plugin\PluginInterface;
/**
 * Composer plugin.
 *
 * @package WPDesk\Composer\Codeception
 */
class Plugin implements \FSProVendor\Composer\Plugin\PluginInterface, \FSProVendor\Composer\Plugin\Capable
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;
    public function activate(\FSProVendor\Composer\Composer $composer, \FSProVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    /**
     * @inheritDoc
     */
    public function deactivate(\FSProVendor\Composer\Composer $composer, \FSProVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    /**
     * @inheritDoc
     */
    public function uninstall(\FSProVendor\Composer\Composer $composer, \FSProVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    public function getCapabilities()
    {
        return [\FSProVendor\Composer\Plugin\Capability\CommandProvider::class => \FSProVendor\WPDesk\Composer\Codeception\CommandProvider::class];
    }
}
