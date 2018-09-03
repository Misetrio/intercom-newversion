<?php
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Class WpsolImportExport
 */
class WpsolImportExport
{
    /**
     * Init allow extison params
     *
     * @var array
     */
    public $allowed_ext = array('json','xml');
    /**
     * WpsolMain constructor.
     */
    public function __construct()
    {
        add_action('load-wp-speed-of-light_page_wpsol_speed_optimization', array($this, 'importConfiguration'));
    }

    /**
     * Export configuration to a json file or xml file
     *
     * @static
     * @return void
     */
    public static function exportConfiguration()
    {
        check_ajax_referer('wpsolImportExportCheck', 'ajaxnonce');

        // Get configuration
        $params = array(
            'optimize' => get_option('wpsol_optimization_settings'),
            'advanced' => get_option('wpsol_advanced_settings'),
            'db_clean' => get_option('wpsol_db_clean_addon'),
            'configuration' => get_option('wpsol_configuration'),
            'cdn_intergartion' => get_option('wpsol_cdn_integration'),
            'cdn_author_max_cdn' => get_option('wpsol_addon_author_max_cdn'),
            'cdn_author_key_cdn' => get_option('wpsol_addon_author_key_cdn'),
            'cdn_author_cloudflare' => get_option('wpsol_addon_author_cloudflare'),
            'cdn_varnish' => get_option('wpsol_addon_varnish_ip'),
        );
        $result = array();
        // Creating json data
        $json = json_encode($params);

        if (is_string($json)) {
            $result['json'] = $json . '_key_' . md5('check_speedoflight_json');
        }

        // Creating object of SimpleXMLElement
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><data></data>');
        $params['xml_check'] = md5('check_speedoflight_xml');
        // function call to convert array to xml
        self::arrayToXml($params, $xml_data);
        //saving generated xml file;
        $xml = $xml_data->asXML();

        if (is_string($xml)) {
            $result['xml'] = $xml;
        }

        wp_send_json($result);
    }

    /**
     * Function defination to convert array to xml
     *
     * @param array  $data     Array to covert
     * @param object $xml_data XML simple element
     *
     * @return void
     */
    public static function arrayToXml($data, &$xml_data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'wpsolitem'.$key; //dealing with <0/>..<n/> issues
            }
            if (is_array($value)) {
                $subnode = $xml_data->addChild($key);
                self::arrayToXml($value, $subnode);
            } else {
                // Add type of value
                if (is_string($value)) {
                    $value = 'string_'.$value;
                }
                if (is_numeric($value)) {
                    $value = 'integer_'.$value;
                }
                $xml_data->addChild($key, htmlspecialchars($value));
            }
        }
    }

    /**
     * Import configuration to option
     *
     * @return void
     */
    public function importConfiguration()
    {
        if (isset($_REQUEST['import-config'])) {
            check_admin_referer('wpsol_import_configuration', '_wpsol_nonce');
            $location = $_REQUEST['_wp_http_referer'];

            // Upload file
            if (isset($_FILES)) {
                $filetype = strtolower(pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION));

                if (in_array($filetype, $this->allowed_ext)) {
                    // If it is json file or xml file, get content to update configuration
                    $fileContent = file_get_contents($_FILES['import_file']['tmp_name']);

                    if (!empty($fileContent)) {
                        if ($filetype === 'json') {
                            $hash = md5('check_speedoflight_json');
                            // Check token of speed of light configuration
                            if (strpos($fileContent, '_key_'.$hash) !== false) {
                                $fileContent = str_replace('_key_'.$hash, '', $fileContent);
                                $data = json_decode($fileContent, true);
                            } else {
                                $location .= '&import=config-incorrect';
                                wp_redirect($location);
                                exit;
                            }
                        } elseif ($filetype === 'xml') {
                            // Convert xml to string
                            $xml = simplexml_load_string($fileContent);
                            $json = json_encode($xml);
                            // return the original key
                            $json = str_replace('wpsolitem', '', $json);
                            $data = json_decode($json, true);
                            // return the original value
                            array_walk_recursive($data, array($this, 'changeTypeArray'));

                            if (isset($data['xml_check']) && ($data['xml_check'] !== md5('check_speedoflight_xml'))) {
                                $location .= '&import=config-incorrect';
                                wp_redirect($location);
                                exit;
                            }
                        }

                        //Update database
                        $check_data = true;
                        if (!isset($data['optimize']) ||
                            !isset($data['advanced']) ||
                            !isset($data['db_clean']) ||
                            !isset($data['configuration']) ||
                            !isset($data['cdn_intergartion']) ||
                            !isset($data['cdn_author_max_cdn']) ||
                            !isset($data['cdn_author_key_cdn']) ||
                            !isset($data['cdn_author_cloudflare']) ||
                            !isset($data['cdn_varnish'])
                        ) {
                            $check_data = false;
                        }

                        if ($check_data) {
                            // Update option
                            update_option('wpsol_optimization_settings', $data['optimize']);
                            update_option('wpsol_advanced_settings', $data['advanced']);
                            update_option('wpsol_db_clean_addon', $data['db_clean']);
                            update_option('wpsol_configuration', $data['configuration']);
                            update_option('wpsol_cdn_integration', $data['cdn_intergartion']);

                            update_option('wpsol_addon_author_max_cdn', $data['cdn_author_max_cdn']);
                            update_option('wpsol_addon_author_key_cdn', $data['cdn_author_key_cdn']);
                            update_option('wpsol_addon_author_cloudflare', $data['cdn_author_cloudflare']);
                            update_option('wpsol_addon_varnish_ip', $data['cdn_varnish']);
                            //return current page
                            $location .= '&import=config-imported';
                        } else {
                            $location .= '&import=import-error';
                        }
                    }
                } else {
                    //return current page
                    $location .= '&import=file-type-incorrect';
                }
            }
            // Refesh page
            if (isset($_REQUEST['_wp_http_referer'])) {
                wp_redirect($location);
                exit;
            }
        }
    }

    /**
     * Change type value in multi array
     *
     * @param string $item Value of array
     * @param string $key  Key of array
     *
     * @return void
     */
    public function changeTypeArray(&$item, $key)
    {
        if (strpos($item, 'string_') !== false) {
            $item = str_replace('string_', '', $item);
        }
        if (strpos($item, 'integer_') !== false) {
            $item = (int)str_replace('integer_', '', $item);
        }
    }
}
