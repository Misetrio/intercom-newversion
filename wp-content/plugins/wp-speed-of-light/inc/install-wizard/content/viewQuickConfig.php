<?php
if (!defined('ABSPATH')) {
    exit;
}
    $wizard = new WpsolInstallWizard();
    // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
    $step  = isset($_GET['step']) ? sanitize_key($_GET['step']) : '';
    $next_link = $wizard->getNextLink($step);
?>

<form method="post" id="quick-config-form">
    <?php wp_nonce_field('wpsol-setup-wizard', 'wizard_nonce'); ?>
    <input type="hidden" name="wpsol_save_step" value="1" />
    <div class="quick-config-header">
        <div class="title"><?php esc_html_e('Quick Configuration', 'wp-speed-of-light'); ?></div>
    </div>
    <div class="quick-config-content">
        <div class="begin-container quick-config-container">
            <div class="title beginner"><?php esc_html_e('Beginner in optimization', 'wp-speed-of-light'); ?></div>
            <p>
                <?php esc_html_e('I\'m not familiar with Wordpress optimization tools. Configure automatically the plugin 
                in one click with default options that works on 100% of the websites', 'wp-speed-of-light'); ?>
            </p>
            <a href="#" onclick="document.getElementById('quick-config-form').submit()"
               class="button"><span><?php esc_html_e('OK DO IT FOR ME', 'wp-speed-of-light'); ?></span></a>
        </div>
        <div class="advanced-container quick-config-container">
            <div class="title advanced"><?php esc_html_e('Advanced in optimization', 'wp-speed-of-light'); ?></div>
            <p>
                <?php esc_html_e('I\'m familiar with Wordpress optimization tools. Show me all the main options and 
                I\'ll decide myself what i want or not or this website.', 'wp-speed-of-light'); ?>
            </p>
            <a href="<?php echo esc_url($next_link); ?>" class="button"><span><?php esc_html_e('select', 'wp-speed-of-light'); ?></span></a>
        </div>
    </div>
    <div class="quick-config-footer">
        <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_dashboard'))?>" ><span><?php esc_html_e('Never Mind, I am expert, skip it', 'wp-speed-of-light'); ?></span></a>
    </div>
</form>
