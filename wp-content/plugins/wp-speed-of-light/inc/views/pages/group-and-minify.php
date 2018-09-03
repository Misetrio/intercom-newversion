<?php
if (!defined('ABSPATH')) {
    exit;
}
$optimization = get_option('wpsol_optimization_settings');

$disabled_addon_class = 'addon-disabled';
$disabled_addon_attr = 'disabled="disabled"';
$disabled_panel = '';
if (class_exists('WpsolAddonSpeedOptimization')) {
    $disabled_addon_class = '';
    $disabled_addon_attr = '';
    $disabled_panel = 'pannel-addon-enabled';
}
?>
<div class="content-group-minify wpsol-optimization">
    <form class="" method="post">
        <input type="hidden" name="action" value="wpsol_save_minification">
        <input type="hidden" name="page" value="group_and_minify" />
        <?php wp_nonce_field('wpsol_speed_optimization', '_wpsol_nonce'); ?>
        <div class="title">
            <label><?php esc_html_e('Group & Minify', 'wp-speed-of-light')?></label>
        </div>
        <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
        if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] && isset($_REQUEST['p']) && $_REQUEST['p'] === 'group_and_minify') :  ?>
            <div id="message-group_and_minify" class="ju-notice-success message-optimize">
                <strong><?php esc_html_e('Setting saved', 'wp-speed-of-light'); ?></strong></div>
        <?php endif; ?>
        <div class="content">
            <div class="left">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="html-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Minification refers to the process
                                    of removing unnecessary or redundant data
                                    without affecting how the resource is processed
                                     by the browser - e.g. code comments and formatting,
                                     removing unused code, using shorter variable
                                      and function names, and so on', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('HTML minification', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" class="wpsol-minification" id="html-minification"
                                       name="html-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        $optimization['advanced_features']['html_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="js-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Minification refers to the process of removing unnecessary 
                                   or redundant data without affecting how the resource 
                                   is processed by the browser - e.g. code comments and 
                                   formatting, removing unused code,
                                   using shorter variable and function names, and so on', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('JS minification', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="js-minification"
                                       name="js-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        $optimization['advanced_features']['js_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>
                                >
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="jsGroup-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Grouping several Javascript files into a single file will minimize the HTTP
                            requests number.Use with caution and test your website,
                             it may generates conflicts', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Group JS', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="jsGroup-minification"
                                       name="jsgroup-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        $optimization['advanced_features']['jsgroup_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <?php
                    $ex_inline_checked = '';
                    if (!empty($optimization) &&
                        isset($optimization['advanced_features']['exclude_inline_script']) &&
                        $optimization['advanced_features']['exclude_inline_script'] === 1) {
                        $ex_inline_checked = 'checked="checked"';
                    }
                    ?>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="exclude-inline-script" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Exclude inline script from minification', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Exclude inline script', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="exclude-inline-script"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                       name="exclude-inline-script" <?php echo esc_attr($ex_inline_checked) ?>
                                value="<?php echo esc_html((isset($optimization['advanced_features']['exclude_inline_script'])) ? (int)$optimization['advanced_features']['exclude_inline_script'] : 0) ?>" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="right">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="css-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Minification refers to the process of removing unnecessary or redundant data
                            without affecting how the resource is processed 
                            by the browser - e.g. code comments and formatting, 
                            removing unused code, using shorter variable and
                             function names, and so on', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('CSS minification', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="css-minification"
                                       name="css-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        $optimization['advanced_features']['css_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>
                                >
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="cssGroup-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Grouping several CSS files into a single file will
                            minimize the HTTP requests number.
                            Use with caution and test your website,
                             it may generates conflicts', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Group CSS', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="cssGroup-minification"
                                       name="cssgroup-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        $optimization['advanced_features']['cssgroup_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <?php
                    $fontGrchecked = '';
                    $moveScriptchecked = '';
                    if (!empty($optimization) &&
                        isset($optimization['advanced_features']['fontgroup_minification']) &&
                        $optimization['advanced_features']['fontgroup_minification'] === 1) {
                        $fontGrchecked = 'checked="checked"';
                    }
                    if (!empty($optimization) &&
                        isset($optimization['advanced_features']['move_script_to_footer']) &&
                        $optimization['advanced_features']['move_script_to_footer'] === 1) {
                        $moveScriptchecked = 'checked="checked"';
                    }

                    $excludeScriptOutput = '';
                    if (!empty($optimization) && !empty($optimization['advanced_features']['exclude_move_to_footer'])) {
                        $excludeScriptOutput = implode("\n", $optimization['advanced_features']['exclude_move_to_footer']);
                    }
                    ?>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="fontGroup-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Group local fonts and Google fonts
              in a single file to be served faster.', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Group fonts and Google fonts', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="fontGroup-minification"
                                       name="fontgroup-minification" <?php echo esc_attr($fontGrchecked) ?>
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                value="<?php echo esc_html((isset($optimization['advanced_features']['fontgroup_minification'])) ? (int)$optimization['advanced_features']['fontgroup_minification'] : 0) ?>" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>

                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="move-script-to-footer" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Move all minified scripts to footer', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Move scripts to footer', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="move-script-to-footer"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                       name="move-script-to-footer" <?php echo esc_attr($moveScriptchecked) ?>
                                value="<?php echo esc_html((isset($optimization['advanced_features']['move_script_to_footer'])) ? (int)$optimization['advanced_features']['move_script_to_footer'] : 0) ?>"/>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>

                    <li class="exclude-script-mtf ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>" style="display: none">
                        <label for="exclude-move-script-to-footer" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Add the script of the pages you want to exclude
                        from move to footer (one URL per line)', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Exclude script move to footer : ', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <p><textarea cols="100" rows="7" <?php echo esc_attr($disabled_addon_attr) ?>
                                     id="exclude-move-script-to-footer" class="wpsol-minification"
                                     name="exclude-move-script-to-footer"><?php echo esc_textarea($excludeScriptOutput) ?></textarea></p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clear"></div>

        <div class="footer" style="margin-bottom: 50px">
            <button type="submit"
                   class="ju-button orange-button waves-effect waves-light" id="speed-optimization">
                <span><?php esc_html_e('Save', 'wp-speed-of-light'); ?></span>
            </button>
        </div>

        <ul>
            <?php

            $excludeFilesChecked = '';
            if (!empty($optimization) &&
                isset($optimization['advanced_features']['excludefiles_minification']) &&
                $optimization['advanced_features']['excludefiles_minification'] === 1) {
                $excludeFilesChecked = 'checked="checked"';
            }
            ?>

            <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                <label for="excludeFiles-minification" class="text speedoflight_tool ju-setting-label"
                       alt="<?php esc_html_e('Advanced file exclusion', 'wp-speed-of-light') ?>">
                    <?php esc_html_e('ADVANCED FILE EXCLUSION', 'wp-speed-of-light') ?></label>

                <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                     alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                    <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                <div class="ju-switch-button">
                    <label class="switch ">
                        <input type="checkbox" class="wpsol-minification" id="excludeFiles-minification"
                               name="excludeFiles-minification" <?php echo esc_attr($excludeFilesChecked) ?>
                            <?php echo esc_attr($disabled_addon_attr) ?>
                        value="<?php echo esc_html((isset($optimization['advanced_features']['excludefiles_minification'])) ? (int)$optimization['advanced_features']['excludefiles_minification'] : 0) ?>" />
                        <div class="slider"></div>
                    </label>
                </div>
            </li>
            <?php
            if (class_exists('WpsolAddonSpeedOptimization')) {
                require_once(WPSOL_ADDON_PLUGIN_DIR . 'views/speed-optimization-exclude-files.php');
            }
            ?>
        </ul>
    </form>
</div>

<?php
if (class_exists('WpsolAddonSpeedOptimization')) {
    do_action('wpsol_addon_add_advanced_file_popup');
}
?>
<!--Dialog-->
<div id="wpsol_check_minify_modal" class="check-minify-dialog" style="display: none">
    <div class="check-minify-icon"><i class="material-icons">info_outline</i></div>
    <div class="check-minify-title"><h2><?php esc_html_e('File minification activation', 'wp-speed-of-light'); ?></h2></div>
    <div class="check-minify-content">
        <span><?php esc_html_e('Check carefully the minification effects 
        on your website, this is a very advanced optimization. If you encounter some errors
         you need to consider disabling it, it has a small impact on performance.', 'wp-speed-of-light'); ?></span>
    </div>
    <div class="check-minify-sucess">
        <button type="button" data-type="" id="agree" class="agree ju-button orange-button waves-effect waves-light">
            <span><?php esc_html_e('OK activate it', 'wp-speed-of-light') ?></span>
        </button>

        <input type="button" class="cancel" value="<?php esc_html_e('Cancel', 'wp-speed-of-light') ?>">
    </div>
</div>
