<?php

namespace FSProVendor\WPDesk\Logger\WC;

use FSProVendor\Monolog\Handler\AbstractProcessingHandler;
use FSProVendor\Monolog\Logger;
/**
 * Class WooCommerceFactory
 */
class WooCommerceHandler extends \FSProVendor\Monolog\Handler\AbstractProcessingHandler
{
    const DEFAULT_WC_SOURCE = 'wpdesk-logger';
    /** @var \WC_Logger_Interface */
    private $wc_logger;
    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $context = \array_merge(['source' => self::DEFAULT_WC_SOURCE], $record['extra'], $record['context']);
        $this->wc_logger->log($this->convertMonologLevelToWC($record['level']), $record['message'], $context);
    }
    /**
     * @param int $level
     * @return string
     */
    private function convertMonologLevelToWC($level)
    {
        return \FSProVendor\Monolog\Logger::getLevelName($level);
    }
    public function __construct(\WC_Logger_Interface $originalWcLogger)
    {
        parent::__construct();
        $this->wc_logger = $originalWcLogger;
    }
}
