<?php
if (!defined('ABSPATH')) {
    exit;
}
$image_src = WPSOL_PLUGIN_URL . 'inc/install-wizard/content/welcome-illustration.png';
?>
<form method="post">
    <div class="start-wizard">
        <div class="start-wizard-image">
            <img src="<?php echo esc_url($image_src); ?>"
                 srcset=""
                 class="Illustration---Done" />
        </div>
        <div class="start-wizard-container">
            <div class="title">
                <?php esc_html_e('Welcome to WP Speed of Light first configuration wizard!', 'wp-speed-of-light') ?>
            </div>
            <p>
                <?php esc_html_e('This wizard will help you with some server compatibility check and with plugin main 
                configuration. Follow some simple steps and speedup your website in no time', 'wp-speed-of-light') ?>
            </p>
        </div>
        <div class="start-wizard-footer configuration-footer">
            <a href="<?php echo esc_url(add_query_arg('step', 'environment', remove_query_arg('activate_error')))?>" class="next-button">
                <span><?php esc_html_e('Continue to environment check', 'wp-speed-of-light'); ?></span>
            </a>

            <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_dashboard'))?>" class="backup-button"><span>
                    <?php esc_html_e('I know what I\'m doing, skip wizard', 'wp-speed-of-light'); ?></span></a>
        </div>
    </div>
</form>
