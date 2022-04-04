<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

/**
 * Class WP_Media_Folder
 *
 * Integration with WP Media Folder plugin from Joomunited
 */
class WPMediaFoldersWPMF
{

    /**
     * All WPMF terms
     *
     * @var array
     */
    protected $terms = null;

    /**
     * WPMediaFoldersWPMF constructor.
     */
    public function __construct()
    {
        /**
         * Hook on the set attachment folder action
         */
        add_action(
            'wpmf_attachment_set_folder',
            function ($attachment_id, $folder, $extra) {
                $update_db = true;
                if (is_array($extra) && !empty($extra['trigger']) && $extra['trigger'] === 'upload') {
                    $update_db = false;
                }

                $folders = WPMediaFoldersHelper::getParentTerms($folder);
                WPMediaFoldersQueue::addToQueue($attachment_id, implode(DIRECTORY_SEPARATOR, $folders), false, false, $update_db);
                // Move files at the end of the script to avoid thumbnails generation issues
                add_action('shutdown', function () {
                    WPMediaFoldersQueue::proceedQueueAsync();
                });
            },
            10,
            3
        );

        /**
         * Hook on the add attachment action
         *
         * @todo : hook on wpmf_after_attachment_import to trigger the peoceedQueueAsync
         */
        add_action(
            'wpmf_add_attachment',
            function ($attachment_id, $folder_id) {
                $folders = WPMediaFoldersHelper::getParentTerms($folder_id);
                WPMediaFoldersQueue::addToQueue($attachment_id, implode(DIRECTORY_SEPARATOR, $folders), false);
            },
            10,
            2
        );

        /**
         * Hook on the move folder action
         */
        add_action(
            'wpmf_move_folder',
            function ($folder_id, $destination_folder_id) {
                $term = get_term($destination_folder_id, WPMF_TAXO);
                WPMediaFoldersHelper::updateFolderName($folder_id, $term->name);
            },
            2,
            2
        );

        /**
         * Hook on the update folder name action
         */
        add_action(
            'wpmf_update_folder_name',
            function ($folder_id, $folder_name) {
                WPMediaFoldersHelper::updateFolderName($folder_id, $folder_name);
            },
            2,
            2
        );

        /**
         * Hook on the delete folder action
         */
        add_action(
            'wpmf_delete_folder',
            function ($folder_term) {
                WPMediaFoldersHelper::deleteFolder($folder_term);
            },
            2,
            2
        );

        /**
         * Ajax syncchonize folders
         */
        add_action('wp_ajax_wpmfs_import_wpmf', function () {
            check_ajax_referer('wpmfs_nonce', 'nonce');

            WPMediaFoldersHelper::updateFolderName(0, '');
            WPMediaFoldersQueue::proceedQueueAsync();

            exit(0);
        });

        add_filter('http_request_args', function($r, $url) {
                if (is_array($r["body"]) && !empty($r["body"]['action']) && $r["body"]['action']==='wp_async_wp_generate_attachment_metadata' && is_array($r["body"]['metadata'])) {
                    unset($r["body"]['metadata']);
                }
                return $r;
            },
            10,
            2
        );
    }
}