<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class WP_Media_Folders_Helper
 */
class WPMediaFoldersQueue
{

    /**
     * Add a file to the queue
     *
     * @param integer $post_id                   Attachment id
     * @param string  $destination               Destination folder to move the file to
     * @param boolean $destination_with_filename Does the destination parameter contains also the file name (without extension)
     * @param boolean $delete_folder             Delete the folder containing this file after the process if it's empty
     * @param boolean $update_database           Update content in database or not
     *
     * @return void
     */
    public static function addToQueue($post_id, $destination, $destination_with_filename = true, $delete_folder = false, $update_database = true)
    {
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix.'wpmfs_queue',
            array(
                'post_id' => $post_id,
                'destination' => ltrim(str_replace('\\', '/', $destination), '/'),
                'with_filename' => $destination_with_filename,
                'delete_folder' => $delete_folder,
                'update_database' => $update_database,
                'date_added' => round(microtime(true) * 1000),
                'date_done' => null,
                'status' => 0
            ),
            array(
                '%d',
                '%s',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d'
            )
        );
    }

    /**
     * Proceed queue asynchronously
     *
     * @return void
     */
    public static function proceedQueueAsync()
    {
        $queue_running = get_option('wpmfs_queue_running');
        $queue_length = self::getQueueLength();

        // Check if queue is currently running for less than 30 seconds
        if ($queue_length && $queue_running + 30 < time()) {
            $result = wp_remote_head(admin_url('admin-ajax.php').'?action=wpmfs_proceed&wpmfs_token='.get_option('wp-media-folders-token'), array('sslverify' => false));
            WP_Media_Folders_Debug::log('Info : Proceed queue asynchronously ' . (is_wp_error($result)?$result->get_error_message():'success'));
        } else if ($queue_length) {
            WP_Media_Folders_Debug::log('Info : Queue already running (queue_running: ' . $queue_running .', time: ' . time());
        }
    }

    /**
     * Proceed elements in the queue
     *
     * @return integer
     */
    private static function proceedQueue()
    {
        WP_Media_Folders_Debug::log('Info : Proceed queue synchronously');
        global $wpdb;
        $done = 0;
        $max_execution_time = self::getMaximumExecutionTime();

        WP_Media_Folders_Debug::log('Info : Max execution time is ' . $max_execution_time);

        // Retrieve all elements in the queue
        do {
            $elements = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'wpmfs_queue WHERE status=0 ORDER BY date_added ASC');
            foreach ($elements as $element) {
                set_time_limit(0);
                // Actually move the file
                $result = WPMediaFoldersHelper::moveFile($element->post_id, $element->destination, (bool)$element->with_filename, $element->delete_folder, $element->update_database);
                $wpdb->update(
                    $wpdb->prefix . 'wpmfs_queue',
                    array(
                        'date_done' => round(microtime(true) * 1000),
                        'status' => ($result ? 1 : -1)
                    ),
                    array('id' => $element->id),
                    array('%d', '%d'),
                    array('%d')
                );

                // Update last queue time value
                update_option('wpmfs_queue_running', time());

                if ($result) {
                    $done++;
                }
            }
            $current_time = microtime(true);
        } while ($elements && $current_time < $max_execution_time);

        // Remove last week elements
        $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'wpmfs_queue WHERE date_done < (UNIX_TIMESTAMP()*1000 - 7 * 24 * 60 * 60 * 1000)');

        WP_Media_Folders_Debug::log('Info : Synchronous queue finished');

        return $done;
    }

    /**
     * Retrieve microtime at which the script should stop
     *
     * @return float
     */
    private static function getMaximumExecutionTime()
    {

        $max_execution_time = (int)ini_get('max_execution_time');

        if (!$max_execution_time) {
            $max_execution_time = 30;
        } elseif ($max_execution_time > 60) {
            $max_execution_time = 60;
        }

        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $time = $_SERVER['REQUEST_TIME_FLOAT'];
        } else {
            // Consider script started 3 seconds ago
            $time = microtime(true) - 3 * 1000 * 1000;
        }

        // We should stop the script 3 seconds before it reach max execution limit
        return $time + $max_execution_time * 1000 * 1000 - 3 * 1000 * 1000;
    }

    /**
     * Get number of items in the queue waiting
     *
     * @return integer
     */
    public static function getQueueLength()
    {
        global $wpdb;
        return (int)$wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'wpmfs_queue WHERE status=0');
    }

    /**
     * Enqueue background task script
     *
     * @param string $plugin_url Plugin url
     *
     * @return void
     */
    public static function enqueueScript($plugin_url)
    {
        wp_enqueue_script('wpmfs_queue', $plugin_url . '/assets/js/queue.js', array('jquery'), null, true);
        wp_localize_script('wpmfs_queue', 'wpmf_ajaxurl', admin_url('admin-ajax.php'));
    }

    /**
     * Ajax request
     *
     * @return void
     */
    public static function initAjax()
    {
        add_action('wp_ajax_wpmfs_queue', function () {
            $queue_length = self::getQueueLength();

            WP_Media_Folders_Debug::log('Info : Ajax queue proceed request (queue length: ' . $queue_length .')');

            echo json_encode(array(
                'queue_length' => $queue_length,
                'title' => sprintf(__('%s Attachments queued to be moved', 'wp-media-folders'), $queue_length)
            ));

            self::proceedQueueAsync();

            exit(0);
        });

        add_action('wp_ajax_nopriv_wpmfs_proceed', function () {
            // phpcs:ignore WordPress.CSRF.NonceVerification -- No action and a custom token is used
            if (!isset($_REQUEST['wpmfs_token']) || $_REQUEST['wpmfs_token'] !== get_option('wp-media-folders-token')) {
                WP_Media_Folders_Debug::log('Info : Proceed queue ajax stopped, wrong token');
                exit(0);
            }

            WP_Media_Folders_Debug::log('Info : Proceed queue ajax');

            if (ob_get_length()) {
                ob_end_clean();
            }
            header('Connection: close\r\n');
            header('Content-Encoding: none\r\n');
            ignore_user_abort(true);
            header('Content-Length: 0');
            ob_end_flush();
            flush();
            if (ob_get_length()) {
                ob_end_clean();
            }

            self::proceedQueue();

            if (self::getQueueLength()) {
                self::proceedQueueAsync();
            }
        });
    }

    /**
     * Hook queue processing on WP hearbeat
     *
     * @return void
     */
    public static function initHeartbeat()
    {
        add_filter('heartbeat_received', function ($response) {
            $queue_length = self::getQueueLength();
            WP_Media_Folders_Debug::log('Info : Heartbeat received, queue length is ' . $queue_length);
            if ($queue_length) {
                self::proceedQueueAsync();
            }
            return $response;
        }, 10);
    }
}
