<?php
add_action('admin_bar_menu', 'galoover_ae_custom_admin_bar', 100);
function galoover_ae_custom_admin_bar( $menu ) {
  $adminUrl = get_admin_url();
  $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $position = strpos($actual_link, $adminUrl);
  if($position !== 0){
    $animationUrl = $adminUrl . 'admin.php?page=animated-anything&url=' . $actual_link;
  }else{
    $animationUrl = $adminUrl . 'admin.php?page=animated-anything';
  }
  
  $menu->add_menu( array(
          'id' => 'makeAnimation',
          'title' => 'Make Animations',
          'href' => $animationUrl,
          'meta' => array( 'title' => 'Make Animations' )
  ) );
}

  function galoover_ae_append_element($template){
    $pageType = galoover_ae_get_page_type();
    switch ($pageType) {
        case 'page':
            $id = get_the_ID();
            break;
        
        case 'post':
            $id = get_the_ID();
            break;

        case 'category':
            $category = get_the_category();
            if($category && $category[0]){
              $id = $category[0]->cat_ID;
            }
            break;
        
        default:
            $id = '';
            break;
    }
    $pageTemplate = basename($template);
    $templateName = get_template();
    $animationList = galoover_ae_get_animation_list_this_page($pageTemplate, $templateName, $pageType, $id);
    if(count($animationList) != 0){
      foreach ($animationList as $key => $animation) {
          $dataEffect = galoover_ae_get_data_effect($animation->ID);
          $effectList = $dataEffect['animationList'];
          $animationList[$key]->effect = $effectList;
      }
    }
    $loadingPageSetting = galoover_ae_get_loading_page_settings($pageType, $id);
    if($loadingPageSetting){
      if($loadingPageSetting['enable_loading'] === '1'){
        $loadingPageEffects = galoover_ae_get_loading_page_effects($pageType, $id, $loadingPageSetting['custom_page']);
        $loadingTemplate = galoover_ae_return_template_loading($loadingPageSetting['loading_type']);
        $loadingFixCss = galoover_ae_return_css($loadingPageSetting['loading_type'], $loadingPageSetting['foreground_color']);
        wp_enqueue_script( 'page-lds-js', GALOOVER_AE_PLUGIN_URL.'/assets/js/page.lds.min.js', array('jquery'), '1.0', 'all' );
    
        wp_localize_script('page-lds-js', 'galoover_ae_script_vars', array(
          'animationList' => $animationList,
          'loadingTemplate' => $loadingTemplate,
          'loadingPageEffects' => $loadingPageEffects,
          'loadingFixCss' => $loadingFixCss,
          'loadingPageSetting' => $loadingPageSetting,
          'pageTemplate' => $pageTemplate,
          'templateName' => $templateName,
          'pageType' => $pageType,
          'id' => $id
          )
        );
      }else{
        wp_enqueue_script( 'page-js', GALOOVER_AE_PLUGIN_URL.'/assets/js/page.min.js', array('jquery'), '1.0', 'all' );
    
        wp_localize_script('page-js', 'galoover_ae_script_vars', array(
          'animationList' => $animationList,
          'pageTemplate' => $pageTemplate,
          'templateName' => $templateName,
          'pageType' => $pageType,
          'id' => $id
          )
        );
      }
    }
    
    return $template;
  }
add_action('template_include','galoover_ae_append_element', 1000);
 
function galoover_ae_get_page_type(){
    $isHome = is_home();
    if($isHome) return 'home';

    $isFrontPage = is_front_page();
    if($isFrontPage) return 'front';

    $isPage = is_page();
    if($isPage) return 'page';

    $isSingle = is_single();
    if($isSingle) return 'post';

    $isCategory = is_category();
    if($isCategory) return 'category';

    $isSearch = is_search();
    if($isSearch) return 'search';

    $is404 = is_404();
    if($is404) return '404';
}

function galoover_ae_get_animation_list_this_page($template, $theme, $page, $id){
    global $wpdb;
    $animationList = array();
  
    if($theme){
      $parentId = galoover_ae_get_post_id('allPage', $theme);
      $sql = "SELECT ID, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$parentId'  AND post_type = 'mya_animation' ";
      $animationAllPage = $wpdb->get_results($sql);
      $animationList = array_merge($animationList, $animationAllPage);
  
      if($template){
        $parentId = galoover_ae_get_post_id('thisTemplate', $theme, $template);
        $sql = "SELECT ID, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$parentId'  AND post_type = 'mya_animation' ";
        $animationThisTemplate = $wpdb->get_results($sql);
        $animationList = array_merge($animationList, $animationThisTemplate);
      }
      if($page){
        switch ($page) {
          case 'home':
            $parentId = galoover_ae_get_post_id('homePage', $theme);
            $sql = "SELECT ID, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$parentId' AND post_type = 'mya_animation' ";
            $animationHomePage = $wpdb->get_results($sql);
            $animationList = array_merge($animationList, $animationHomePage);
            break;
          case 'front':
            $parentId = galoover_ae_get_post_id('frontPage', $theme);
            $sql = "SELECT ID, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$parentId' AND post_type = 'mya_animation' ";
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
              $sql = "SELECT ID, post_excerpt FROM {$wpdb->prefix}posts WHERE post_parent = '$id' AND post_type = 'mya_animation' ";
              $animationThisPost = $wpdb->get_results($sql);
              $animationList = array_merge($animationList, $animationThisPost);
            }
        }
      }
    }
    foreach ($animationList as $key => $animation) {
      $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta WHERE post_id = '$animation->ID' AND meta_key LIKE '%_effect' AND meta_value <> '' ";
      $result = $wpdb->get_var($sql);
      $result = $result[0];
      if($result !== '0'){
        $resultList[] = $animationList[$key];
      }
    }
    return $resultList;
}

function galoover_ae_get_data_effect($id){
    global $wpdb;
    if($id){
        $sql = "SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = '$id' AND meta_key LIKE '%_effect' AND meta_value <> '' ";
        $result = $wpdb->get_results($sql);
        $sql = "SELECT post_parent FROM {$wpdb->prefix}posts WHERE ID = '$id' AND post_type = 'mya_animation' ";
        $parentId = $wpdb->get_row($sql);
        $sql = "SELECT post_title, post_excerpt, post_type FROM {$wpdb->prefix}posts WHERE ID = '$parentId->post_parent' ";
        $parentData = $wpdb->get_row($sql);
        
    }
    if($result){
        foreach ($result as $key => $animationField) {
        $animationField->meta_value = unserialize($animationField->meta_value);
        }
        $data['animationList'] = $result;
        return $data;
    }
}
function galoover_ae_return_template_loading($type){
  if($type){
    switch ($type) {
      case 'mya-lds-circle':
        $template = '<div class="mya-lds mya-lds-circle"></div>';
        break;
      case 'mya-lds-dual-ring':
        $template = '<div class="mya-lds mya-lds-dual-ring"></div>';
        break;
      case 'mya-lds-facebook':
        $template = '<div class="mya-lds mya-lds-facebook"><div></div><div></div><div></div></div>';
        break;
      case 'mya-lds-heart':
        $template = '<div class="mya-lds mya-lds-heart"><div></div></div>';
        break;
      case 'mya-lds-ring':
        $template = '<div class="mya-lds mya-lds-ring"><div></div><div></div><div></div><div></div></div>';
        break;
      case 'mya-lds-roller':
        $template = '<div class="mya-lds mya-lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
        break;
      case 'mya-lds-default':
        $template = '<div class="mya-lds mya-lds-default"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
        break;
      case 'mya-lds-ellipsis':
        $template = '<div class="mya-lds mya-lds-ellipsis"><div></div><div></div><div></div><div></div></div>';
        break;
      case 'mya-lds-grid':
        $template = '<div class="mya-lds mya-lds-grid"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
        break;
      case 'mya-lds-hourglass':
        $template = '<div class="mya-lds mya-lds-hourglass"></div>';
        break;
      case 'mya-lds-ripple':
        $template = '<div class="mya-lds mya-lds-ripple"><div></div><div></div></div>';
        break;
      case 'mya-lds-spinner':
        $template = '<div class="mya-lds mya-lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
        break;
    }
    return $template;
  }
}
function galoover_ae_return_css($type, $foreground_color){
  if($type){
    $foreground_color = $foreground_color ? $foreground_color : '#fff';
    switch ($type) {
      case 'mya-lds-circle':
        $fixCss = '.mya-lds-circle{background: '. $foreground_color .';}';
        break;
      case 'mya-lds-dual-ring':
        $fixCss = '.mya-lds-dual-ring:after{border-color: '. $foreground_color .' transparent '. $foreground_color .' transparent;}';
        break;
      case 'mya-lds-facebook':
        $fixCss = '.mya-lds-facebook div{background: '. $foreground_color .';}';
        break;
      case 'mya-lds-heart':
        $fixCss = '.mya-lds-heart div{background: '. $foreground_color .';}';
        break;
      case 'mya-lds-ring':
        $fixCss = '.mya-lds-ring div{border-color: '. $foreground_color .' transparent transparent transparent;}';
        break;
      case 'mya-lds-roller':
        $fixCss = '.mya-lds-roller div:after{background: '. $foreground_color .';}';
        break;
      case 'mya-lds-default':
        $fixCss = '.mya-lds-default div{background: '. $foreground_color .';}';
        break;
      case 'mya-lds-ellipsis':
        $fixCss = '.mya-lds-ellipsis div{background: '. $foreground_color .';}';
        break;
      case 'mya-lds-grid':
        $fixCss = '.mya-lds-grid div{background: '. $foreground_color .';}';
        break;
      case 'mya-lds-hourglass':
        $fixCss = '.mya-lds-hourglass:after{border-color: '. $foreground_color .' transparent '. $foreground_color .' transparent;}';
        break;
      case 'mya-lds-ripple':
        $fixCss = '.mya-lds-ripple div{border-color: '. $foreground_color .'}';
        break;
      case 'mya-lds-spinner':
        $fixCss = '.mya-lds-spinner div:after{background: '. $foreground_color .';}';
        break;
    }
    return $fixCss;
  }
}