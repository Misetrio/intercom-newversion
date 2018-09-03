<?php
if (!defined('ABSPATH')) {
    exit;
}
$config = get_option('wpsol_configuration');

$roles = array(
    'author' => 'Authors',
    'editor' => 'Editors',
    'contributor' => 'Contributors',
    'subscriber' => 'Subscribers'
);

// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
if (isset($_REQUEST['import'])) {
    if ($_REQUEST['import'] === 'config-incorrect') {
        $notice = esc_html__('Configuration incorrect', 'wp-speed-of-light');
        $class_notice = 'notice-error';
    }
    if ($_REQUEST['import'] === 'file-type-incorrect') {
        $notice = esc_html__('File type incorrect', 'wp-speed-of-light');
        $class_notice = 'notice-error';
    }
    if ($_REQUEST['import'] === 'config-imported') {
        $notice = esc_html__('Configuration imported', 'wp-speed-of-light');
        $class_notice = 'notice-success';
    }
    if ($_REQUEST['import'] === 'import-error') {
        $notice = esc_html__('Update to option occurs', 'wp-speed-of-light');
        $class_notice = 'notice-error';
    }
}
// phpcs:enable
?>
<div class="content-configuration">
    <div class="ju-top-tabs-wrapper">
        <ul class="tabs ju-top-tabs">
            <li class="tab">
                <a class="link-tab waves-effect waves-light" href="#general">
                    <?php esc_html_e('General Settings', 'wp-speed-of-light') ?>
                </a>
            </li>
            <li class="tab">
                <a data-tab-id="import-export" class="link-tab waves-effect waves-light" href="#im_export">
                    <?php esc_html_e('Import/Export', 'wp-speed-of-light') ?>
                </a>
            </li>
            <li class="tab">
                <a data-tab-id="translation" class="link-tab waves-effect waves-light" href="#translation">
                    <?php esc_html_e('Translation tool', 'wp-speed-of-light') ?>
                </a>
            </li>
            <div class="indicator" style="right: 400px; left: 0px;"></div>
        </ul>
    </div>
    <div id="general" class="tab-content" style="display: none">
        <div class="wpsol-optimization">
            <form method="post">
                <input type="hidden" name="action" value="wpsol_save_configuration">
                <input type="hidden" name="page" value="configuration" />
                <?php wp_nonce_field('wpsol_speed_optimization', '_wpsol_nonce'); ?>
                <div class="title">
                    <label><?php esc_html_e('Configuration', 'wp-speed-of-light')?></label>
                </div>
                <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
                if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] && isset($_REQUEST['p']) && $_REQUEST['p'] === 'configuration') :  ?>
                    <div id="message-configuration" class="ju-notice-success message-optimize">
                        <strong><?php esc_html_e('Setting saved', 'wp-speed-of-light'); ?></strong></div>
                <?php endif; ?>
                <div class="content">
                    <div class="left">
                        <ul class="field">
                            <li class="ju-settings-option full-width">
                                <label for="config2" class="speedoflight_tool ju-setting-label"
                                       alt="<?php esc_html_e('Display a button in the topbar 
                           to clean all the site cache', 'wp-speed-of-light') ?>">
                                    <?php esc_html_e('Display clean cache in top toolbar', 'wp-speed-of-light'); ?>
                                </label>
                                <div class="ju-switch-button">
                                    <label class="switch ">
                                        <input type="checkbox" class="wpsol-optimization" id="config2"
                                               name="display_clean"
                                               value="1"
                                            <?php
                                            if (!empty($config)) {
                                                if ($config['display_clean'] === 1) {
                                                    echo 'checked="checked"';
                                                }
                                            } ?>
                                        />
                                        <div class="slider"></div>
                                    </label>
                                </div>
                            </li>
                            <li class="ju-settings-option full-width">
                                <label for="config3" class="speedoflight_tool ju-setting-label"
                                       alt="<?php esc_html_e('WebPageTest API key required to run the speed analysis tool. 
                           Allows 200 page speed test per day.
                           Hit the button to get one, it’s FREE!', 'wp-speed-of-light') ?>">
                                    <?php esc_html_e('Your WebPagetest API Key :', 'wp-speed-of-light'); ?></label>
                                <input type="text" size="50" id="config3" class="ju-input" name="webtest_api_key"
                                       value="<?php
                                        if (!empty($config['webtest_api_key'])) {
                                            echo esc_html($config['webtest_api_key']);
                                        } else {
                                            echo '' ;
                                        } ?>"/>
                                <div class="note">
                                    <b>Note:&nbsp;</b>
                                    <span>
                                        <?php esc_html_e('Paste the WebPageTest API key to run speed tests', 'wp-speed-of-light') ?>
                                    </span>
                                </div>
                            </li>

                        </ul>
                    </div>
                    <div class="right">
                        <ul class="field">
                            <li class="ju-settings-option full-width">
                                <label for="config1" class="speedoflight_tool ju-setting-label"
                                       alt="<?php esc_html_e('When an admin is Logged In you can disable 
                           speed optimization (cache, compression…)', 'wp-speed-of-light') ?>">
                                    <?php esc_html_e('Disable optimization for admin users', 'wp-speed-of-light'); ?></label>
                                <div class="ju-switch-button wpsol-option-configuration">
                                    <label class="switch ">
                                        <input type="checkbox" class="wpsol-optimization" id="config1" name="disable_user"
                                               value="1"
                                            <?php
                                            if (!empty($config)) {
                                                if ($config['disable_user'] === 1) {
                                                    echo 'checked="checked"';
                                                }
                                            }
                                            ?>
                                        />
                                        <div class="slider"></div>
                                    </label>
                                </div>
                            </li>
                            <li class="ju-settings-option full-width field-block addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                                <label for="disable-user-roles"
                                       class="text speedoflight_tool ju-setting-label"
                                       alt="<?php esc_html_e('Disable cache and optimization system
              for those user roles (when loggedIn)', 'wp-speed-of-light') ?>"
                                ><?php esc_html_e('Disable optimization for', 'wp-speed-of-light') ?></label>

                                <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                                     alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                                    <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                                <div class="wpsol-roles-configuration">
                                    <ul>
                                        <?php foreach ($roles as $k => $v) :
                                            $checked = '';
                                            if (isset($config['disable_roles']) && in_array($k, $config['disable_roles'])) {
                                                $checked = 'checked = "checked"';
                                            } ?>
                                        <li><input type="checkbox" class="filled-in" id="<?php echo esc_attr($k) ?>" name="disable_roles[]"
                                                <?php echo esc_attr($disabled_addon_attr) ?>
                                                   value="<?php echo esc_html($k) ?>"
                                            <?php echo esc_attr($checked) ?> />
                                            <label class="roles-label" for="<?php echo esc_attr($k) ?>"><?php echo esc_html($v) ?></label>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div style="clear: both"></div>
                            </li>;
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="footer">
                    <div class="left-footer">
                        <a href="https://www.webpagetest.org/getkey.php" style="background: #F4EAE4"
                           class="ju-button orange-outline-button waves-effect waves-light" target="_blank"><?php esc_html_e('Get an api key', 'wp-speed-of-light'); ?></a>
                    </div>
                    <div class="right-footer">
                        <button type="submit"
                                style="margin-right: 12px;"
                                class="ju-button orange-button waves-effect waves-light" id="speed-optimization">
                            <span><?php esc_html_e('Save settings', 'wp-speed-of-light'); ?></span>
                        </button>

                        <button type="button"
                                class="ju-button orange-outline-button waves-effect waves-dark clean-cache-button" name="clean-cache-button">
                            <span><?php esc_html_e('Clean Cache', 'wp-speed-of-light'); ?></span>
                        </button>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>
    <div id="im_export" class="tab-content" style="display: none;">
        <div class="wpsol-optimization import-export">
            <div class="title" style="height: 115px !important; width: auto">
                <label><?php esc_html_e('Import & Export', 'wp-speed-of-light')?></label>
                <div class="label-intro">
                    <blockquote>
                        <?php esc_html_e('Import or Export the WP Speed of Light configuration through websites', 'wp-speed-of-light') ?>
                    </blockquote>
                </div>
            </div>
            <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
            if (isset($notice) && isset($class_notice)) :  ?>
                <div id="message-save-settings" class="ju-notice-success <?php echo esc_attr($class_notice) ?>" style="margin-top:20px; padding: 10px;">
                    <strong><?php
                        echo $notice //phpcs:ignore WordPress.Security.EscapeOutput -- Content already escaped in the method
                    ?></strong></div>
            <?php endif; ?>
            <div class="content">
                <div class="left">
                    <ul class="field">
                        <li class="ju-settings-option full-width">
                            <label for="cdn-active" class="speedoflight_tool ju-setting-label"
                                   alt="<?php esc_html_e('Export configration', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Export configration', 'wp-speed-of-light') ?></label>
                            <button type="button" class="ju-button orange-button waves-effect waves-light export-btn" id="export-button">
                                <span><?php esc_html_e('EXPORT', 'wp-speed-of-light'); ?></span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="right">
                    <ul class="field">
                        <li class="ju-settings-option full-width">
                            <label for="cdn-url" class="speedoflight_tool ju-setting-label"
                                   alt="<?php esc_html_e('Import configration', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Import configration', 'wp-speed-of-light') ?></label>
                            <button type="button"
                                   class="ju-button orange-button waves-effect waves-light import-btn" id="import-button">
                                <span><?php esc_html_e('IMPORT', 'wp-speed-of-light'); ?></span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="translation" class="tab-content" style="display: none;">
        <div class="content-jutranslation">
            <?php
            //phpcs:ignore WordPress.Security.EscapeOutput -- Content already escaped in the method
            echo \Joomunited\WPSOL\Jutranslation\Jutranslation::getInput();
            ?>
        </div>
    </div>
</div>


<!--Dialog-->
<!--Export config-->
<div id="wpsol_export_config_modal" class="import-export-dialog" style="display:none">
    <div class="import-export-title"><h2><?php esc_html_e('Exporting Speed Of Light configuration', 'wp-speed-of-light'); ?></h2></div>
    <div class="import-export-content">
        <span><?php esc_html_e('The system will export the configuration to a json or xml file.', 'wp-speed-of-light'); ?></span>
        <br><br>
        <span class="data-format">
            <?php esc_html_e('Data format: ', 'wp-speed-of-light'); ?>
            <b><input type="checkbox" value="json" id="ex-json" name="data-type[]" />
                <label for="ex-json"><?php esc_html_e('JSON', 'wp-speed-of-light'); ?></label></b>
            &nbsp&nbsp&nbsp&nbsp
            <b><input type="checkbox" value="xml" id="ex-xml" name="data-type[]" />
                <label for="ex-xml"><?php esc_html_e('XML', 'wp-speed-of-light'); ?></label></b>
        </span>
        <span class="import-select-error error-message"><?php esc_html_e('Please select data format!', 'wp-speed-of-light'); ?></span>
    </div>
    <div class="import-export-button">
        <button type="button" id="export-agree" class="agree ju-button orange-button waves-effect waves-light agree-export">
            <span><?php esc_html_e('Export', 'wp-speed-of-light') ?></span>
        </button>
        <button type="button" class="cancel ju-button orange-outline-button waves-effect waves-dark">
            <span><?php esc_html_e('Cancel', 'wp-speed-of-light') ?></span>
        </button>
    </div>
</div>
<!--Import config-->
<div id="wpsol_import_config_modal" class="import-export-dialog" style="display:none">
    <form method="POST" enctype="multipart/form-data">
        <?php wp_nonce_field('wpsol_import_configuration', '_wpsol_nonce'); ?>
        <input type="hidden" value="1" name="import-config"/>
        <div class="import-export-title"><h2><?php esc_html_e('Importing configuration from json file', 'wp-speed-of-light'); ?></h2></div>
        <div class="import-export-content">
            <span><?php esc_html_e('Select a Speed Of Light .json,.xml exported file and import it.', 'wp-speed-of-light'); ?></span>
            <br><br>
            <span class="data-format">
                <b><?php esc_html_e('Select your file: ', 'wp-speed-of-light'); ?></b>
                <input type="file" accept=".json,.xml"  name="import_file" id="input_import_file" required >
            </span>
        </div>
        <div class="import-export-button">
            <button type="submit" id="import-agree" class="agree ju-button orange-button waves-effect waves-light agree-import">
                <span><?php esc_html_e('Import', 'wp-speed-of-light') ?></span>
            </button>
            <button type="button" class="cancel ju-button orange-outline-button waves-effect waves-dark">
                <span><?php esc_html_e('Cancel', 'wp-speed-of-light') ?></span>
            </button>
        </div>
    </form>
</div>
