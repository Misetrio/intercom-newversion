<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WpsolDatabaseCleanup
 */
class WpsolDatabaseCleanup
{
    /**
     * Use query to clean system
     *
     * @param string $type Type of clean
     *
     * @return string
     */
    public static function cleanSystem($type)
    {
        check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');

        self::cleanupDb($type);
        $message = 'Database cleanup successful';

        return $message;
    }

    /**
     * Exclude clean element
     *
     * @param string $type Type of database
     *
     * @return void
     */
    public static function cleanupDb($type)
    {
        global $wpdb;

        /**
         * Clean database by type
         *
         * @param string Type of database object cleaned (revisions, drafted, trash, comments, trackbacks, transient)
         */
        do_action('wpsol_clean_database', $type);

        switch ($type) {
            case 'revisions':
                $revisions = $wpdb->query(
                    $wpdb->prepare(
                        'DELETE FROM '.$wpdb->posts.' WHERE post_type = %s',
                        'revision'
                    )
                );
                break;
            case 'drafted':
                $autodraft = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->posts.' WHERE post_status = %s',
                    'auto-draft'
                ));
                break;
            case 'trash':
                $posttrash = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->posts.' WHERE post_status = %s',
                    'trash'
                ));
                break;
            case 'comments':
                $comments = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->comments.' WHERE comment_approved = %s OR comment_approved = %s',
                    'spam',
                    'trash'
                ));
                break;
            case 'trackbacks':
                $comments = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->comments.' WHERE comment_type = %s OR comment_type = %s',
                    'trackback',
                    'pingback'
                ));
                break;
            case 'transient':
                $comments = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->options.' WHERE option_name LIKE %s',
                    '%\_transient\_%'
                ));
                break;
        }

        /**
         * Action display optimize and clean duplicate table settings.
         *
         * @param string Type of database
         *
         * @internal
         */
        do_action('wpsol_addon_optimize_and_clean_duplicate_table', $type);
    }


    /**
     * Count element which to cleanup
     *
     * @param string $type Type of clean element
     *
     * @return false|integer
     */
    public function getElementToClean($type)
    {
        global $wpdb;
        $return = 0;
        switch ($type) {
            case 'revisions':
                $return = $wpdb->query($wpdb->prepare(
                    'SELECT * FROM '.$wpdb->posts.' WHERE post_type = %s',
                    'revision'
                ));
                break;
            case 'drafted':
                $return = $wpdb->query($wpdb->prepare(
                    'SELECT * FROM '.$wpdb->posts.' WHERE post_status = %s',
                    'auto-draft'
                ));
                break;
            case 'trash':
                $return = $wpdb->query($wpdb->prepare(
                    'SELECT * FROM '.$wpdb->posts.' WHERE post_status = %s',
                    'trash'
                ));
                break;
            case 'comments':
                $return = $wpdb->query($wpdb->prepare(
                    'SELECT * FROM '.$wpdb->comments.' WHERE comment_approved = %s OR comment_approved = %s',
                    'spam',
                    'trash'
                ));
                break;
            case 'trackbacks':
                $return = $wpdb->query($wpdb->prepare(
                    'SELECT * FROM '.$wpdb->comments.' WHERE comment_type = %s OR comment_type = %s',
                    'trackback',
                    'pingback'
                ));
                break;
            case 'transient':
                $return = $wpdb->query($wpdb->prepare(
                    'SELECT * FROM '.$wpdb->options.' WHERE option_name LIKE %s',
                    '%\_transient\_%'
                ));
                break;
        }
        /**
         * Filter count number of database to display
         *
         * @param integer Number return
         * @param string Type of database
         *
         * @internal
         *
         * @return string|integer
         */
        $return = apply_filters('wpsol_addon_count_number_db', $return, $type);
        return $return;
    }
}
