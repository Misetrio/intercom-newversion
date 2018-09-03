<?php
if (!defined('ABSPATH')) {
    exit;
}
$cleanup = new WpsolDatabaseCleanup;

$check = array('', '', '', '', '', '', '', '', '', '', '');
if (class_exists('WpsolAddonDatabaseCleanup')) {
    $check = apply_filters('wpsol_addon_check_input_db_cleanup', $check);
}
$all_check = '';
if (!in_array('', $check)) {
    $all_check = 'checked="checked"';
}
?>
<div class="content-database-clean wpsol-optimization">
    <form method="post">
        <input type="hidden" name="action" value="wpsol_save_database" />
        <input type="hidden" name="page" value="database_cleanup" />
        <?php wp_nonce_field('wpsol_speed_optimization', '_wpsol_nonce'); ?>
        <div class="title">
            <label><?php esc_html_e('Database cleanup', 'wp-speed-of-light')?></label>
        </div>
        <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
        if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] && isset($_REQUEST['p']) && $_REQUEST['p'] === 'database_cleanup') : ?>
            <div id="message-speedup" class="ju-notice-success message-optimize">
                <strong><?php esc_html_e('Database cleanup successful', 'wp-speed-of-light'); ?></strong></div>
        <?php endif; ?>
        <div class="content">
            <?php
            $db_clean_checked = '';
            $db_cleansettings = get_option('wpsol_db_clean_addon');
            if (!empty($db_cleansettings['db_clean_auto'])) {
                $db_clean_checked = 'checked = "checked"';
            }
            $parameters = array('Days', 'Hours', 'Minutes');
            ?>

            <div class="left">
                <ul class="field">
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="db-clean-auto" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Active the database automatic cleanup', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Automatic cleanup', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="db-clean-auto" name="db-clean-auto"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                <?php echo esc_attr($db_clean_checked) ?> value="<?php echo esc_html($db_cleansettings['db_clean_auto']) ?>" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="right">
                <ul class="field">
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="clean-db-each" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Define automatic cleanup frequency', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Cleanup each', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>" style="right: 250px"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-input-select">
                            <input type="text" id="clean-db-each" class="clean-db-each ju-input" size="2"
                                <?php echo esc_attr($disabled_addon_attr) ?>
                                   name="clean-db-each" value="<?php echo esc_html($db_cleansettings['clean_db_each']) ?>">
                            <select name="clean-db-each-params" class="clean-db-each-params" <?php echo esc_attr($disabled_addon_attr) ?> style="vertical-align: bottom;display: inline-block">
                                <?php
                                foreach ($parameters as $k => $v) {
                                    $selected = '';
                                    if ($k === $db_cleansettings['clean_db_each_params']) {
                                        $selected = 'selected = "selected"';
                                    }
                                    echo '<option value="' . esc_html($k) . '" ' . esc_attr($selected) . '>' . esc_html($v) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </li>
                 </ul>
             </div>
            <div class="litte-left">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="data0" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Select all database ', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Select all', 'wp-speed-of-light') ?></label>
                        <input type="checkbox" id="data0" name="all_control"
                                <?php echo esc_attr($all_check); ?>
                               value="all_data" class="filled-in"/>
                        <label class="db-checkbox" for="data0"></label>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="data2" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('WordPress, by default, have an auto saving feature 
                       to restore content from the latest auto saved version. 
                       You don’t need auto saved content? cleanup!', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Clean all auto drafted content', 'wp-speed-of-light') ?>
                            <?php echo '&nbsp(' . esc_html($cleanup->getElementToClean('drafted')) . ')'; ?></label>
                        <input type="checkbox" id="data2" name="clean[]" <?php echo esc_attr($check[1]); ?> class="clean-data filled-in"
                               value="drafted"/>
                        <label class="db-checkbox" for="data2"></label>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="data4" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('All comments in trash or classified 
                       as spam will be cleaned up', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Remove comments from trash & spam', 'wp-speed-of-light') ?>

                            <?php echo '&nbsp(' . esc_html($cleanup->getElementToClean('comments')) . ')'; ?></label>
                        <input type="checkbox" id="data4" name="clean[]" <?php echo esc_attr($check[3]); ?> class="clean-data filled-in"
                               value="comments"/>
                        <label class="db-checkbox" for="data4"></label>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="data6" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Transient options is something like a basic cache system used by wordpress. 
                       No risk it’s regenerated by WordPress automatically', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Remove transient options', 'wp-speed-of-light') ?>

                            <?php echo '&nbsp(' . esc_html($cleanup->getElementToClean('transient')) . ')'; ?></label>
                        <input type="checkbox" id="data6" name="clean[]" <?php echo esc_attr($check[5]); ?> class="clean-data filled-in"
                               value="transient"/>
                        <label class="db-checkbox" for="data6"></label>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="data1" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('WordPress, by default, is generating content revisions (copy) 
                       to restore it from an old version. You don’t need revisions? cleanup!', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Clean all post revisions', 'wp-speed-of-light') ?>

                            <?php echo '&nbsp(' . esc_html($cleanup->getElementToClean('revisions')) . ')'; ?></label>
                        <input type="checkbox" id="data1" name="clean[]" <?php echo esc_attr($check[0]); ?> class="clean-data filled-in"
                               value="revisions"/>
                        <label class="db-checkbox" for="data1"></label>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="data3" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('All content (post, page…) in trash will be cleaned up', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Remove all trashed content', 'wp-speed-of-light') ?>

                            <?php echo '&nbsp(' . esc_html($cleanup->getElementToClean('trash')) . ')'; ?></label>
                        <input type="checkbox" id="data3" name="clean[]" <?php echo esc_attr($check[2]); ?> class="clean-data filled-in"
                               value="trash"/>
                        <label class="db-checkbox" for="data3"></label>
                    </li>

                </ul>
            </div>
            <div class="litte-right">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="data5" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Trackbacks and pingbacks are methods for alerting blogs
                        that you have linked to them. You don’t need revisions? Cleanup!', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Remove trackbacks and pingbacks', 'wp-speed-of-light') ?>

                            <?php echo '&nbsp(' . esc_html($cleanup->getElementToClean('trackbacks')) . ')'; ?></label>
                        <input type="checkbox" id="data5" name="clean[]" <?php echo esc_attr($check[4]); ?> class="clean-data filled-in"
                               value="trackbacks"/>
                        <label class="db-checkbox" for="data5"></label>
                    </li>




                   <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="data7" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Remove duplicated post meta from the database', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Duplicated post meta ', 'wp-speed-of-light') ?>
                            &nbsp(<?php echo esc_html($cleanup->getElementToClean('dup_postmeta')) ?>)
                        </label>

                       <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                            alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <input type="checkbox" id="data7" name="clean[]" <?php echo esc_attr($check[6]) ?>
                            <?php echo esc_attr($disabled_addon_attr) ?>
                        class="clean-data filled-in" value="dup_postmeta"/>
                        <label class="db-checkbox" for="data7"></label>
                    </li>
                   <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="data8" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Remove duplicated comment meta from the database', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Duplicated comment meta', 'wp-speed-of-light') ?>
                            &nbsp(<?php echo esc_html($cleanup->getElementToClean('dup_commentmeta')) ?>)
                        </label>

                       <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                            alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <input type="checkbox" id="data8" name="clean[]" <?php echo esc_attr($check[7]) ?>
                            <?php echo esc_attr($disabled_addon_attr) ?>
                        class="clean-data filled-in" value="dup_commentmeta"/>
                        <label class="db-checkbox" for="data8"></label>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="data9" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Remove duplicated user meta from the database', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Duplicated user meta', 'wp-speed-of-light') ?>
                            &nbsp(<?php echo esc_html($cleanup->getElementToClean('dup_usermeta')) ?>)
                        </label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <input type="checkbox" id="data9" name="clean[]" <?php echo esc_attr($check[8]) ?>
                            <?php echo esc_attr($disabled_addon_attr) ?>
                        class="clean-data filled-in" value="dup_usermeta"/>
                        <label class="db-checkbox" for="data9"></label>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="data10" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Remove duplicated term meta post meta from the database', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Duplicated term meta', 'wp-speed-of-light') ?>
                            &nbsp(<?php echo esc_html($cleanup->getElementToClean('dup_termmeta')) ?>)
                        </label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <input type="checkbox" id="data10" name="clean[]" <?php echo esc_attr($check[9]) ?>
                            <?php echo esc_attr($disabled_addon_attr) ?>
                        class="clean-data filled-in" value="dup_termmeta"/>
                        <label class="db-checkbox" for="data10"></label>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="data11" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('You can use OPTIMIZE TABLE to
                reclaim the unused space and to defragment the data file', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Optimize database tables', 'wp-speed-of-light') ?>
                            &nbsp(<?php echo esc_html($cleanup->getElementToClean('optimize_table')) ?> tables)
                        </label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <input type="checkbox" id="data11" name="clean[]" <?php echo esc_attr($check[10]) ?>
                            <?php echo esc_attr($disabled_addon_attr) ?>
                        class="clean-data filled-in" value="optimize_table"/>
                        <label class="db-checkbox" for="data11"></label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clear"></div>
        <div class="footer">
            <button type="submit"
                   class="ju-button orange-button waves-effect waves-light" id="speed-optimization">
                <?php esc_html_e('Clean & Save', 'wp-speed-of-light'); ?>
            </button>

        </div>
    </form>
</div>
