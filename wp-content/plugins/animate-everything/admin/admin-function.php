<?php
add_action( 'admin_enqueue_scripts', 'galoover_ae_make_animation' );

function galoover_ae_make_animation() {
  $uri = $_SERVER[REQUEST_URI];
  $search = strpos($uri, 'admin.php?page=animated-anything' );
  if($search !== false ){
    wp_enqueue_script('makeAnimation', GALOOVER_AE_PLUGIN_URL.'/assets/js/js.min.js', array('jquery'));
    wp_localize_script( 'makeAnimation', 'makeAnimationajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    
    wp_enqueue_script('lds', GALOOVER_AE_PLUGIN_URL.'/assets/js/lds.min.js', array('jquery'));
    wp_localize_script( 'lds', 'ldsajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );   
  }   
}

function galoover_ae_create_mya_post($type, $postType, $value = ''){
  switch ($type) {
    case 'allPage':
      $my_post = array(
        'post_title'    => 'mya_all_page',
        'post_status'   => 'private',
        'post_name'   => 'mya_all_page',
        'post_parent'   => 0,
        'post_type'   => $postType
      );
      break;
    case 'thisTemplate':
      $my_post = array(
        'post_title'    => 'mya_this_template',
        'post_status'   => 'private',
        'post_name'   => 'mya_this_template',
        'post_parent'   => 0,
        'post_type'   => $postType,
        'post_excerpt'   => $value
      );
      break;
    case 'thisCategory':
      $my_post = array(
        'post_title'    => 'mya_this_category',
        'post_status'   => 'private',
        'post_name'   => 'mya_this_category',
        'post_parent'   => 0,
        'post_type'   => $postType,
        'post_excerpt'   => $value
      );
      break;
    case 'homePage':
      $my_post = array(
        'post_title'    => 'mya_home_page',
        'post_status'   => 'private',
        'post_name'   => 'mya_home_page',
        'post_parent'   => 0,
        'post_type'   => $postType
      );
      break;
    case 'frontPage':
      $my_post = array(
        'post_title'    => 'mya_front_page',
        'post_status'   => 'private',
        'post_name'   => 'mya_front_page',
        'post_parent'   => 0,
        'post_type'   => $postType
      );
      break;
  }
  $id = wp_insert_post( $my_post );
  return $id;
}

function galoover_ae_get_post_id($type, $theme, $value = ''){
  global $wpdb;
  $postType = 'mya_' . $theme;
    switch ($type) {
      case 'allPage':
        $sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = 'mya_all_page' AND post_status = 'private' AND post_parent = '0' AND post_type = '$postType'";
        break;
      case 'thisTemplate':
        $sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = 'mya_this_template' AND post_status = 'private' AND post_parent = '0' AND post_type = '$postType' AND post_excerpt = '$value'";
        break;
      case 'thisCategory':
        $sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = 'mya_this_category' AND post_status = 'private' AND post_parent = '0' AND post_type = '$postType' AND post_excerpt = '$value'";
        break;
      case 'homePage':
        $sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = 'mya_home_page' AND post_parent = '0' AND post_type = '$postType' ";
        break;
      case 'frontPage':
        $sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = 'mya_front_page' AND post_status = 'private' AND post_parent = '0' AND post_type = '$postType'";
        break;
    }
  $result = $wpdb->get_row($sql);
  if($result){
    $id = $result->ID;
  }else{
    $id = galoover_ae_create_mya_post($type, $postType, $value);
  }
  return $id;
}

function galoover_ae_create_animation($animationName, $eleXPath, $parentId){
  $my_post = array(
    'post_title'    => $animationName,
    'post_excerpt'    => $eleXPath,
    'post_status'   => 'private',
    'post_parent'   => $parentId,
    'post_type'   => 'mya_animation'
  );
  $id = wp_insert_post( $my_post );
  return $id;
}

function galoover_ae_update_animation($animationId, $animationName, $eleXPath, $parentId){
  $my_post = array(
    'ID' => $animationId,
    'post_title'    => $animationName,
    'post_excerpt'    => $eleXPath,
    'post_parent'   => $parentId
  );
  $id = wp_update_post( $my_post );
  return $id;
}

function galoover_ae_create_post_meta_animation($animationId, $animationType, $animationData){
  global $wpdb;
  $sql = "SELECT meta_id FROM {$wpdb->prefix}postmeta WHERE post_id = '$animationId' AND meta_key = '$animationType'";
  $result = $wpdb->get_row($sql);
  if($result){
    update_post_meta( $animationId, $animationType, $animationData );
  }else{
    add_post_meta( $animationId, $animationType, $animationData, true );
  }
}

function galoover_ae_save_animation(){
  if ( ! is_admin() ) { return 0; }
  $animationPageValue = isset( $_POST['animationPageValue'] ) ? sanitize_text_field($_POST['animationPageValue']) : '';
 
  $animationPageData = isset( $_POST['animationPageData'] ) ? (array) $_POST['animationPageData'] : array();
  
  $theme = isset( $animationPageData['theme'] ) ? sanitize_text_field($animationPageData['theme']) : '';
  if($animationPageValue == '1' || $animationPageValue == '2'){
    $parentId = $animationPageData['id'];
  }else{
    switch ($animationPageValue) {
      case '3':
        $type = 'homePage';
        break;
      case '4':
        $type = 'frontPage';
        break;
      case '5':
        $type = 'thisTemplate';
        $value = isset( $animationPageData['template'] ) ? sanitize_text_field($animationPageData['template']) : '';
        break;
      case '6':
        $type = 'thisCategory';
        $value = $animationPageData['id'];
        break;
      case '0':
        $type = 'allPage';
        break;
    }
    $parentId = galoover_ae_get_post_id($type, $theme, $value);
  }
  
  $animationName = isset( $_POST['animationName'] ) ? sanitize_text_field($_POST['animationName']) : '';
  $eleXPath = isset( $_POST['eleXPath'] ) ? sanitize_text_field($_POST['eleXPath']) : '';
  $animationId = isset( $_POST['animationId'] ) ? sanitize_text_field($_POST['animationId']) : '';
  if($animationId){
    galoover_ae_update_animation($animationId, $animationName, $eleXPath, $parentId);
  }else{
    $animationId = galoover_ae_create_animation($animationName, $eleXPath, $parentId);
  }

  $animationData = isset( $_POST['animationData'] ) ? (array) $_POST['animationData'] : array();
  
  $animationType = isset( $_POST['animationType'] ) ? sanitize_text_field($_POST['animationType']) : '';

  galoover_ae_create_post_meta_animation($animationId, $animationType, $animationData);
  
  wp_send_json_success($animationId);die;
}
add_action( 'wp_ajax_galoover_ae_save_animation', 'galoover_ae_save_animation' );

function galoover_ae_remove_animation(){
  if ( ! is_admin() ) { return 0; }
  $postId = isset( $_POST['id'] ) ? sanitize_text_field($_POST['id']) : '';
  if($postId){
    $status = wp_delete_post($postId);
    delete_post_meta( $postId, 'load_effect' );
    delete_post_meta( $postId, 'hover_effect' );
    delete_post_meta( $postId, 'click_effect' );
  }
  if($status != false){
    wp_send_json_success('Animation deleted successfully.');die;
  }
}
add_action( 'wp_ajax_galoover_ae_remove_animation', 'galoover_ae_remove_animation' );

function galoover_ae_get_animation_list(){
  global $wpdb;
  $template = isset( $_POST['template'] ) ? sanitize_text_field($_POST['template']) : '';
  $page = isset( $_POST['page'] ) ? sanitize_text_field($_POST['page']) : '';
  $theme = isset( $_POST['theme'] ) ? sanitize_text_field($_POST['theme']) : '';
  $id = isset( $_POST['id'] ) ? sanitize_text_field($_POST['id']) : '';
  $animationList = array();

  if($theme){
    $parentId = galoover_ae_get_post_id('allPage', $theme);
    $sql = "SELECT ID, post_title, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$parentId'  AND post_type = 'mya_animation' ";
    $animationAllPage = $wpdb->get_results($sql);
    $animationList = array_merge($animationList, $animationAllPage);

    if($template){
      $parentId = galoover_ae_get_post_id('thisTemplate', $theme, $template);
      $sql = "SELECT ID, post_title, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$parentId'  AND post_type = 'mya_animation' ";
      $animationThisTemplate = $wpdb->get_results($sql);
      $animationList = array_merge($animationList, $animationThisTemplate);
    }
    if($page){
      switch ($page) {
        case 'home':
          $parentId = galoover_ae_get_post_id('homePage', $theme);
          $sql = "SELECT ID, post_title, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$parentId' AND post_type = 'mya_animation' ";
          $animationHomePage = $wpdb->get_results($sql);
          $animationList = array_merge($animationList, $animationHomePage);
          break;
        case 'front':
          $parentId = galoover_ae_get_post_id('frontPage', $theme);
          $sql = "SELECT ID, post_title, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$parentId' AND post_type = 'mya_animation' ";
          $animationFrontPage = $wpdb->get_results($sql);
          $animationList = array_merge($animationList, $animationFrontPage);
          break;
        case 'category':
          $parentId = galoover_ae_get_post_id('thisCategory', $theme, $id);
          $sql = "SELECT ID, post_title, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$parentId' AND post_type = 'mya_animation' ";
          $animationThisCategory = $wpdb->get_results($sql);
          $animationList = array_merge($animationList, $animationThisCategory);
          break;
        case 'page':
        case 'post':
          if($id){
            $sql = "SELECT ID, post_title, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$id' AND post_type = 'mya_animation' ";
            $animationThisPost = $wpdb->get_results($sql);
            $animationList = array_merge($animationList, $animationThisPost);
          }
      }
    }
    foreach ($animationList as $key => $animation) {
      $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE post_id = '$animation->ID' AND meta_key LIKE '%_effect' AND meta_value <> '' ";
      $result = $wpdb->get_var($sql);
      $result = $result[0];
      if($result !== '0'){
        $resultList[] = $animationList[$key];
      }else{
        wp_delete_post($animation->ID);
      }
    }
  }
  wp_send_json_success($resultList);die;
}
add_action( 'wp_ajax_galoover_ae_get_animation_list', 'galoover_ae_get_animation_list' );

function galoover_ae_get_animation_data(){
  global $wpdb;
  $postId = isset( $_POST['id'] ) ? sanitize_text_field($_POST['id']) : '';
  if($postId){
    $sql = "SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = '$postId' AND meta_key LIKE '%_effect' AND meta_value <> '' ";
    $result = $wpdb->get_results($sql);
    $sql = "SELECT post_parent FROM {$wpdb->prefix}posts WHERE ID = '$postId' AND post_type = 'mya_animation' ";
    $parentId = $wpdb->get_row($sql);
    $sql = "SELECT post_title, post_excerpt, post_type FROM {$wpdb->prefix}posts WHERE ID = '$parentId->post_parent' ";
    $parentData = $wpdb->get_row($sql);
    switch ($parentData->post_type) {
      case 'post':
        $pageValue = 1;
        break;
      case 'page':
        $pageValue = 2;
        break;
      default:
        if($parentData->post_title === 'mya_home_page'){
          $pageValue = 3;
        }else{
          if($parentData->post_title === 'mya_all_page'){
            $pageValue = 0;
          }else{
            if($parentData->post_title === 'mya_front_page'){
              $pageValue = 4;
            }else{
              if($parentData->post_title === 'mya_this_template'){
                $pageValue = 5;
              }
            }
          }
        }
        break;
    }
  }
  if($result){
    foreach ($result as $key => $animationField) {
      $animationField->meta_value = unserialize($animationField->meta_value);
    }
    $data['pageValue'] = $pageValue;
    $data['animationList'] = $result;
    wp_send_json_success($data);die;
  }
}
add_action( 'wp_ajax_galoover_ae_get_animation_data', 'galoover_ae_get_animation_data' );

galoover_ae_fs()->add_action('after_uninstall', 'galoover_ae_fs_uninstall_cleanup');
function galoover_ae_fs_uninstall_cleanup(){
  global $wpdb;

	$sql = "SELECT ID, post_parent FROM {$wpdb->prefix}posts WHERE post_status = 'private'  AND post_type LIKE 'mya_%' ";
	$posts = $wpdb->get_results($sql);
	
	foreach ( $posts as $post ) {
		if($post->post_parent !== 0){
			delete_post_meta( $post->ID, 'load_effect' );
			delete_post_meta( $post->ID, 'hover_effect' );
			delete_post_meta( $post->ID, 'click_effect' );
		}
		wp_delete_post( $post->ID, true );
	}
}

function galoover_ae_get_loading_page_settings($pageType = '' , $id = '')
{
  if($pageType){
    $optionName = galoover_ae_return_loading_option_name($pageType, $id);
  }else{
    $optionName = 'galoover_ae_loading_page_options';
  }
  $loading_page_options = get_option($optionName, array());
  if( !empty($loading_page_options) && $loading_page_options['enable_loading'] === '1' ){
    $loading_page_options['custom_page'] = true;
    return $loading_page_options;
  }else{
    $loading_page_options = get_option('galoover_ae_loading_page_options', array());
  }
  if( !empty($loading_page_options) ) {
    $loading_page_options['custom_page'] = false;
    return $loading_page_options;
  }
  // Set the default options here
  $loading_page_options = array(
      'enable_loading'    => '1',
      'moment_remove'    		=> '1',
      'show_times'  		=> '0',
      'enable_loading_percent' 	=> '0',
      'loading_type' 	=> 'mya-lds-default',
      'foreground_color'           => '#FFFFFF',
      'background_color'           => '#000000',
      'background_image'           => '',
      'background_image_repeat'   => 'center',
      'enable_fullsize'       => '0',
      'additional_second'        => '0',
      'min_loading_screen'        => '0',
      'max_loading_screen'        => '0',
  );
  update_option('galoover_ae_loading_page_options', $loading_page_options);
  $loading_page_options['custom_page'] = false;
  return $loading_page_options;
}

function galoover_ae_update_loading_page_settings(){
  if ( ! is_admin() ) { return 0; }
  $data = $_POST['data'];
  $enable_loading = isset( $data['enable_loading'] ) ? sanitize_text_field($data['enable_loading']) : '1';
  $moment_remove = isset( $data['moment_remove'] ) ? sanitize_text_field($data['moment_remove']) : '1';
  $show_times = isset( $data['show_times'] ) ? sanitize_text_field($data['show_times']) : '0';
  $enable_loading_percent = isset( $data['enable_loading_percent'] ) ? sanitize_text_field($data['enable_loading_percent']) : '0';
  $loading_type = isset( $data['loading_type'] ) ? sanitize_text_field($data['loading_type']) : 'mya-lds-default';
  $foreground_color = isset( $data['foreground_color'] ) ? sanitize_text_field($data['foreground_color']) : '#FFFFFF';
  $background_color = isset( $data['background_color'] ) ? sanitize_text_field($data['background_color']) : '#000000';
  $background_image = isset( $data['background_image'] ) ? sanitize_text_field($data['background_image']) : '';
  $background_image_repeat = isset( $data['background_image_repeat'] ) ? sanitize_text_field($data['background_image_repeat']) : 'center';
  $enable_fullsize = isset( $data['enable_fullsize'] ) ? sanitize_text_field($data['enable_fullsize']) : '0';
  $additional_second = isset( $data['additional_second'] ) ? sanitize_text_field($data['additional_second']) : '0';
  $min_loading_screen = isset( $data['min_loading_screen'] ) ? sanitize_text_field($data['min_loading_screen']) : '0';
  $max_loading_screen = isset( $data['max_loading_screen'] ) ? sanitize_text_field($data['max_loading_screen']) : '0';
  $loading_page_options = array(
    'enable_loading'    => $enable_loading,
    'moment_remove'    		=> $moment_remove,
    'show_times'  		=> $show_times,
    'enable_loading_percent' 	=> $enable_loading_percent,
    'loading_type' 	=> $loading_type,
    'foreground_color'           => $foreground_color,
    'background_color'           => $background_color,
    'background_image'           => $background_image,
    'background_image_repeat'   => $background_image_repeat,
    'enable_fullsize'       => $enable_fullsize,
    'additional_second'        => $additional_second,
    'min_loading_screen'        => $min_loading_screen,
    'max_loading_screen'        => $max_loading_screen,
);
$status = update_option('galoover_ae_loading_page_options', $loading_page_options);
if($status)
  wp_send_json_success();die;
}
add_action( 'wp_ajax_galoover_ae_update_loading_page_settings', 'galoover_ae_update_loading_page_settings' );

function galoover_ae_get_loading_page_effects($pageType = '', $id = '', $customPage = '')
{
  if($customPage && $pageType){
    $optionEffect = galoover_ae_return_loading_effects($pageType, $id);
  }else{
    $optionEffect = 'galoover_ae_loading_page_effects';
  }
  $loading_page_effects = get_option($optionEffect, array());
  if( !empty($loading_page_effects) ) return $loading_page_effects;
  return;
}

function galoover_ae_update_loading_page_effects(){
  if ( ! is_admin() ) { return 0; }
  $data = $_POST['data'];
  $pageEntranceData = isset( $data['pageEntranceData'] ) ? $data['pageEntranceData'] : '';
  $ldsEffectData = isset( $data['ldsEffectData'] ) ? $data['ldsEffectData'] : '';
  
  $loading_page_effects = array(
    'page_entrance_data'    => $pageEntranceData,
    'lds_effect_data'    		=> $ldsEffectData,
);
$status = update_option('galoover_ae_loading_page_effects', $loading_page_effects);
if($status)
  wp_send_json_success();die;
}
add_action( 'wp_ajax_galoover_ae_update_loading_page_effects', 'galoover_ae_update_loading_page_effects' );

function galoover_ae_update_loading_page_settings_this_page(){
  if ( ! is_admin() ) { return 0; }
  $pageData = $_POST['pageData'];
  $data = $_POST['data'];
  $enable_loading = isset( $data['enable_loading'] ) ? sanitize_text_field($data['enable_loading']) : '1';
  $moment_remove = isset( $data['moment_remove'] ) ? sanitize_text_field($data['moment_remove']) : '1';
  $show_times = isset( $data['show_times'] ) ? sanitize_text_field($data['show_times']) : '0';
  $enable_loading_percent = isset( $data['enable_loading_percent'] ) ? sanitize_text_field($data['enable_loading_percent']) : '0';
  $loading_type = isset( $data['loading_type'] ) ? sanitize_text_field($data['loading_type']) : 'mya-lds-default';
  $foreground_color = isset( $data['foreground_color'] ) ? sanitize_text_field($data['foreground_color']) : '#FFFFFF';
  $background_color = isset( $data['background_color'] ) ? sanitize_text_field($data['background_color']) : '#000000';
  $background_image = isset( $data['background_image'] ) ? sanitize_text_field($data['background_image']) : '';
  $background_image_repeat = isset( $data['background_image_repeat'] ) ? sanitize_text_field($data['background_image_repeat']) : 'center';
  $enable_fullsize = isset( $data['enable_fullsize'] ) ? sanitize_text_field($data['enable_fullsize']) : '0';
  $additional_second = isset( $data['additional_second'] ) ? sanitize_text_field($data['additional_second']) : '0';
  $min_loading_screen = isset( $data['min_loading_screen'] ) ? sanitize_text_field($data['min_loading_screen']) : '0';
  $max_loading_screen = isset( $data['max_loading_screen'] ) ? sanitize_text_field($data['max_loading_screen']) : '0';
  $loading_page_options = array(
    'enable_loading'    => $enable_loading,
    'moment_remove'    		=> $moment_remove,
    'show_times'  		=> $show_times,
    'enable_loading_percent' 	=> $enable_loading_percent,
    'loading_type' 	=> $loading_type,
    'foreground_color'           => $foreground_color,
    'background_color'           => $background_color,
    'background_image'           => $background_image,
    'background_image_repeat'   => $background_image_repeat,
    'enable_fullsize'       => $enable_fullsize,
    'additional_second'        => $additional_second,
    'min_loading_screen'        => $min_loading_screen,
    'max_loading_screen'        => $max_loading_screen,
);
if($pageData['page']){
  $optionName = galoover_ae_return_loading_option_name($pageData['page'], $pageData['id']);
}
$status = update_option($optionName, $loading_page_options);
if($status)
  wp_send_json_success();die;
}
add_action( 'wp_ajax_galoover_ae_update_loading_page_settings_this_page', 'galoover_ae_update_loading_page_settings_this_page' );

function galoover_ae_return_loading_option_name($pageType, $id = ''){
  if($pageType){
    switch ($pageType) {
      case 'home':
        $optionName = 'home_page_loading_page_options';
        break;
      case 'front':
        $optionName = 'front_page_loading_page_options';
        break;
      case 'page':
        $optionName = $id . '_page_loading_page_options';
        break;
      case 'post':
        $optionName = $id . '_post_loading_page_options';
        break;
      case 'category':
        $optionName = $id . '_category_loading_page_options';
        break;
      case 'search':
        $optionName = 'search_page_loading_page_options';
        break;
      case '404':
        $optionName = '404_category_loading_page_options';
        break;
    }
    return $optionName;
  }
}

function galoover_ae_return_loading_effects($pageType, $id = ''){
  if($pageType){
    switch ($pageType) {
      case 'home':
        $optionEffect = 'home_page_loading_page_effect';
        break;
      case 'front':
        $optionEffect = 'front_page_loading_page_effect';
        break;
      case 'page':
        $optionEffect = $id . '_page_loading_page_effect';
        break;
      case 'post':
        $optionEffect = $id . '_post_loading_page_effect';
        break;
      case 'category':
        $optionEffect = $id . '_category_loading_page_effect';
        break;
      case 'search':
        $optionEffect = 'search_page_loading_page_effect';
        break;
      case '404':
        $optionEffect = '404_category_loading_page_effect';
        break;
    }
    return $optionEffect;
  }
}

function galoover_ae_update_loading_page_effects_this_page(){
  if ( ! is_admin() ) { return 0; }
  $data = $_POST['data'];
  $pageData = $_POST['pageData'];
  $pageEntranceData = isset( $data['pageEntranceData'] ) ? $data['pageEntranceData'] : '';
  $ldsEffectData = isset( $data['ldsEffectData'] ) ? $data['ldsEffectData'] : '';
  
  $loading_page_effects = array(
    'page_entrance_data'    => $pageEntranceData,
    'lds_effect_data'    		=> $ldsEffectData,
  );
  if($pageData['page']){
    $optionEffect = galoover_ae_return_loading_effects($pageData['page'], $pageData['id']);
  }

$status = update_option($optionEffect, $loading_page_effects);
if($status)
  wp_send_json_success();die;
}
add_action( 'wp_ajax_galoover_ae_update_loading_page_effects_this_page', 'galoover_ae_update_loading_page_effects_this_page' );

function galoover_ae_get_loading_page_data_this_page()
{
  $id = isset( $_POST['id'] ) ? $_POST['id'] : '';
  $page = isset( $_POST['page'] ) ? $_POST['page'] : '';
  if($page){
    $optionEffect = galoover_ae_return_loading_effects($page, $id);
    $optionName = galoover_ae_return_loading_option_name($page, $id);
  }
  $loading_page_effects = get_option($optionEffect, array());
  $loading_page_options = get_option($optionName, array());
  $data = [];
  $data['loading_page_options'] = $loading_page_options;
  $data['loading_page_effects'] = $loading_page_effects;
  if( !empty($data) ) wp_send_json_success($data);die;
  return;
}
add_action( 'wp_ajax_galoover_ae_get_loading_page_data_this_page', 'galoover_ae_get_loading_page_data_this_page' );

function galoover_ae_get_loading_page_data_active()
{
  $id = isset( $_POST['id'] ) ? $_POST['id'] : '';
  $page = isset( $_POST['page'] ) ? $_POST['page'] : '';
  if($page){
    $optionName = galoover_ae_return_loading_option_name($page, $id);
  }
  $loading_page_options = get_option($optionName, array());
  if( !empty($loading_page_options) && $loading_page_options['enable_loading'] === '1' ){
    $custom_page = true;
  }else{
    $loading_page_options = get_option('galoover_ae_loading_page_options', array());
  }
  if( !empty($loading_page_options) ) {
    $custom_page = false;
  }

  $loadingTemplate = galoover_ae_return_template_loading($loading_page_options['loading_type']);
  $loadingFixCss = galoover_ae_return_css($loading_page_options['loading_type'], $loading_page_options['foreground_color']);

  $data = [];
  $data['loadingPageSetting'] = $loading_page_options;
  $data['loadingTemplate'] = $loadingTemplate;
  $data['loadingFixCss'] = $loadingFixCss;
  if( !empty($data) ) wp_send_json_success($data);die;
  return;
}
add_action( 'wp_ajax_galoover_ae_get_loading_page_data_active', 'galoover_ae_get_loading_page_data_active' );