<?php

/**
 * Interface CanFormatForLog
 * @package WPDesk\FS\TableRate\Logger
 */
namespace FSProVendor\WPDesk\FS\TableRate\Logger;

/**
 * Can format for log.
 */
interface CanFormatForLog
{
    /**
     * @return string
     */
    public function format_for_log();
}
