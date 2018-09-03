<?php
if (!defined('ABSPATH')) {
    exit;
}
    $image_src = WPSOL_PLUGIN_URL . 'inc/install-wizard/content/checklist-icon.png';
?>
<form method="post">
    <div class="wizard-content-done">
        <div class="wizard-done">
            <div class="wizard-done-image">
                <img src="<?php echo esc_url($image_src); ?>"
                     srcset=""
                     class="Illustration---Done" />
            </div>
            <div class="wizard-done-container">
                <div class="title"><?php esc_html_e('Done', 'wp-speed-of-light') ?></div>
                <p>
                    <?php esc_html_e('You have now completed the plugin quick configuration', 'wp-speed-of-light') ?>
                </p>
            </div>
            <div class="wizard-done-footer configuration-footer">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_dashboard'))?>" class="button"><span>
                        <?php esc_html_e('Go to dashboard', 'wp-speed-of-light'); ?></span></a>
            </div>
        </div>
    </div>
</form>
