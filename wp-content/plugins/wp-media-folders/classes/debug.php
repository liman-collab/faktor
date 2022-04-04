<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class WP_Media_Folders_Debug
 *
 * Handles debuging and logging features
 */
class WP_Media_Folders_Debug
{
    /**
     * If debug is enabled or not
     *
     * @var boolean
     */
    public static $debug_enabled = false;

    /**
     * Debug file
     *
     * @var string
     */
    public static $debug_file;

    /**
     * Log into a debug file
     *
     * @return void
     */
    public static function log()
    {
        // Do nothing if not enabled
        if (!self::$debug_enabled) {
            return;
        }

        // Retrieve arguments passed
        $args = func_get_args();

        // Check that we have at least a string to log
        if ($args<2) {
            return;
        }

        global $wp_filesystem;
        if ($wp_filesystem === null) {
            // Initialize Wp_filesystem
            include_once(ABSPATH .'/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        $dir = dirname(self::$debug_file);
        if (!$wp_filesystem->exists($dir)) {
            if (!$wp_filesystem->mkdir($dir)) {
                error_log('WP Media Folders debug failed to create folder ' . $dir);
                return;
            }
        }

        if (!$wp_filesystem->exists(self::$debug_file)) {
            if (!$wp_filesystem->put_contents(self::$debug_file, '<?php die(); ?>'.PHP_EOL)) {
                error_log('WP Media Folders debug failed to create file ' . self::$debug_file);
                return;
            }
        }

        if (!$wp_filesystem->is_writable(self::$debug_file)) {
            if ($wp_filesystem->chmod(self::$debug_file, 0644)) {
                error_log('WP Media Folders debug failed to chmod file ' . self::$debug_file);
            }
        }

        $arguments = array();
        // phpcs:ignore Generic.CodeAnalysis.ForLoopWithTestFunctionCall -- Allow using count
        for ($ij=1; $ij < count($args); $ij++) {
            $arguments[] = $args[$ij];
        }

        $fp = fopen(self::$debug_file, 'a+');
        $result = fwrite($fp, vsprintf($args[0], $arguments).PHP_EOL);
        fclose($fp);

        if ($result === false) {
            // Write failed, let'e error log it
            error_log('WP Media Folders debug failed to write to file ' . self::$debug_file);
        }
    }
}
