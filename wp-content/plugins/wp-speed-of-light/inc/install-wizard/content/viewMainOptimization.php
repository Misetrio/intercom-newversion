<?php
if (!defined('ABSPATH')) {
    exit;
}
$parameters = array('Days', 'Hours', 'Minutes');
?>
<form method="post">
    <?php wp_nonce_field('wpsol-setup-wizard', 'wizard_nonce'); ?>
    <div class="main-optimization-header">
        <div class="title"><?php esc_html_e('Main optimization', 'wp-speed-of-light'); ?></div>
    </div>
    <div class="main-optimization-content configuration-content">
        <div class="activate-container first-container">
            <div class="title"><?php esc_html_e('Activate cache system', 'wp-speed-of-light'); ?></div>
            <table>
                <tr>
                    <td width="85%"><label for="active_cache"><?php esc_html_e('Activate cache system', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="active_cache"
                                   name="active_cache"
                                   value="1"
                                   checked="checked"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="clean_each"><?php esc_html_e('Clean each', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%" style="position: relative;">
                        <div class="clean-each-option">
                            <input type="text" class="clean-each-text" id="clean_each" size="2"
                                   name="clean_each"
                                   value="40">
                            <select class="clean-each-params" name="clean_each_params">
                                <?php
                                $checked = '';
                                foreach ($parameters as $k => $v) {
                                    if ($k === 2) {
                                        $checked = 'selected="selected"';
                                    }
                                    $selected = '';
                                    echo '<option '.esc_attr($checked).' value="' . esc_html($k) . '" ' . esc_attr($selected) . '>' . esc_html($v) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="wordpress-container second-container">
            <div class="title"><?php esc_html_e('Wordpress optimization', 'wp-speed-of-light'); ?></div>
            <table>
                <tr>
                    <td width="85%"><label for="remove_query"><?php esc_html_e('Remove query strings', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="remove_query"
                                   name="remove_query"
                                   value="1"
                                   checked="checked"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="add_expired"><?php esc_html_e('Add expired headers', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="add_expired"
                                   name="add_expired"
                                   value="1"
                                   checked="checked"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="external_script"><?php esc_html_e('Cache external script', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="external_script"
                                   name="external_script"
                                   value="1"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="disable_rest"><?php esc_html_e('Disable REST API', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="disable_rest"
                                   name="disable_rest"
                                   value="1"
                                   checked="checked"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="disable_rss"><?php esc_html_e('Disable RSS Feed', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="disable_rss"
                                   name="disable_rss"
                                   value="1"
                                   checked="checked"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="main-optimization-footer configuration-footer">
        <input type="submit" value="Continue" class="" name="wpsol_save_step" />
    </div>
</form>
