<?php
/**
 * @copyright 2018 Damien Barrère
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class WPMediaFolders
{

    /**
     * Plugin main file
     *
     * @var string
     */
    protected $plugin_main_file;

    /**
     * WPMediaFolders constructor.
     *
     * @param string $plugin_main_file Plugin main file
     */
    public function __construct($plugin_main_file)
    {
        $this->plugin_main_file = $plugin_main_file;

        $this->runUpgrades();

        // Enable logging if needed
        include_once dirname($this->plugin_main_file) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'debug.php';
        $options = get_option('wp-media-folders-options');
        if (isset($options['mode_debug'])) {
            WP_Media_Folders_Debug::$debug_enabled = true;
            WP_Media_Folders_Debug::$debug_file = dirname($this->plugin_main_file) . DIRECTORY_SEPARATOR . 'debug.php';
        }

        add_action(
            'admin_init',
            function () use ($plugin_main_file) {
                register_setting('wp-media-folders', 'wp-media-folders-tables');
                register_setting('wp-media-folders', 'wp-media-folders-options');

                // Enable queue asynchronous processing
                if (wp_script_is('heartbeat', 'registered')) {
                    // Use WP hearbeat
                    WPMediaFoldersQueue::initHeartbeat();
                }

                add_action('admin_footer', function () use ($plugin_main_file) {
                    WPMediaFoldersQueue::enqueueScript(plugin_dir_url($plugin_main_file));
                }, 0);

                // Add menu bar
                $options = get_option('wp-media-folders-options');
                if (isset($options['status_menu_bar'])) {
                    add_action(
                        'admin_bar_menu',
                        function (WP_Admin_Bar $wp_admin_bar) {
                            $args = array(
                                'id' => 'wpmfs-topbar',
                                'title' => '<a href="#"><span class="wpmfs"></span><span class="wpmfs-queue">0</span></a>',
                                'meta' => array(
                                    'classname' => 'wp-media-folders',
                                ),
                            );
                            $wp_admin_bar->add_node($args);
                        },
                        999
                    );

                    wp_register_style('wpmfs-dummy-handle', false);
                    wp_enqueue_style('wpmfs-dummy-handle');
                    wp_add_inline_style(
                        'wpmfs-dummy-handle',
                        '#wp-admin-bar-wpmfs-topbar a {
                            color: #FFF !important;                    
                          }
                          #wp-admin-bar-wpmfs-topbar span.wpmfs {
                            width: 10px;
                            height: 10px;
                            border-radius: 5px;
                            background-color: #969696;
                            display: inline-block;
                            vertical-align: baseline;
                            margin-right: 6px;
                          }
                          #wp-admin-bar-wpmfs-topbar span.wpmfs-querying {
                            opacity: 0.6;
                          }
                          #wp-admin-bar-wpmfs-topbar span.wpmfs-green {
                            background-color: #4caf50;
                          }
                          #wp-admin-bar-wpmfs-topbar span.wpmfs-orange {
                            background-color: #ff9800;
                          }'
                    );
                }
            }
        );

        add_action(
            'admin_menu',
            function () {
                add_options_page(
                    __('WP Media Folders', 'wp-media-folders'),
                    'WP Media Folders',
                    'manage_options',
                    'wp-media-folders-settings',
                    function () {
                        $this->tables = get_option('wp-media-folders-tables');
                        $this->options = get_option('wp-media-folders-options');
                        $this->disclaimer = get_option('wp-media-folders-disclaimer-confirmed');
                        // phpcs:ignore WordPress.WP.PreparedSQL.NotPrepared -- No variable needs to be prepared
                        $this->fields = WPMediaFoldersHelper::getDbColumns(true);

                        include_once dirname($this->plugin_main_file) . DIRECTORY_SEPARATOR . 'settings_view.php';
                    }
                );
            }
        );

        // Include WP Media Folder by Joomunited Integration
        if (defined('WPMF_TAXO')) {
            include_once 'wpmf.php';
            new WPMediaFoldersWPMF();
        }

        /**
         * Add an input to allow changing file path
         */
        add_filter(
            'attachment_fields_to_edit',
            function ($form_fields, $post) {
                $url = wp_get_attachment_url($post->ID);

                $uploads = wp_upload_dir();

                if (strpos($url, $uploads['baseurl'])!==0) {
                    $html = __('This file is not in the allowed upload folder', 'wp-media-folders');
                } else {
                    $path = str_replace($uploads['baseurl'], '', $url);

                    $file_extension = pathinfo($path, PATHINFO_EXTENSION);

                    $path = substr($path, 0, -(strlen($file_extension)+1));

                    $html = '<input name="attachments['.$post->ID.'][file_path]" id="attachments['.$post->ID.'][file_path]" value="'.htmlentities($path).'" /> . '.$file_extension;
                }

                $form_fields['file_path'] = array(
                'label' => __('File path', 'wp-media-folders'),
                'input' => 'html',
                'html' => $html,
                'helps' => __(sprintf('File path and name related to upload folder %s', '/' . substr($uploads['basedir'], strlen(get_home_path()))), 'wp-media-folders')
                );

                return $form_fields;
            },
            10,
            2
        );

        /**
         * Save modification made on media page
         */
        add_filter(
            'attachment_fields_to_save',
            function ($post, $attachment) {
                if (isset($attachment['file_path'])) {
                    $result = WPMediaFoldersQueue::addToQueue($post['ID'], $attachment['file_path'], true);

                    if (is_wp_error($result)) {
                        $post['errors']['file_path']['errors'][] = $result->get_error_message();
                        return $post;
                    }
                }

                return $post;
            },
            10,
            2
        );

        WPMediaFoldersQueue::initAjax();
    }

    /**
     * Check if the plugin need to run an update of db or options
     *
     * @return void
     */
    private function runUpgrades()
    {

        $version = get_option('wp-media-folders-version', '0.0.0');

        // Up to date, nothing to do
        if ($version === WP_MEDIA_FOLDERS_VERSION) {
            return;
        }

        global $wpdb;

        if (version_compare($version, '1.1.0') === -1) {
            $wpdb->query('CREATE TABLE `'.$wpdb->prefix.'wpmfs_queue` (
                      `id` int(11) NOT NULL,
                      `post_id` int(11) NOT NULL,
                      `destination` text NOT NULL,
                      `with_filename` tinyint(1) NOT NULL,
                      `delete_folder` tinyint(1) NOT NULL,
                      `date_added` varchar(14) NOT NULL,
                      `date_done` varchar(14) DEFAULT NULL,
                      `status` tinyint(1) NOT NULL
                    ) ENGINE=InnoDB');

            $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'wpmfs_queue`
                          ADD UNIQUE KEY `id` (`id`),
                          ADD KEY `date_added` (`date_added`,`status`);');

            $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'wpmfs_queue`
                          MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
        }

        if (version_compare($version, '1.1.8') === -1) {
            $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'wpmfs_queue` CONVERT TO CHARACTER SET '.$wpdb->charset.' COLLATE ' . $wpdb->collate);
            $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'wpmfs_queue` CHANGE `destination` `destination` LONGTEXT CHARACTER SET ' . $wpdb->charset . ' COLLATE ' . $wpdb->collate . ' NOT NULL;');
            $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'wpmfs_queue` CHANGE `date_added` `date_added` VARCHAR(14) CHARACTER SET ' . $wpdb->charset . ' COLLATE ' . $wpdb->collate . ' NOT NULL;');
            $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'wpmfs_queue` CHANGE `date_done` `date_done` VARCHAR(14) CHARACTER SET ' . $wpdb->charset . ' COLLATE ' . $wpdb->collate . ' DEFAULT NULL;');
        }

        if (version_compare($version, '1.1.9') === -1) {
            $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'wpmfs_queue` ADD `update_database` BOOLEAN NOT NULL AFTER `delete_folder`');

            $option = get_option('wp-media-folders-options');
            $option['replace_relative_paths'] = 'on';
            update_option('wp-media-folders-options', $option);
        }

        // Set version as nothing is already set
        if ($version === '0.0.0') {
            add_option('wp-media-folders-token', WPMediaFoldersHelper::getRandomString());

            $option_value = array(
                'auto_detect_tables' => 'on',
                'status_menu_bar' => 'on'
            );

            if (get_option('uploads_use_yearmonth_folders')) {
                $option_value['replace_relative_paths'] = 'on';
            }

            update_option('wp-media-folders-options', $option_value);
        }

        // Update the wpmfs version
        update_option('wp-media-folders-version', WP_MEDIA_FOLDERS_VERSION);


        // Set default options values
        $options = get_option('wp-media-folders-tables');
        if (!$options) {
            add_option(
                'wp-media-folders-tables',
                array(
                    'wp_posts' => array(
                        'post_content' => 1,
                        'post_excerpt' => 1
                    )
                )
            );
        }
    }
}
