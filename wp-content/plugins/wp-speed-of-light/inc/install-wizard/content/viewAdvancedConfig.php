<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<form method="post">
    <?php wp_nonce_field('wpsol-setup-wizard', 'wizard_nonce'); ?>
    <div class="advanced-optimization-header">
        <div class="title"><?php esc_html_e('Advanced Configuration', 'wp-speed-of-light'); ?></div>
        <p>
            <?php esc_html_e('This features may not be applicable to all wordpress websites depending of the server and 
            the plugin used . Please run a full test on your website before considering keeping those options to Yes', 'wp-speed-of-light'); ?>
        </p>
    </div>
    <div class="advanced-optimization-content configuration-content">
        <div class="minification-container first-container">
            <div class="title"><?php esc_html_e('Resources minification', 'wp-speed-of-light'); ?></div>
            <table>
                <tr>
                    <td width="85%"><label for="minify_html"><?php esc_html_e('HTML minification', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="minify_html"
                                   name="minify_html"
                                   value="1"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="minify_css"><?php esc_html_e('Css minification', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="minify_css"
                                   name="minify_css"
                                   value="1"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="minify_js"><?php esc_html_e('JS minification', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="minify_js"
                                   name="minify_js"
                                   value="1"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        <div class="group-container second-container">
            <div class="title"><?php esc_html_e('Resources group', 'wp-speed-of-light'); ?></div>
            <table>
                <tr>
                    <td width="85%"><label for="group_css"><?php esc_html_e('Group CSS', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="group_css"
                                   name="group_css"
                                   value="1"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="group_js"><?php esc_html_e('Group JS', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="group_js"
                                   name="group_js"
                                   value="1"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="advanced-optimization-footer configuration-footer">
        <input type="submit" value="Continue" class="" name="wpsol_save_step" />
    </div>
</form>
