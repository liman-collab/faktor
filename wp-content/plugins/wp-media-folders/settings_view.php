<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');

wp_enqueue_style('wp-media-folders-settings', plugins_url(null, __FILE__).'/assets/css/settings.css');
wp_enqueue_script('wp-media-folders-settings', plugins_url(null, __FILE__).'/assets/js/settings.js');
global $wpdb;
?>

<script>var wpmfs_nonce = "<?php echo wp_create_nonce('wpmfs_nonce'); ?>";</script>

<div class="wrap wp-media-folders-settings">
    <h1>WP Media folders settings</h1>

    <?php if (!$this->disclaimer): ?>
    <div id="wpmfs-disclaimer">
        <h2>Please read this disclaimer first</h2>
        <p>
            WP Media Folders will move WordPress media inside real folders and edit filenames.
            In its default version it will allow you to edit the file path and name through the image edition in the WordPress default media manager.
        </p>
        <p>
            The plugin has a special integration with <a target="_blank" href="https://www.joomunited.com/wordpress-products/wp-media-folder">JoomUnited's plugin WP Media Folder</a>. If installed, all media organization made through this plugin will be reflected into real folders on your server.
        </p>
        <h2>Important restrictions</h2>
        <p>
            WordPress has not been designed to allow changing the path of files. You should use this plugin only if you really need it but note that:
        </p>
        <p>
            <ul>
                <li>
                    <strong>Moving images won't help your SEO</strong> like other would expect you to believe, file name and Alt information are far more important.
                </li>
                <li>
                    Even if the plugin will try its best to find and replace URLs of files in your database, depending on how other plugins deals with URLs, it may fail
                </li>
                <li>The process of replacing requires strong server performances and the more content you have the more powerful your server has to be</li>
                <li><strong>Always make backups of your website before any modification</strong></li>
            </ul>
        </p>
        <a id="wpmfs-hide-disclaimer" class="button">I have read the disclaimer, hide it please.</a>
    </div>
    <?php endif; ?>

    <form method="post" action="options.php">
        <div>
            <h2><?php esc_html_e('Main settings', 'wp-media-folders'); ?></h2>
            <div class="container">
                <input
                        type="checkbox"
                        name="wp-media-folders-options[auto_detect_tables]"
                    <?php echo isset($this->options['auto_detect_tables'])?'checked':''; ?>
                    />
                <strong><?php esc_html_e('Detect media tables to replace content', 'wp-media-folders'); ?></strong>
                <p class="hint">
                <?php
                echo esc_html__('The plugin will auto select the tables and columns where the replacement of attachments (media) URLs should be proceeded. This is the better option if you want to make sure to not loose replacements.', 'wp-media-folders').'<br/>';
                echo esc_html__('Disable this option if you know what you’re doing and want to select custom data set  to optimize the process.', 'wp-media-folders').'<br/>';
                ?>
                </p>
            </div>
        </div>

        <div id="table_replace" style="<?php echo isset($this->options['auto_detect_tables'])?'display:none':''; ?>">
            <p>
                <strong><?php esc_html_e('Tables to replace content into', 'wp-media-folders'); ?></strong><br/>
                <?php esc_html_e('Select the tables which you want the images url to be replaced into', 'wp-media-folders'); ?>
            </p>
            <div class="container">
                <?php
                settings_fields('wp-media-folders');
                do_settings_sections('wp-media-folders');

                $last_table = '';
                foreach ($this->fields as $field) {
                    if ($last_table !== $field->TABLE_NAME) {
                        if ($last_table!=='') {
                            echo '</div>';
                        }
                        $last_table = $field->TABLE_NAME;
                        ?><div class="database-table"><h2><?php echo esc_html($last_table); ?></h2><?php
                    }
                    ?>

                    <div class="database-field">
                        <span><?php echo esc_html($field->COLUMN_NAME); ?></span>
                        <span><input
                                type="checkbox"
                                name="wp-media-folders-tables[<?php echo esc_html($last_table); ?>][<?php echo esc_html($field->COLUMN_NAME); ?>]"
                                <?php echo isset($this->tables[$last_table][$field->COLUMN_NAME])?'checked':''; ?>
                        /></span>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>

        <div>
            <div class="container">
                <strong><?php esc_html_e('Synchronize media with WP Media Folder', 'wp-media-folders'); ?></strong>
                <button id="sync_wpmf" class="button" <?php echo defined('WPMF_TAXO')?'':('disabled="disabled" title="'.esc_html__('This functionnality requires WP Media Folder from Joomunited plugin', 'wp-media-folders').'"'); ?>>
                    <?php esc_html_e('Move existing media', 'wp-media-folders'); ?>
                </button>
                <span id="sync_wpmf_doing" style="display: none"><?php esc_html_e('Media will be moved asynchronously in backgound', 'wp-media-folders'); ?></span>

                <p class="hint">
                    <?php
                    // phpcs:ignore WordPress.XSS.EscapeOutput -- Already escaped
                    echo sprintf(esc_html__('This functionality requires the WP Media Folder plugin from JoomUnited to be installed (%s)', 'wp-media-folders'), '<a target="_blank" href="https://www.joomunited.com/wordpress-products/wp-media-folder">WP Media Folder</a>.').'<br/>';
                    echo esc_html__('WP Media Folder from JoomUnited allows to manage folders in the WordPress Media Library.', 'wp-media-folders').'<br/>';
                    ?>
                </p>
            </div>
        </div>

        <div>
            <div class="container">
                <input
                        type="checkbox"
                        name="wp-media-folders-options[status_menu_bar]"
                    <?php echo isset($this->options['status_menu_bar'])?'checked':''; ?>
                />
                <strong><?php esc_html_e('Show status menu bar', 'wp-media-folders'); ?></strong>
                <p class="hint">
                    <?php
                    // phpcs:ignore WordPress.XSS.EscapeOutput -- Already escaped
                    echo esc_html__('Show the number of attachments waiting to be processed in the admin menu bar.', 'wp-media-folders');
                    echo esc_html__('To prevent PHP timeout errors during attachment moving, the process is done asynchronously in background.', 'wp-media-folders');
                    ?>
                </p>
            </div>
        </div>

        <div id="full_search" style="<?php echo !isset($this->options['auto_detect_tables'])?'display:none':''; ?>">
            <h2><?php esc_html_e('Advanced settings', 'wp-media-folders'); ?></h2>
            <div class="container">
                <input
                        type="checkbox"
                        name="wp-media-folders-options[search_full_database]"
                    <?php echo isset($this->options['search_full_database'])?'checked':''; ?>
                />
                <strong><?php echo sprintf(esc_html('Search into full database instead of only "%s" prefixed tables', 'wp-media-folders'), $wpdb->prefix); ?></strong>
                <p class="hint">
                    <?php
                    echo esc_html__('If checked, the plugin will not only replace content in your wordpress tables but in all the table it will find in the database.', 'wp-media-folders').'<br/>';
                    echo esc_html__('It could be usefull if you use your attachments links in another cms or custom script.', 'wp-media-folders').'<br/>';
                    echo esc_html__('If you don\'t specifically need it, leave this option unchecked ', 'wp-media-folders').'<br/>';
                    ?>
                </p>
            </div>
            <div class="container">
                <input
                        type="checkbox"
                        name="wp-media-folders-options[replace_relative_paths]"
                    <?php echo isset($this->options['replace_relative_paths'])?'checked':''; ?>
                />
                <strong><?php echo sprintf(esc_html('Replace relative paths in database', 'wp-media-folders')); ?></strong>
                <p class="hint">
                    <?php
                    echo esc_html__('By default WordPress uses absolutes urls, but some plugins may use relative path.', 'wp-media-folders').'<br/>';
                    echo esc_html__('If checked, the plugin will try to replace also relative path in database, instead of only absolute urls.', 'wp-media-folders').'<br/>';
                    echo esc_html__('Replacements in database may fail in particular cases (files with the same name) if this option is used while you\'re not using the default year/month upload folders option in WordPress settings', 'wp-media-folders').'<br/>';
                    ?>
                </p>
            </div>
        </div>

        <h2><?php esc_html_e('Debug', 'wp-media-folders'); ?></h2>

        <div class="container">
            <input
                    type="checkbox"
                    name="wp-media-folders-options[mode_debug]"
                <?php echo isset($this->options['mode_debug'])?'checked':''; ?>
            />
            <strong><?php esc_html_e('Mode debug activated', 'wp-media-folders'); ?></strong>

            <p class="hint"><?php esc_html_e('When enabled, all actions made by the plugin will be stored into a log file in the plugin folder', 'wp-media-folders'); ?></p>
        </div>

        <?php submit_button(); ?>

    </form>
</div>