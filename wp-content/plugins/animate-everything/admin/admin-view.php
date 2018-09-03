<?php

add_action( 'admin_menu', 'galoover_ae_admin_menu' );
function galoover_ae_admin_menu()
{
    add_menu_page(
        'Animated anything',
        'Make animation',
        'manage_options',
        'animated-anything',
        'galoover_ae_animated_admin_page',
        'dashicons-admin-links',
        6
    );
}

function galoover_ae_animated_admin_page()
{
    $urlIframe = $_GET['url'];
    
    if ( $urlIframe ) {
        $adminUrl = get_admin_url();
        $position = strpos( $urlIframe, $adminUrl );
        if ( $position === 0 ) {
            $urlIframe = get_home_url();
        }
    } else {
        $urlIframe = get_home_url();
    }
    
    $loadingPageSetting = galoover_ae_get_loading_page_settings();
    $loadingPageEffects = galoover_ae_get_loading_page_effects();
    
    if ( $loadingPageEffects['lds_effect_data'] === 'false' ) {
        $ldsEffectsName = '';
    } else {
        $ldsEffectsName = $loadingPageEffects['lds_effect_data']['name'];
    }
    
    
    if ( $loadingPageEffects['page_entrance_data'] === 'false' ) {
        $loadingPageEffectsName = '';
    } else {
        $loadingPageEffectsName = $loadingPageEffects['page_entrance_data']['name'];
    }
    
    ?>
    <div id="the-animated-view" style="position:relative;">
      <div class="wrap">
        <div class="mya-base mya-block-hover hover-top">
          <div class="border"></div>
        </div>
        <div class="mya-base mya-block-hover hover-right">
          <div class="border"></div>
        </div>
        <div class="mya-base mya-block-hover hover-bottom">
          <div class="border"></div>
        </div>
        <div class="mya-base mya-block-hover hover-left">
          <div class="border"></div>
        </div>
  
        <div id="animation_element_choosed">
          <div class="mya-base mya-block-active active-top">
            <div class="border"></div>
            <span class="dashicons dashicons-video-alt3 icon-status" id="choose-another-element" onClick="choseAnotherElement('animation')" title="choose another element"></span>
            <span class="dashicons dissable-event"></span>
          </div>
          <div class="mya-base mya-block-active active-right">
            <div class="border"></div>
          </div>
          <div class="mya-base mya-block-active active-bottom">
            <div class="border"></div>
          </div>
          <div class="mya-base mya-block-active active-left">
            <div class="border"></div>
          </div>
        </div>
  
        <div id="animation_active_list">
        </div>
  
        <div class="mya-base mya-block-hover-active active-hover-top">
          <div class="border"></div>
          <span class="icon-choose-element" id="choose-another-hover-element" onClick="choseAnotherElement('hover')"></span>
          <span class="dissable-event"></span>
        </div>
        <div class="mya-base mya-block-hover-active active-hover-right">
          <div class="border"></div>
        </div>
        <div class="mya-base mya-block-hover-active active-hover-bottom">
          <div class="border"></div>
        </div>
        <div class="mya-base mya-block-hover-active active-hover-left">
          <div class="border"></div>
        </div>
  
        <div class="mya-base mya-block-click-active active-click-top">
          <div class="border"></div>
          <span class="icon-choose-element" id="choose-another-click-element" onClick="choseAnotherElement('click')"></span>
          <span class="dissable-event"></span>
        </div>
        <div class="mya-base mya-block-click-active active-click-right">
          <div class="border"></div>
        </div>
        <div class="mya-base mya-block-click-active active-click-bottom">
          <div class="border"></div>
        </div>
        <div class="mya-base mya-block-click-active active-click-left">
          <div class="border"></div>
        </div>
  
        <div class="mya-base tool-box">
          <ul style="margin: 0;">
            <li id="go-previous-element" title="Go to previous element"></li>
            <li id="go-parent-element" title="Go to parent element"></li>
            <li id="show-all-element" title="Show all childrens element"></li>
            <li class="start-make-animation" id="add-animate" title="Make animation for this element" data-action="animation"></li>
            <!-- <li id="choose-another" title="choose another element" onClick="choseAnotherElement()"></li> -->
          </ul>
          <i class="mya-toolbox-arrow" style="visibility: visible;"></i>
        </div>
        <div class="mya-base hover-tool-box">
          <ul style="margin: 0;">
            <li id="go-previous-hover-element" title="Go to previous element"></li>
            <li id="go-parent-hover-element" title="Go to parent element"></li>
            <li id="show-all-element-hover" title="Show all childrens element"></li>
            <li class="start-make-animation" id="lock-hover-element" title="Choose this element" data-action="hover"></li>
            <!-- <li id="choose-another" title="choose another element" onClick="choseAnotherElement()"></li> -->
          </ul>
          <i class="mya-toolbox-arrow" style="visibility: visible;"></i>
        </div>
        <div class="mya-base click-tool-box">
          <ul style="margin: 0;">
            <li id="go-previous-click-element" title="Go to previous element"></li>
            <li id="go-parent-click-element" title="Go to parent element"></li>
            <li id="show-all-element-click" title="Show all childrens element"></li>
            <li class="start-make-animation" id="lock-click-element" title="Choose this element" data-action="click"></li>
            <!-- <li id="choose-another" title="choose another element" onClick="choseAnotherElement()"></li> -->
          </ul>
          <i class="mya-toolbox-arrow" style="visibility: visible;"></i>
        </div>
        <div id="upgrate-notice"></div>
        <div id="display_rules_page">
          <header>
            <div class="row">
              <div class="col-md-4"><h1 style="padding-left:20px;padding-top:4px;font-size: 23px;font-weight:400;margin: 0;line-height: 29px;">Select Your Animation Style</h1></div>
              <div class="col-md-4">
                <div id="save_success_message" class="save-message-glow">
                    <span class="dashicons dashicons-yes"></span>
                    <span class="success-message">Saved Successfully</span>
                </div>
                <div id="save_errors_message" class="save-message-glow">
                    <span class="dashicons dashicons-warning"></span>
                    <span class="error-message">Save Error</span>
                </div>
                <div id="loading" class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
              </div>
              <div class="col-md-4" id="function">
                <span class="dashicons dashicons-plus" id="add_another_animation" title="add another animation"></span>
                <span class="dashicons dashicons-menu" id="manager-button" style="margin-top:-1px"><span class="count">0</span></span>
                <div class="animations-manager">
                  <h3>Animations</h3>
                    <ul>
                    </ul> 
                </div>
                <span class="dashicons dashicons-editor-help" id="notice-button" style="margin-top:-1px" title="notices"></span>
                <div class="animations-notice">
                  <div class="wrapper">
                    <div class="content">
                      <p>***Please clear your cache after created animations***</p>
                      <p>- An element can add 3 animations:</p>
                      <p>&nbsp;&nbsp;+ Load animation will active when the website loaded.</p>
                      <p>&nbsp;&nbsp;+ Hover animation will active when mouse hover over an element( any element ).</p>
                      <p>&nbsp;&nbsp;+ Click animation will active when mouse click an element( any element ).</p>
                      <p>-> So you can add animation to sub menu, when click or hover a menu.</p>
                      <p>- <span class="dashicons dashicons-video-alt3"></span> : View this animation.</p>
                      <p>- <span class="dashicons dashicons-video-alt3" style="color: #db2c2c;"></span> : Choose another element.</p>
                      <p>- Button <span class="dashicons dashicons-welcome-view-site"></span> show all hidden element on website and button <span class="dashicons dashicons-visibility"></span> only show chilrdens element of selected element </p>
                      <p>- Or you can use your mouse to show hidden element ( click or hover), then press ( Ctrl + c ) to start to make your animation! </p>
                      <p>- When repeat value is '0', animation wil repeat forever.</p>
                      <p>- Press ( Ctrl + c ) to start to style your animation, but sometime it's not working, you can try to move your mouse out of iframe then move in back.</p>
                      <p>- If your load animation isn't active, try to set Start value to ( After all contents have been loaded )</p>
                    </div>
                  </div>
                </div>
                <span class="dashicons dashicons-admin-home" style="margin-top:-1px">
                  <a href="" target="_blank"></a>
                </span>
              </div>
            </div>
          </header>
          <form>
            <div class="row">
              <div class="form-group row col-5">
                <span class="col-sm-2 animation-status edit-animation dashicons dashicons-edit" title="Editing"></span>
                <span class="col-sm-2 animation-status add-animation dashicons dashicons-plus" title="Adding"></span>
                <label for="colFormLabelSm" class="col-sm-4 col-form-label col-form-label-sm text-align-right">Animation Name</label>
                <div class="col-sm-6 no-padding-left">
                  <input type="text" name="animation_name" class="form-control form-control-sm" id="colFormLabelSm">
                </div>
              </div>
              <div class="form-group row col-5">
                <label for="display-animation" class="col-sm-4 col-form-label col-form-label-sm text-align-right">Apply animation on</label>
                <div class="col-sm-8 no-padding-left">
                  <select id="display-animation" name="animation_page" class="custom-select custom-select-sm">
                    <option class="page-type post" value="1">This post only</option>
                    <option class="page-type page" value="2">This page only</option>
                    <option class="page-type home" value="3">Home page only</option>
                    <option class="page-type front" value="4">Front page only</option>
                    <option class="page-type category" value="6">This category only</option>
                    <option class="template" value="5">This template only</option>
                    <option value="0">All page on website</option>
                  </select>
                </div>
              </div>
            </div>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item" data-effect="load-effect">
                <a class="nav-link active" data-toggle="tab" href="#load" role="tab" aria-controls="load" title="This effect will play after the page loads">Load Animation <span class="dashicons dashicons-yes have-effect load-effect"></span></a>
              </li>
              <?php 
    ?>
            </ul>
          
            <div class="tab-content">
              <div class="tab-pane active" id="load" role="tabpanel">
                <div class="row">
                  <div class="form-group row col-4">
                    <label for="firstEffect" class="col-sm-4 col-form-label col-form-label-sm text-align-right">First Effect</label>
                    <div class="col-sm-6">
                      <select id="firstEffect" name="first-effect" class="custom-select custom-select-sm">
                        <option value="">None</option>
                        <optgroup label="Attention Seekers">
                          <?php 
    ?>
                          <option value="bounce">bounce</option>
                          <?php 
    ?>
                          <option value="flash">flash</option>
                          <option value="pulse">pulse</option>
                          <?php 
    ?>
                          <option value="rubberBand">rubberBand</option>
                          <option value="shake">shake</option>
                          <option value="swing">swing</option>
                          <option value="tada">tada</option>
                          <option value="wobble">wobble</option>
                          <option value="jello">jello</option>
                        </optgroup>

                        <optgroup label="Bouncing Entrances">
                          <option value="bounceIn">bounceIn</option>
                          <option value="bounceInDown">bounceInDown</option>
                          <option value="bounceInLeft">bounceInLeft</option>
                          <option value="bounceInRight">bounceInRight</option>
                          <option value="bounceInUp">bounceInUp</option>
                        </optgroup>
                      
                        <optgroup label="Bouncing Exits">
                          <option value="bounceOut">bounceOut</option>
                          <option value="bounceOutDown">bounceOutDown</option>
                          <option value="bounceOutLeft">bounceOutLeft</option>
                          <option value="bounceOutRight">bounceOutRight</option>
                          <option value="bounceOutUp">bounceOutUp</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Fading Entrances">
                          <option value="fadeIn">fadeIn</option>
                          <option value="fadeInDown">fadeInDown</option>
                          <option value="fadeInDownBig">fadeInDownBig</option>
                          <option value="fadeInLeft">fadeInLeft</option>
                          <option value="fadeInLeftBig">fadeInLeftBig</option>
                          <option value="fadeInRight">fadeInRight</option>
                          <option value="fadeInRightBig">fadeInRightBig</option>
                          <option value="fadeInUp">fadeInUp</option>
                          <option value="fadeInUpBig">fadeInUpBig</option>
                        </optgroup>
                      
                        <optgroup label="Fading Exits">
                          <option value="fadeOut">fadeOut</option>
                          <option value="fadeOutDown">fadeOutDown</option>
                          <option value="fadeOutDownBig">fadeOutDownBig</option>
                          <option value="fadeOutLeft">fadeOutLeft</option>
                          <option value="fadeOutLeftBig">fadeOutLeftBig</option>
                          <option value="fadeOutRight">fadeOutRight</option>
                          <option value="fadeOutRightBig">fadeOutRightBig</option>
                          <option value="fadeOutUp">fadeOutUp</option>
                          <option value="fadeOutUpBig">fadeOutUpBig</option>
                        </optgroup>
                      
                        <optgroup label="Flippers">
                          <option value="flip">flip</option>
                          <option value="flipInX">flipInX</option>
                          <option value="flipInY">flipInY</option>
                          <option value="flipOutX">flipOutX</option>
                          <option value="flipOutY">flipOutY</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Lightspeed">
                          <option value="lightSpeedIn">lightSpeedIn</option>
                          <option value="lightSpeedOut">lightSpeedOut</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Rotating Entrances">
                          <option value="rotateIn">rotateIn</option>
                          <option value="rotateInDownLeft">rotateInDownLeft</option>
                          <option value="rotateInDownRight">rotateInDownRight</option>
                          <option value="rotateInUpLeft">rotateInUpLeft</option>
                          <option value="rotateInUpRight">rotateInUpRight</option>
                        </optgroup>
                      
                        <optgroup label="Rotating Exits">
                          <option value="rotateOut">rotateOut</option>
                          <option value="rotateOutDownLeft">rotateOutDownLeft</option>
                          <option value="rotateOutDownRight">rotateOutDownRight</option>
                          <option value="rotateOutUpLeft">rotateOutUpLeft</option>
                          <option value="rotateOutUpRight">rotateOutUpRight</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Sliding Entrances">
                          <option value="slideInUp">slideInUp</option>
                          <option value="slideInDown">slideInDown</option>
                          <option value="slideInLeft">slideInLeft</option>
                          <option value="slideInRight">slideInRight</option>
                      
                        </optgroup>
                        <optgroup label="Sliding Exits">
                          <option value="slideOutUp">slideOutUp</option>
                          <option value="slideOutDown">slideOutDown</option>
                          <option value="slideOutLeft">slideOutLeft</option>
                          <option value="slideOutRight">slideOutRight</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Specials">
                          <option value="hinge">hinge</option>
                          <option value="jackInTheBox">jackInTheBox</option>
                        </optgroup>
                      </select>
                    </div>
                    <button type="button" class="play-animation first-load_effect" data-effect="first-effect" data-time="time_load" data-delay="time_load_delay" data-timing="timing_first_effect">
                        <span></span>
                      </button>
                  </div>
                  <div class="form-group row col-3">
                    <label for="time_load_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation duration of the first effect">Time</label>
                    <div class="col-sm-6">
                      <input id="time_load" type="text" data-slider-min="0.5" data-slider-max="5" data-slider-step="0.1" data-slider-value="1">
                    </div>
                    <div class="col-sm-4"><input id="time_load_value" type="text" name="time_load" class="time-value" value="1"><span>sec</span></div>
                  </div>
                  <div class="form-group row col-3">
                    <label for="time_load_delay_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation delay of the first effect">Delay</label>
                    <div class="col-sm-6">
                      <input id="time_load_delay" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="0.1" data-slider-value="0">
                    </div>
                    <div class="col-sm-4"><input id="time_load_delay_value" type="text" name="time_load_delay" class="time-value" value="0"><span>sec</span></div>
                  </div>
                  <div class="form-group row col-3">
                      <label for="timingFirstEffect" class="col-sm-4 col-form-label col-form-label-sm" title="Timing function of the first effect">Timing</label>
                      <div class="col-sm-8">
                        <select id="timingFirstEffect" name="timing_first_effect" class="custom-select custom-select-sm">
                          <option value="linear">linear</option>
                          <option value="ease">ease</option>
                          <option value="ease-in">easeIn</option>
                          <option value="ease-out">easeOut</option>
                          <option value="ease-in-out">easeInOut</option>
                          <option value="cubic-bezier(0.47,0,0.745,0.715)">easeInSine</option>
                          <option value="cubic-bezier(0.39,0.575,0.565,1)">easeOutSine</option>
                          <option value="cubic-bezier(0.445,0.05,0.55,0.95)">easeInOutSine</option>
                          <option value="cubic-bezier(0.55,0.085,0.68,0.53)">easeInQuad</option>
                          <option value="cubic-bezier(0.25,0.46,0.45,0.94)">easeOutQuad</option>
                          <option value="cubic-bezier(0.455,0.03,0.515,0.955)">easeInOutQuad</option>
                          <option value="cubic-bezier(0.55,0.055,0.675,0.19)">easeInCubic</option>
                          <option value="cubic-bezier(0.215,0.61,0.355,1)">easeOutCubic</option>
                          <option value="cubic-bezier(0.645,0.045,0.355,1)">easeInOutCubic</option>
                          <option value="cubic-bezier(0.895,0.03,0.685,0.22)">easeInQuart</option>
                          <option value="cubic-bezier(0.165,0.84,0.44,1)">easeOutQuart</option>
                          <option value="cubic-bezier(0.77,0,0.175,1)">easeInOutQuart</option>
                          <option value="cubic-bezier(0.755,0.05,0.855,0.06)">easeInQuint</option>
                          <option value="cubic-bezier(0.23,1,0.32,1)">easeOutQuint</option>
                          <option value="cubic-bezier(0.86,0,0.07,1)">easeInOutQuint</option>
                          <option value="cubic-bezier(0.95,0.05,0.795,0.035)">easeInExpo</option>
                          <option value="cubic-bezier(0.19,1,0.22,1)">easeOutExpo</option>
                          <option value="cubic-bezier(1,0,0,1)">easeInOutExpo</option>
                          <option value="cubic-bezier(0.6,0.04,0.98,0.335)">easeInCirc</option>
                          <option value="cubic-bezier(0.075,0.82,0.165,1)">easeOutCirc</option>
                          <option value="cubic-bezier(0.785,0.135,0.15,0.86)">easeInOutCirc</option>
                          <option value="cubic-bezier(0.6,-0.28,0.735,0.045)">easeInBack</option>
                          <option value="cubic-bezier(0.175,0.885,0.32,1.275)">easeOutBack</option>
                          <option value="cubic-bezier(.68, -.55, .265, 1.55)">easeInOutBack</option>
                        </select>
                      </div>
                    </div>
                </div>
                <div class="row">
                  <div class="form-group row col-4">
                    <label for="seconEffect" class="col-sm-4 col-form-label col-form-label-sm text-align-right">Secon Effect</label>
                    <div class="col-sm-6">
                      <select id="seconEffect" name="secon_effect" class="custom-select custom-select-sm">
                      <option value="">None</option>
                        <optgroup label="Attention Seekers">
                          <?php 
    ?>
                          <option value="bounce">bounce</option>
                          <?php 
    ?>
                          <option value="flash">flash</option>
                          <option value="pulse">pulse</option>
                          <?php 
    ?>
                          <option value="rubberBand">rubberBand</option>
                          <option value="shake">shake</option>
                          <option value="swing">swing</option>
                          <option value="tada">tada</option>
                          <option value="wobble">wobble</option>
                          <option value="jello">jello</option>
                        </optgroup>

                        <optgroup label="Bouncing Entrances">
                          <option value="bounceIn">bounceIn</option>
                          <option value="bounceInDown">bounceInDown</option>
                          <option value="bounceInLeft">bounceInLeft</option>
                          <option value="bounceInRight">bounceInRight</option>
                          <option value="bounceInUp">bounceInUp</option>
                        </optgroup>
                      
                        <optgroup label="Bouncing Exits">
                          <option value="bounceOut">bounceOut</option>
                          <option value="bounceOutDown">bounceOutDown</option>
                          <option value="bounceOutLeft">bounceOutLeft</option>
                          <option value="bounceOutRight">bounceOutRight</option>
                          <option value="bounceOutUp">bounceOutUp</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Fading Entrances">
                          <option value="fadeIn">fadeIn</option>
                          <option value="fadeInDown">fadeInDown</option>
                          <option value="fadeInDownBig">fadeInDownBig</option>
                          <option value="fadeInLeft">fadeInLeft</option>
                          <option value="fadeInLeftBig">fadeInLeftBig</option>
                          <option value="fadeInRight">fadeInRight</option>
                          <option value="fadeInRightBig">fadeInRightBig</option>
                          <option value="fadeInUp">fadeInUp</option>
                          <option value="fadeInUpBig">fadeInUpBig</option>
                        </optgroup>
                      
                        <optgroup label="Fading Exits">
                          <option value="fadeOut">fadeOut</option>
                          <option value="fadeOutDown">fadeOutDown</option>
                          <option value="fadeOutDownBig">fadeOutDownBig</option>
                          <option value="fadeOutLeft">fadeOutLeft</option>
                          <option value="fadeOutLeftBig">fadeOutLeftBig</option>
                          <option value="fadeOutRight">fadeOutRight</option>
                          <option value="fadeOutRightBig">fadeOutRightBig</option>
                          <option value="fadeOutUp">fadeOutUp</option>
                          <option value="fadeOutUpBig">fadeOutUpBig</option>
                        </optgroup>
                      
                        <optgroup label="Flippers">
                          <option value="flip">flip</option>
                          <option value="flipInX">flipInX</option>
                          <option value="flipInY">flipInY</option>
                          <option value="flipOutX">flipOutX</option>
                          <option value="flipOutY">flipOutY</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Lightspeed">
                          <option value="lightSpeedIn">lightSpeedIn</option>
                          <option value="lightSpeedOut">lightSpeedOut</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Rotating Entrances">
                          <option value="rotateIn">rotateIn</option>
                          <option value="rotateInDownLeft">rotateInDownLeft</option>
                          <option value="rotateInDownRight">rotateInDownRight</option>
                          <option value="rotateInUpLeft">rotateInUpLeft</option>
                          <option value="rotateInUpRight">rotateInUpRight</option>
                        </optgroup>
                      
                        <optgroup label="Rotating Exits">
                          <option value="rotateOut">rotateOut</option>
                          <option value="rotateOutDownLeft">rotateOutDownLeft</option>
                          <option value="rotateOutDownRight">rotateOutDownRight</option>
                          <option value="rotateOutUpLeft">rotateOutUpLeft</option>
                          <option value="rotateOutUpRight">rotateOutUpRight</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Sliding Entrances">
                          <option value="slideInUp">slideInUp</option>
                          <option value="slideInDown">slideInDown</option>
                          <option value="slideInLeft">slideInLeft</option>
                          <option value="slideInRight">slideInRight</option>
                      
                        </optgroup>
                        <optgroup label="Sliding Exits">
                          <option value="slideOutUp">slideOutUp</option>
                          <option value="slideOutDown">slideOutDown</option>
                          <option value="slideOutLeft">slideOutLeft</option>
                          <option value="slideOutRight">slideOutRight</option>
                        </optgroup>
                        <?php 
    ?>
                        <optgroup label="Specials">
                          <option value="hinge">hinge</option>
                          <option value="jackInTheBox">jackInTheBox</option>
                        </optgroup>
                      </select>
                    </div>
                    <button type="button" class="play-animation secon-load_effect" data-effect="secon_effect" data-time="time_secon_effect" data-delay="time_delay_secon_effect" data-timing="timing_secon_effect">
                        <span></span>
                      </button>
                  </div>
                  <div class="form-group row col-3">
                    <label for="time_secon_effect_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation duration of the secon effect">Time</label>
                    <div class="col-sm-6">
                      <input id="time_secon_effect" type="text" data-slider-min="0.5" data-slider-max="5" data-slider-step="0.1" data-slider-value="1">
                    </div>
                    <div class="col-sm-4"><input id="time_secon_effect_value" type="text" name="time_secon_effect" class="time-value" value="1"><span>sec</span></div>
                  </div>
                  <div class="form-group row col-3">
                    <label for="time_delay_secon_effect_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation delay of the secon effect">Delay</label>
                    <div class="col-sm-6">
                      <input id="time_delay_secon_effect" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="0.1" data-slider-value="0">
                    </div>
                    <div class="col-sm-4"><input id="time_delay_secon_effect_value" type="text" name="time_delay_secon_effect" class="time-value" value="0"><span>sec</span></div>
                  </div>
                  <div class="form-group row col-3">
                      <label for="timingSeconEffect" class="col-sm-4 col-form-label col-form-label-sm" title="Timing function of the secon effect">Timing</label>
                      <div class="col-sm-8">
                        <select id="timingSeconEffect" name="timing_secon_effect" class="custom-select custom-select-sm">
                          <option value="linear">linear</option>
                          <option value="ease">ease</option>
                          <option value="ease-in">easeIn</option>
                          <option value="ease-out">easeOut</option>
                          <option value="ease-in-out">easeInOut</option>
                          <option value="cubic-bezier(0.47,0,0.745,0.715)">easeInSine</option>
                          <option value="cubic-bezier(0.39,0.575,0.565,1)">easeOutSine</option>
                          <option value="cubic-bezier(0.445,0.05,0.55,0.95)">easeInOutSine</option>
                          <option value="cubic-bezier(0.55,0.085,0.68,0.53)">easeInQuad</option>
                          <option value="cubic-bezier(0.25,0.46,0.45,0.94)">easeOutQuad</option>
                          <option value="cubic-bezier(0.455,0.03,0.515,0.955)">easeInOutQuad</option>
                          <option value="cubic-bezier(0.55,0.055,0.675,0.19)">easeInCubic</option>
                          <option value="cubic-bezier(0.215,0.61,0.355,1)">easeOutCubic</option>
                          <option value="cubic-bezier(0.645,0.045,0.355,1)">easeInOutCubic</option>
                          <option value="cubic-bezier(0.895,0.03,0.685,0.22)">easeInQuart</option>
                          <option value="cubic-bezier(0.165,0.84,0.44,1)">easeOutQuart</option>
                          <option value="cubic-bezier(0.77,0,0.175,1)">easeInOutQuart</option>
                          <option value="cubic-bezier(0.755,0.05,0.855,0.06)">easeInQuint</option>
                          <option value="cubic-bezier(0.23,1,0.32,1)">easeOutQuint</option>
                          <option value="cubic-bezier(0.86,0,0.07,1)">easeInOutQuint</option>
                          <option value="cubic-bezier(0.95,0.05,0.795,0.035)">easeInExpo</option>
                          <option value="cubic-bezier(0.19,1,0.22,1)">easeOutExpo</option>
                          <option value="cubic-bezier(1,0,0,1)">easeInOutExpo</option>
                          <option value="cubic-bezier(0.6,0.04,0.98,0.335)">easeInCirc</option>
                          <option value="cubic-bezier(0.075,0.82,0.165,1)">easeOutCirc</option>
                          <option value="cubic-bezier(0.785,0.135,0.15,0.86)">easeInOutCirc</option>
                          <option value="cubic-bezier(0.6,-0.28,0.735,0.045)">easeInBack</option>
                          <option value="cubic-bezier(0.175,0.885,0.32,1.275)">easeOutBack</option>
                          <option value="cubic-bezier(.68, -.55, .265, 1.55)">easeInOutBack</option>
                        </select>
                      </div>
                    </div>
                </div>
                <div class="row">
                  <div class="form-group row col-4">
                    <label for="repeat-load_effect_value" class="col-sm-4 col-form-label col-form-label-sm text-align-right" title="Repeat times of this effect">Repeat</label>
                    <div class="col-sm-5">
                      <input id="repeat-load_effect" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="1" data-slider-value="1">
                    </div>
                    <div class="col-sm-3" style="padding-left:0;"><input id="repeat-load_effect_value" type="text" name="repeat-load_effect" class="time-value" value="1"><span>times</span></div>
                  </div>
                  <div class="form-group row col-4">
                    <label for="repeat_delay-load_effect_value" class="col-sm-4 col-form-label col-form-label-sm" title="Repeat delay time">Repeat Delay</label>
                    <div class="col-sm-5">
                      <input id="repeat_delay-load_effect" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="0.1" data-slider-value="0">
                    </div>
                    <div class="col-sm-3" style="padding-left:0;"><input id="repeat_delay-load_effect_value" type="text" name="repeat_delay-load_effect" class="time-value" value="0"><span>sec</span></div>
                  </div>
                  <div class="col-4 row no-padding">
                    <label for="start-load-efect" class="col-3 col-form-label col-form-label-sm text-align-right">Start</label>
                    <div class="col-sm-9 no-padding">
                      <select id="start-load-efect" class="custom-select custom-select-sm" name="start_load_effect">
                        <option value="2">When the html document is ready</option>
                        <option value="1">When the element become visible</option>
                        <option value="3">After all contents have been loaded</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <!-- <div class="container"> -->
                      <div class="form-group row col-4">
                        <label for="MinSS1" class="col-sm-5 col-form-label col-form-label-sm text-align-right" title="Minimum screen to play this effect">Minimum screen</label>
                        <div class="col-sm-6 no-padding">
                          <select id="MinSS1" class="custom-select custom-select-sm" name="min_screen_load_effect">
                            <option value="0" selected>No limit</option>
                            <option value="1">Smartphone (320 x 480)</option>
                            <option value="2">Tablet Portrait (768 x 1024)</option>
                            <option value="3">Tablet Landscape (1024 x 768)</option>
                            <option value="4">Laptop (1366 x 768)</option>
                            <option value="5">Desktop (1600 x 1080)</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row col-4">
                        <label for="MaxSS1" class="col-sm-5 col-form-label col-form-label-sm text-align-right" title="Maximum screen to play this effect">Maximum screen</label>
                        <div class="col-sm-6 no-padding">
                          <select id="MaxSS1" class="custom-select custom-select-sm" name="max_screen_load_effect">
                            <option value="0" selected>No limit</option>
                            <option value="1">Smartphone (320 x 480)</option>
                            <option value="2">Tablet Portrait (768 x 1024)</option>
                            <option value="3">Tablet Landscape (1024 x 768)</option>
                            <option value="4">Laptop (1366 x 768)</option>
                            <option value="5">Desktop (1600 x 1080)</option>
                          </select>
                        </div>
                      </div>
                      <div class="row col-4 button-action">
                        <button type="button" class="btn btn-primary animated-it" data-action="load_effect">Animated It!</button>
                        <button type="button" class="btn btn-primary save-this-animation" data-action="load_effect">Save</button>
                      </div>
                      
                </div>
              </div>
            <?php 
    ?>
            </div>
            </form>
        </div>
        <div id="display_loading_page_setting" style="min-height:355px">
          <header>
            <div class="row" style="margin-left: 0">
              <ul class="nav nav-tabs col-md-4 loading-page-tab" id="addLoading" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#website_lds" role="tab" title="">All pages on website </span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#this_page_lds" role="tab" title="">Only <span id="only-this-page">this page</span> </a>
                </li>
              </ul>
              <div class="col-md-4">
                <div id="success_message" class="save-message-glow">
                    <span class="dashicons dashicons-yes"></span>
                    <span class="success-message">Saved Successfully</span>
                </div>
                <div id="errors_message" class="save-message-glow">
                    <span class="dashicons dashicons-warning"></span>
                    <span class="error-message">Save Error</span>
                </div>
                <div id="loading-page-loading" class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
              </div>
            </div>
          </header>
          <div class="tab-content" style="border-left:none">
            <div class="tab-pane active" id="website_lds" role="tabpanel">
              <ul class="nav nav-tabs" id="loadingTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#loading_page_setting" role="tab" title="">Loading Page Setting </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#loading_page_effect" role="tab" title="">Page Effect </span></a>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="loading_page_setting" role="tabpanel">
                  <div class="row">
                    <div class="form-group row col-3">
                      <label for="enableLoading" class="col-sm-10 col-form-label col-form-label-sm text-align-right">Enable loading screen</label>
                      <div class="col-sm-2">
                        <input type="checkbox" id="enableLoading" name="enable_loading" <?php 
    if ( $loadingPageSetting['enable_loading'] === '1' ) {
        echo  'checked' ;
    }
    ?>>
                      </div>
                    </div>
                    <div class="form-group row col-5">
                      <label for="removeMoment" class="col-sm-7 col-form-label col-form-label-sm text-align-right">Remove the loading screen in </label>
                      <div class="col-sm-5 no-padding">
                        <select id="removeMoment" class="custom-select custom-select-sm" name="moment_remove">
                          <option value="0" <?php 
    if ( $loadingPageSetting['moment_remove'] === '0' ) {
        echo  'selected="selected"' ;
    }
    ?>>The document ready event</option>
                          <option value="1" <?php 
    if ( $loadingPageSetting['moment_remove'] === '1' ) {
        echo  'selected="selected"' ;
    }
    ?>>The window onload event</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row col-4">
                      <label for="displayTime" class="col-sm-7 col-form-label col-form-label-sm text-align-right">Display the loading screen </label>
                      <div class="col-sm-5 no-padding">
                        <select id="displayTime" class="custom-select custom-select-sm" name="show_times">
                          <option value="0" <?php 
    if ( $loadingPageSetting['show_times'] === '0' ) {
        echo  'selected="selected"' ;
    }
    ?>>Always</option>
                          <option value="1" <?php 
    if ( $loadingPageSetting['show_times'] === '1' ) {
        echo  'selected="selected"' ;
    }
    ?>>Once per session</option>
                          <option value="2" <?php 
    if ( $loadingPageSetting['show_times'] === '2' ) {
        echo  'selected="selected"' ;
    }
    ?>>Once per page</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group row col-3">
                      <label for="enableLoadingPercent" class="col-sm-10 col-form-label col-form-label-sm text-align-right">Display loading percent</label>
                      <div class="col-sm-2">
                        <input type="checkbox" id="enableLoadingPercent" name="enable_loading_percent" <?php 
    if ( $loadingPageSetting['enable_loading_percent'] === '1' ) {
        echo  'checked' ;
    }
    ?> disabled>
                      </div>
                    </div>
                    <div class="form-group row col-5">
                      <label for="loadingType" class="col-sm-7 col-form-label col-form-label-sm text-align-right">Select the loading screen </label>
                      <div class="col-sm-5 no-padding">
                        <select id="loadingType" class="custom-select custom-select-sm" name="loading_type">
                          <option value="mya-lds-default" <?php 
    if ( $loadingPageSetting['loading_type'] === 'mya-lds-default' ) {
        echo  'selected="selected"' ;
    }
    ?>>Default</option>
                          <option value="mya-lds-circle" <?php 
    if ( $loadingPageSetting['loading_type'] === 'mya-lds-circle' ) {
        echo  'selected="selected"' ;
    }
    ?>>Circle</option>
                          <option value="mya-lds-dual-ring" <?php 
    if ( $loadingPageSetting['loading_type'] === 'mya-lds-dual-ring' ) {
        echo  'selected="selected"' ;
    }
    ?>>Dual ring</option>
                          <?php 
    ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row col-4">
                      <label for="foregroundColor" class="col-sm-5 col-form-label col-form-label-sm text-align-right">Foreground color</label>
                      <div class="col-sm-7">
                        <input type="text" id="foregroundColor" name="foreground_color" value="<?php 
    echo  $loadingPageSetting['foreground_color'] ;
    ?>">
                        <div id="foregroundColorPicker"></div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group row col-3">
                      <label for="enableImageFullsize" class="col-sm-10 col-form-label col-form-label-sm text-align-right">Display image in fullscreen</label>
                      <div class="col-sm-2">
                        <input type="checkbox" id="enableImageFullsize" name="enable_fullsize" <?php 
    if ( $loadingPageSetting['enable_fullsize'] === '1' ) {
        echo  'checked' ;
    }
    ?>>
                      </div>
                    </div>
                    <div class="form-group row col-5 no-padding-right">
                      <label for="backgroundImage" class="col-sm-4 col-form-label col-form-label-sm text-align-right">Background image</label>
                      <div class="col-sm-8 upload-img no-padding">
                        <input id="backgroundImage" class="upload_image" data-show="show-background-img" type="text" size="17" name="background_image" value="<?php 
    echo  $loadingPageSetting['background_image'] ;
    ?>" />
                        <input class="button up_image" data-but="backgroundImage" type="button" value="Upload" / style="height:32px">
                        <select class="custom-select" name="background_image_repeat" style="width:80px;vertical-align: top;">
                          <option value="tile" <?php 
    if ( $loadingPageSetting['background_image_repeat'] === 'tile' ) {
        echo  'selected="selected"' ;
    }
    ?>>Tile</option>
                          <option value="center" <?php 
    if ( $loadingPageSetting['background_image_repeat'] === 'center' ) {
        echo  'selected="selected"' ;
    }
    ?>>Center</option>
                        </select>
                        <div class="show-background-img" id="backgroundImageShow">
                          <img src="<?php 
    echo  $loadingPageSetting['background_image'] ;
    ?>" alt="">
                        </div>
                      </div>
                    </div>
                    <div class="form-group row col-4">
                      <label for="backgroundColor" class="col-sm-5 col-form-label col-form-label-sm text-align-right">Background color</label>
                      <div class="col-sm-7">
                        <input type="text" id="backgroundColor" name="background_color" value="<?php 
    echo  $loadingPageSetting['background_color'] ;
    ?>">
                        <div id="backgroundColorPicker"></div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group row col-5">
                      <label for="additionalSeconds" class="col-sm-4 offset-sm-2 col-form-label col-form-label-sm" title="Repeat delay time">Additional seconds</label>
                      <div class="col-sm-4">
                        <input id="additional_second" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="0.1" data-slider-value="<?php 
    echo  $loadingPageSetting['additional_second'] ;
    ?>">
                      </div>
                      <div class="col-sm-2" style="padding-left:0;"><input id="additionalSeconds" type="text" name="additional_second" class="time-value" value="<?php 
    echo  $loadingPageSetting['additional_second'] ;
    ?>"><span>sec</span></div>
                    </div>
                    <div class="form-group row col-7">
                      <label class="col-form-label col-form-label-sm">( Show the loading screen some few seconds after loading the page. )</label>
                    </div>
                  </div>
                  <div class="row">
                        <div class="form-group row col-4">
                          <label for="MinLoading" class="col-sm-5 col-form-label col-form-label-sm text-align-right" title="">Minimum screen</label>
                          <div class="col-sm-6 no-padding">
                            <select id="MinLoading" class="custom-select custom-select-sm" name="min_loading_screen">
                              <option value="0" <?php 
    if ( $loadingPageSetting['min_loading_screen'] === '0' ) {
        echo  'selected="selected"' ;
    }
    ?>>No limit</option>
                              <option value="1" <?php 
    if ( $loadingPageSetting['min_loading_screen'] === '1' ) {
        echo  'selected="selected"' ;
    }
    ?>>Smartphone (320 x 480)</option>
                              <option value="2" <?php 
    if ( $loadingPageSetting['min_loading_screen'] === '2' ) {
        echo  'selected="selected"' ;
    }
    ?>>Tablet Portrait (768 x 1024)</option>
                              <option value="3" <?php 
    if ( $loadingPageSetting['min_loading_screen'] === '3' ) {
        echo  'selected="selected"' ;
    }
    ?>>Tablet Landscape (1024 x 768)</option>
                              <option value="4" <?php 
    if ( $loadingPageSetting['min_loading_screen'] === '4' ) {
        echo  'selected="selected"' ;
    }
    ?>>Laptop (1366 x 768)</option>
                              <option value="5" <?php 
    if ( $loadingPageSetting['min_loading_screen'] === '5' ) {
        echo  'selected="selected"' ;
    }
    ?>>Desktop (1600 x 1080)</option>
                            </select>
                          </div>
                        </div>
                        <div class="form-group row col-4">
                          <label for="MaxLoading" class="col-sm-5 col-form-label col-form-label-sm text-align-right" title="">Maximum screen</label>
                          <div class="col-sm-6 no-padding">
                            <select id="MaxLoading" class="custom-select custom-select-sm" name="max_loading_screen">
                              <option value="0" <?php 
    if ( $loadingPageSetting['max_loading_screen'] === '0' ) {
        echo  'selected="selected"' ;
    }
    ?>>No limit</option>
                              <option value="1" <?php 
    if ( $loadingPageSetting['max_loading_screen'] === '1' ) {
        echo  'selected="selected"' ;
    }
    ?>>Smartphone (320 x 480)</option>
                              <option value="2" <?php 
    if ( $loadingPageSetting['max_loading_screen'] === '2' ) {
        echo  'selected="selected"' ;
    }
    ?>>Tablet Portrait (768 x 1024)</option>
                              <option value="3" <?php 
    if ( $loadingPageSetting['max_loading_screen'] === '3' ) {
        echo  'selected="selected"' ;
    }
    ?>>Tablet Landscape (1024 x 768)</option>
                              <option value="4" <?php 
    if ( $loadingPageSetting['max_loading_screen'] === '4' ) {
        echo  'selected="selected"' ;
    }
    ?>>Laptop (1366 x 768)</option>
                              <option value="5" <?php 
    if ( $loadingPageSetting['max_loading_screen'] === '5' ) {
        echo  'selected="selected"' ;
    }
    ?>>Desktop (1600 x 1080)</option>
                            </select>
                          </div>
                        </div>
                        <div class="row col-4 button-action">
                          <button type="button" class="btn btn-primary" id="save_loading_setting">Update Settings</button>
                        </div>
                        
                  </div>
                </div>
                <div class="tab-pane" id="loading_page_effect" role="tabpanel">
                  <div class="row">
                    <div class="form-group row col-4">
                      <label for="loadingExit" class="col-sm-4 col-form-label col-form-label-sm text-align-right">Loading screen</label>
                      <div class="col-sm-6">
                        <select id="loadingExit" name="loading_exit" class="custom-select custom-select-sm">
                          <option value="">None</option>
                          
                          <optgroup label="Bouncing Exits">
                            <option <?php 
    if ( $ldsEffectsName === 'bounceOut' ) {
        echo  'selected="selected"' ;
    }
    ?> value="bounceOut">bounceOut</option>
                            <option <?php 
    if ( $ldsEffectsName === 'bounceOutDown' ) {
        echo  'selected="selected"' ;
    }
    ?> value="bounceOutDown">bounceOutDown</option>
                            <option <?php 
    if ( $ldsEffectsName === 'bounceOutLeft' ) {
        echo  'selected="selected"' ;
    }
    ?> value="bounceOutLeft">bounceOutLeft</option>
                            <option <?php 
    if ( $ldsEffectsName === 'bounceOutRight' ) {
        echo  'selected="selected"' ;
    }
    ?> value="bounceOutRight">bounceOutRight</option>
                            <option <?php 
    if ( $ldsEffectsName === 'bounceOutUp' ) {
        echo  'selected="selected"' ;
    }
    ?> value="bounceOutUp">bounceOutUp</option>
                          </optgroup>
                          <?php 
    ?>
                        </select>
                      </div>
                      <button type="button" id="get_lds_effect_data" class="play-animation" data-effect="loading_exit" data-time="time_loading_exit" data-delay="time_delay_loading_exit" data-timing="timing_loading_exit">
                          <span></span>
                        </button>
                    </div>
                    <div class="form-group row col-3">
                      <label for="time_loading_exit_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation duration of the first effect">Time</label>
                      <div class="col-sm-6">
                        <input id="time_loading_exit" type="text" data-slider-min="0.5" data-slider-max="5" data-slider-step="0.1" data-slider-value="1">
                      </div>
                      <div class="col-sm-4"><input id="time_loading_exit_value" type="text" name="time_loading_exit" class="time-value" value="1"><span>sec</span></div>
                    </div>
                    <div class="form-group row col-3">
                      <label for="time_delay_loading_exit_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation delay of the secon effect">Delay</label>
                      <div class="col-sm-6">
                        <input id="time_delay_loading_exit" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="0.1" data-slider-value="0">
                      </div>
                      <div class="col-sm-4"><input id="time_delay_loading_exit_value" type="text" name="time_delay_loading_exit" class="time-value" value="0"><span>sec</span></div>
                    </div>
                    <div class="form-group row col-3">
                      <label for="timingFirstEffect" class="col-sm-4 col-form-label col-form-label-sm" title="Timing function of the first effect">Timing</label>
                      <div class="col-sm-8">
                        <select id="timingFirstEffect" name="timing_loading_exit" class="custom-select custom-select-sm">
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'linear' ) {
        echo  'selected="selected"' ;
    }
    ?> value="linear">linear</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'ease' ) {
        echo  'selected="selected"' ;
    }
    ?> value="ease">ease</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'ease-in' ) {
        echo  'selected="selected"' ;
    }
    ?> value="ease-in">easeIn</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'ease-out' ) {
        echo  'selected="selected"' ;
    }
    ?> value="ease-out">easeOut</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'ease-in-out' ) {
        echo  'selected="selected"' ;
    }
    ?> value="ease-in-out">easeInOut</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.47,0,0.745,0.715)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.47,0,0.745,0.715)">easeInSine</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.39,0.575,0.565,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.39,0.575,0.565,1)">easeOutSine</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.445,0.05,0.55,0.95)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.445,0.05,0.55,0.95)">easeInOutSine</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.55,0.085,0.68,0.53)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.55,0.085,0.68,0.53)">easeInQuad</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.25,0.46,0.45,0.94)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.25,0.46,0.45,0.94)">easeOutQuad</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.455,0.03,0.515,0.955)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.455,0.03,0.515,0.955)">easeInOutQuad</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.55,0.055,0.675,0.19)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.55,0.055,0.675,0.19)">easeInCubic</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.215,0.61,0.355,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.215,0.61,0.355,1)">easeOutCubic</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.645,0.045,0.355,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.645,0.045,0.355,1)">easeInOutCubic</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.895,0.03,0.685,0.22)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.895,0.03,0.685,0.22)">easeInQuart</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.165,0.84,0.44,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.165,0.84,0.44,1)">easeOutQuart</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.77,0,0.175,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.77,0,0.175,1)">easeInOutQuart</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.755,0.05,0.855,0.06)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.755,0.05,0.855,0.06)">easeInQuint</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.23,1,0.32,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.23,1,0.32,1)">easeOutQuint</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.86,0,0.07,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.86,0,0.07,1)">easeInOutQuint</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.95,0.05,0.795,0.035)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.95,0.05,0.795,0.035)">easeInExpo</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.19,1,0.22,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.19,1,0.22,1)">easeOutExpo</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(1,0,0,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(1,0,0,1)">easeInOutExpo</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.6,0.04,0.98,0.335)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.6,0.04,0.98,0.335)">easeInCirc</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.075,0.82,0.165,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.075,0.82,0.165,1)">easeOutCirc</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.785,0.135,0.15,0.86)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.785,0.135,0.15,0.86)">easeInOutCirc</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.6,-0.28,0.735,0.045)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.6,-0.28,0.735,0.045)">easeInBack</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(0.175,0.885,0.32,1.275)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.175,0.885,0.32,1.275)">easeOutBack</option>
                          <option <?php 
    if ( $loadingPageEffects['page_entrance_data']['timing'] === 'cubic-bezier(.68, -.55, .265, 1.55)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(.68, -.55, .265, 1.55)">easeInOutBack</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group row col-4">
                      <label for="pageEntrance" class="col-sm-4 col-form-label col-form-label-sm text-align-right">Entrance page </label>
                      <div class="col-sm-6">
                        <select id="pageEntrance" name="page_entrance" class="custom-select custom-select-sm">
                        <option value="">None</option>
                         
                        <?php 
    ?>
                        </select>
                      </div>
                      <button id="get_page_entrance_data" type="button" class="play-animation" data-effect="page_entrance" data-time="time_page_entrance" data-delay="time_delay_page_entrance" data-timing="timing_page_enchance">
                          <span></span>
                        </button>
                    </div>
                    <div class="form-group row col-3">
                      <label for="time_page_entrance_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation duration of the secon effect">Time</label>
                      <div class="col-sm-6">
                        <input id="time_page_entrance" type="text" data-slider-min="0.5" data-slider-max="5" data-slider-step="0.1" data-slider-value="1">
                      </div>
                      <div class="col-sm-4"><input id="time_page_entrance_value" type="text" name="time_page_entrance" class="time-value" value="1"><span>sec</span></div>
                    </div>
                    <div class="form-group row col-3">
                      <label for="time_delay_page_entrance_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation delay of the secon effect">Delay</label>
                      <div class="col-sm-6">
                        <input id="time_delay_page_entrance" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="0.1" data-slider-value="0">
                      </div>
                      <div class="col-sm-4"><input id="time_delay_page_entrance_value" type="text" name="time_delay_page_entrance" class="time-value" value="0"><span>sec</span></div>
                    </div>
                    <div class="form-group row col-3">
                        <label for="timingPageEntrance" class="col-sm-4 col-form-label col-form-label-sm">Timing</label>
                        <div class="col-sm-8">
                          <select id="timingPageEntrance" name="timing_page_enchance" class="custom-select custom-select-sm">
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'linear' ) {
        echo  'selected="selected"' ;
    }
    ?> value="linear">linear</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'ease' ) {
        echo  'selected="selected"' ;
    }
    ?> value="ease">ease</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'ease-in' ) {
        echo  'selected="selected"' ;
    }
    ?> value="ease-in">easeIn</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'ease-out' ) {
        echo  'selected="selected"' ;
    }
    ?> value="ease-out">easeOut</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'ease-in-out' ) {
        echo  'selected="selected"' ;
    }
    ?> value="ease-in-out">easeInOut</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.47,0,0.745,0.715)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.47,0,0.745,0.715)">easeInSine</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.39,0.575,0.565,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.39,0.575,0.565,1)">easeOutSine</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.445,0.05,0.55,0.95)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.445,0.05,0.55,0.95)">easeInOutSine</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.55,0.085,0.68,0.53)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.55,0.085,0.68,0.53)">easeInQuad</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.25,0.46,0.45,0.94)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.25,0.46,0.45,0.94)">easeOutQuad</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.455,0.03,0.515,0.955)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.455,0.03,0.515,0.955)">easeInOutQuad</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.55,0.055,0.675,0.19)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.55,0.055,0.675,0.19)">easeInCubic</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.215,0.61,0.355,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.215,0.61,0.355,1)">easeOutCubic</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.645,0.045,0.355,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.645,0.045,0.355,1)">easeInOutCubic</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.895,0.03,0.685,0.22)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.895,0.03,0.685,0.22)">easeInQuart</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.165,0.84,0.44,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.165,0.84,0.44,1)">easeOutQuart</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.77,0,0.175,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.77,0,0.175,1)">easeInOutQuart</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.755,0.05,0.855,0.06)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.755,0.05,0.855,0.06)">easeInQuint</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.23,1,0.32,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.23,1,0.32,1)">easeOutQuint</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.86,0,0.07,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.86,0,0.07,1)">easeInOutQuint</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.95,0.05,0.795,0.035)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.95,0.05,0.795,0.035)">easeInExpo</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.19,1,0.22,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.19,1,0.22,1)">easeOutExpo</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(1,0,0,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(1,0,0,1)">easeInOutExpo</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.6,0.04,0.98,0.335)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.6,0.04,0.98,0.335)">easeInCirc</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.075,0.82,0.165,1)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.075,0.82,0.165,1)">easeOutCirc</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.785,0.135,0.15,0.86)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.785,0.135,0.15,0.86)">easeInOutCirc</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.6,-0.28,0.735,0.045)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.6,-0.28,0.735,0.045)">easeInBack</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(0.175,0.885,0.32,1.275)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(0.175,0.885,0.32,1.275)">easeOutBack</option>
                          <option <?php 
    if ( $loadingPageEffects['lds_effect_data']['timing'] === 'cubic-bezier(.68, -.55, .265, 1.55)' ) {
        echo  'selected="selected"' ;
    }
    ?> value="cubic-bezier(.68, -.55, .265, 1.55)">easeInOutBack</option>
                          </select>
                        </div>
                      </div>
                  </div>
                  <div class="row">
                    <label class="col-8 col-form-label col-form-label-sm" style="padding-left:40px">
                      - Premium plan: 50+ effects, can add Entrance page effect, more loading screen type and add loading screen to specific pages(post, page, homepage only,...).<br />
                      - All animations will active after Entrance page effect end, if Entrance page effect is 'none',
                      animations will active after Loading screen effect end.<br />
                      - For see Entrance page effect, you need hide loading screen fisrt.
                    </label>
                    <div class="col-4 button-action" style="float:right">
                      <button type="button" class="btn btn-primary display-loading-screen">Show/hide lds</button>
                      <button type="button" class="btn btn-primary" id="save-page-effect">Save</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="this_page_lds" role="tabpanel">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#this_page_setting" role="tab" title="">Loading Page Setting </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#this_page_effect" role="tab" title="">Page Effect </span></a>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="this_page_setting" role="tabpanel">
                  <div class="row">
                    <div class="form-group row col-3">
                      <label for="enableLoadingThisPage" class="col-sm-10 col-form-label col-form-label-sm text-align-right">Enable loading screen this page</label>
                      <div class="col-sm-2">
                        <input type="checkbox" id="enableLoadingThisPage" name="enable_loading_this_page">
                      </div>
                    </div>
                    <div class="form-group row col-5">
                      <label for="removeMoment" class="col-sm-7 col-form-label col-form-label-sm text-align-right">Remove the loading screen in </label>
                      <div class="col-sm-5 no-padding">
                        <select id="removeMoment" class="custom-select custom-select-sm" name="moment_remove_this_page">
                          <option value="0">The document ready event</option>
                          <option value="1">The window onload event</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row col-4">
                      <label for="displayTime" class="col-sm-7 col-form-label col-form-label-sm text-align-right">Display the loading screen </label>
                      <div class="col-sm-5 no-padding">
                        <select id="displayTime" class="custom-select custom-select-sm" name="show_times_this_page">
                          <option value="0">Always</option>
                          <option value="1">Once per session</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group row col-3">
                      <label for="enableLoadingPercent" class="col-sm-10 col-form-label col-form-label-sm text-align-right">Display loading percent</label>
                      <div class="col-sm-2">
                        <input type="checkbox" id="enableLoadingPercent" name="enable_loading_percent_this_page"  disabled>
                      </div>
                    </div>
                    <div class="form-group row col-5">
                      <label for="loadingType" class="col-sm-7 col-form-label col-form-label-sm text-align-right">Select the loading screen </label>
                      <div class="col-sm-5 no-padding">
                        <select id="loadingType" class="custom-select custom-select-sm" name="loading_type_this_page">
                          <option value="mya-lds-default">Default</option>
                          <option value="mya-lds-circle">Circle</option>
                          <option value="mya-lds-dual-ring">Dual ring</option>
                          <?php 
    ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row col-4">
                      <label for="foregroundColorThisPage" class="col-sm-5 col-form-label col-form-label-sm text-align-right">Foreground color</label>
                      <div class="col-sm-7">
                        <input type="text" id="foregroundColorThisPage" name="foreground_color_this_page" value="">
                        <div id="foregroundColorThisPagePicker"></div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group row col-3">
                      <label for="enableImageFullsizeThisPage" class="col-sm-10 col-form-label col-form-label-sm text-align-right">Display image in fullscreen</label>
                      <div class="col-sm-2">
                        <input type="checkbox" id="enableImageFullsizeThisPage" name="enable_fullsize_this_page" >
                      </div>
                    </div>
                    <div class="form-group row col-5 no-padding-right">
                      <label for="backgroundImageThisPage" class="col-sm-4 col-form-label col-form-label-sm text-align-right">Background image</label>
                      <div class="col-sm-8 upload-img no-padding">
                        <input id="backgroundImageThisPage" class="upload_image" data-show="show-background-img-this-page" type="text" size="17" name="background_image_this_page" value="" />
                        <input class="button up_image" data-but="backgroundImageThisPage" type="button" value="Upload" / style="height:32px">
                        <select class="custom-select" name="background_image_repeat_this_page" style="width:80px;vertical-align: top;">
                          <option value="tile">Tile</option>
                          <option value="center">Center</option>
                        </select>
                        <div class="show-background-img-this-page" id="backgroundImageShowThisPage">
                          <img src="" alt="">
                        </div>
                      </div>
                    </div>
                    <div class="form-group row col-4">
                      <label for="backgroundColorThisPage" class="col-sm-5 col-form-label col-form-label-sm text-align-right">Background color</label>
                      <div class="col-sm-7">
                        <input type="text" id="backgroundColorThisPage" name="background_color_this_page" value="">
                        <div id="backgroundColorThisPagePicker"></div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group row col-5">
                      <label for="additionalSecondsThisPage" class="col-sm-4 offset-sm-2 col-form-label col-form-label-sm">Additional seconds</label>
                      <div class="col-sm-4">
                        <input id="additional_second_this_page" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="0.1" data-slider-value="">
                      </div>
                      <div class="col-sm-2" style="padding-left:0;"><input id="additionalSecondsThisPage" type="text" name="additional_second_this_page" class="time-value" value=""><span>sec</span></div>
                    </div>
                    <div class="form-group row col-7">
                      <label class="col-form-label col-form-label-sm">( Show the loading screen some few seconds after loading the page. )</label>
                    </div>
                  </div>
                  <div class="row">
                        <div class="form-group row col-4">
                          <label for="MinLoadingThisPage" class="col-sm-5 col-form-label col-form-label-sm text-align-right" title="">Minimum screen</label>
                          <div class="col-sm-6 no-padding">
                            <select id="MinLoadingThisPage" class="custom-select custom-select-sm" name="min_loading_screen_this_page">
                              <option value="0">No limit</option>
                              <option value="1">Smartphone (320 x 480)</option>
                              <option value="2">Tablet Portrait (768 x 1024)</option>
                              <option value="3">Tablet Landscape (1024 x 768)</option>
                              <option value="4">Laptop (1366 x 768)</option>
                              <option value="5">Desktop (1600 x 1080)</option>
                            </select>
                          </div>
                        </div>
                        <div class="form-group row col-4">
                          <label for="MaxLoadingThisPage" class="col-sm-5 col-form-label col-form-label-sm text-align-right" title="">Maximum screen</label>
                          <div class="col-sm-6 no-padding">
                            <select id="MaxLoadingThisPage" class="custom-select custom-select-sm" name="max_loading_screen_this_page">
                              <option value="0">No limit</option>
                              <option value="1">Smartphone (320 x 480)</option>
                              <option value="2">Tablet Portrait (768 x 1024)</option>
                              <option value="3">Tablet Landscape (1024 x 768)</option>
                              <option value="4">Laptop (1366 x 768)</option>
                              <option value="5">Desktop (1600 x 1080)</option>
                            </select>
                          </div>
                        </div>
                        <div class="row col-4 button-action">
                          <?php 
    ?>
                        </div>
                        
                  </div>
                </div>
                <div class="tab-pane" id="this_page_effect" role="tabpanel">
                  <div class="row">
                    <div class="form-group row col-4">
                      <label for="loadingExitThisPage" class="col-sm-4 col-form-label col-form-label-sm text-align-right">Loading screen</label>
                      <div class="col-sm-6">
                        <select id="loadingExitThisPage" name="loading_exit_this_page" class="custom-select custom-select-sm">
                          <option value="">None</option>
                          
                          <optgroup label="Bouncing Exits">
                            <option value="bounceOut">bounceOut</option>
                            <option value="bounceOutDown">bounceOutDown</option>
                            <option value="bounceOutLeft">bounceOutLeft</option>
                            <option value="bounceOutRight">bounceOutRight</option>
                            <option value="bounceOutUp">bounceOutUp</option>
                          </optgroup>
                          <?php 
    ?>
                        </select>
                      </div>
                      <button type="button" id="get_lds_effect_data_this_page" class="play-animation" data-effect="loading_exit_this_page" data-time="time_loading_exit_this_page" data-delay="time_delay_loading_exit_this_page" data-timing="timing_loading_exit_this_page">
                          <span></span>
                        </button>
                    </div>
                    <div class="form-group row col-3">
                      <label for="time_loading_exit_this_page_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation duration of the first effect">Time</label>
                      <div class="col-sm-6">
                        <input id="time_loading_exit_this_page" type="text" data-slider-min="0.5" data-slider-max="5" data-slider-step="0.1" data-slider-value="1">
                      </div>
                      <div class="col-sm-4"><input id="time_loading_exit_this_page_value" type="text" name="time_loading_exit_this_page" class="time-value" value="1"><span>sec</span></div>
                    </div>
                    <div class="form-group row col-3">
                      <label for="time_delay_loading_exit_this_page_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation delay of the secon effect">Delay</label>
                      <div class="col-sm-6">
                        <input id="time_delay_loading_exit_this_page" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="0.1" data-slider-value="0">
                      </div>
                      <div class="col-sm-4"><input id="time_delay_loading_exit_this_page_value" type="text" name="time_delay_loading_exit_this_page" class="time-value" value="0"><span>sec</span></div>
                    </div>
                    <div class="form-group row col-3">
                      <label for="timingFirstEffect" class="col-sm-4 col-form-label col-form-label-sm" title="Timing function of the first effect">Timing</label>
                      <div class="col-sm-8">
                        <select id="timingFirstEffect" name="timing_loading_exit_this_page" class="custom-select custom-select-sm">
                          <option value="linear">linear</option>
                          <option value="ease">ease</option>
                          <option value="ease-in">easeIn</option>
                          <option value="ease-out">easeOut</option>
                          <option value="ease-in-out">easeInOut</option>
                          <option value="cubic-bezier(0.47,0,0.745,0.715)">easeInSine</option>
                          <option value="cubic-bezier(0.39,0.575,0.565,1)">easeOutSine</option>
                          <option value="cubic-bezier(0.445,0.05,0.55,0.95)">easeInOutSine</option>
                          <option value="cubic-bezier(0.55,0.085,0.68,0.53)">easeInQuad</option>
                          <option value="cubic-bezier(0.25,0.46,0.45,0.94)">easeOutQuad</option>
                          <option value="cubic-bezier(0.455,0.03,0.515,0.955)">easeInOutQuad</option>
                          <option value="cubic-bezier(0.55,0.055,0.675,0.19)">easeInCubic</option>
                          <option value="cubic-bezier(0.215,0.61,0.355,1)">easeOutCubic</option>
                          <option value="cubic-bezier(0.645,0.045,0.355,1)">easeInOutCubic</option>
                          <option value="cubic-bezier(0.895,0.03,0.685,0.22)">easeInQuart</option>
                          <option value="cubic-bezier(0.165,0.84,0.44,1)">easeOutQuart</option>
                          <option value="cubic-bezier(0.77,0,0.175,1)">easeInOutQuart</option>
                          <option value="cubic-bezier(0.755,0.05,0.855,0.06)">easeInQuint</option>
                          <option value="cubic-bezier(0.23,1,0.32,1)">easeOutQuint</option>
                          <option value="cubic-bezier(0.86,0,0.07,1)">easeInOutQuint</option>
                          <option value="cubic-bezier(0.95,0.05,0.795,0.035)">easeInExpo</option>
                          <option value="cubic-bezier(0.19,1,0.22,1)">easeOutExpo</option>
                          <option value="cubic-bezier(1,0,0,1)">easeInOutExpo</option>
                          <option value="cubic-bezier(0.6,0.04,0.98,0.335)">easeInCirc</option>
                          <option value="cubic-bezier(0.075,0.82,0.165,1)">easeOutCirc</option>
                          <option value="cubic-bezier(0.785,0.135,0.15,0.86)">easeInOutCirc</option>
                          <option value="cubic-bezier(0.6,-0.28,0.735,0.045)">easeInBack</option>
                          <option value="cubic-bezier(0.175,0.885,0.32,1.275)">easeOutBack</option>
                          <option value="cubic-bezier(.68, -.55, .265, 1.55)">easeInOutBack</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group row col-4">
                      <label for="pageEntranceThisPage" class="col-sm-4 col-form-label col-form-label-sm text-align-right">Entrance page </label>
                      <div class="col-sm-6">
                        <select id="pageEntranceThisPage" name="page_entrance_this_page" class="custom-select custom-select-sm">
                        <option value="">None</option>
                        <?php 
    ?>
                        </select>
                      </div>
                      <button id="get_page_entrance_data_this_page" type="button" class="play-animation" data-effect="page_entrance_this_page" data-time="time_page_entrance_this_page" data-delay="time_delay_page_entrance_this_page" data-timing="timing_page_enchance_this_page">
                          <span></span>
                        </button>
                    </div>
                    <div class="form-group row col-3">
                      <label for="time_page_entrance_this_page_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation duration of the secon effect">Time</label>
                      <div class="col-sm-6">
                        <input id="time_page_entrance_this_page" type="text" data-slider-min="0.5" data-slider-max="5" data-slider-step="0.1" data-slider-value="1">
                      </div>
                      <div class="col-sm-4"><input id="time_page_entrance_this_page_value" type="text" name="time_page_entrance_this_page" class="time-value" value="1"><span>sec</span></div>
                    </div>
                    <div class="form-group row col-3">
                      <label for="time_delay_page_entrance_this_page_value" class="col-sm-2 col-form-label col-form-label-sm" title="Animation delay of the secon effect">Delay</label>
                      <div class="col-sm-6">
                        <input id="time_delay_page_entrance_this_page" type="text" data-slider-min="0" data-slider-max="5" data-slider-step="0.1" data-slider-value="0">
                      </div>
                      <div class="col-sm-4"><input id="time_delay_page_entrance_this_page_value" type="text" name="time_delay_page_entrance_this_page" class="time-value" value="0"><span>sec</span></div>
                    </div>
                    <div class="form-group row col-3">
                        <label for="timingPageEntrance" class="col-sm-4 col-form-label col-form-label-sm">Timing</label>
                        <div class="col-sm-8">
                          <select id="timingPageEntrance" name="timing_page_enchance_this_page" class="custom-select custom-select-sm">
                            <option value="linear">linear</option>
                            <option value="ease">ease</option>
                            <option value="ease-in">easeIn</option>
                            <option value="ease-out">easeOut</option>
                            <option value="ease-in-out">easeInOut</option>
                            <option value="cubic-bezier(0.47,0,0.745,0.715)">easeInSine</option>
                            <option value="cubic-bezier(0.39,0.575,0.565,1)">easeOutSine</option>
                            <option value="cubic-bezier(0.445,0.05,0.55,0.95)">easeInOutSine</option>
                            <option value="cubic-bezier(0.55,0.085,0.68,0.53)">easeInQuad</option>
                            <option value="cubic-bezier(0.25,0.46,0.45,0.94)">easeOutQuad</option>
                            <option value="cubic-bezier(0.455,0.03,0.515,0.955)">easeInOutQuad</option>
                            <option value="cubic-bezier(0.55,0.055,0.675,0.19)">easeInCubic</option>
                            <option value="cubic-bezier(0.215,0.61,0.355,1)">easeOutCubic</option>
                            <option value="cubic-bezier(0.645,0.045,0.355,1)">easeInOutCubic</option>
                            <option value="cubic-bezier(0.895,0.03,0.685,0.22)">easeInQuart</option>
                            <option value="cubic-bezier(0.165,0.84,0.44,1)">easeOutQuart</option>
                            <option value="cubic-bezier(0.77,0,0.175,1)">easeInOutQuart</option>
                            <option value="cubic-bezier(0.755,0.05,0.855,0.06)">easeInQuint</option>
                            <option value="cubic-bezier(0.23,1,0.32,1)">easeOutQuint</option>
                            <option value="cubic-bezier(0.86,0,0.07,1)">easeInOutQuint</option>
                            <option value="cubic-bezier(0.95,0.05,0.795,0.035)">easeInExpo</option>
                            <option value="cubic-bezier(0.19,1,0.22,1)">easeOutExpo</option>
                            <option value="cubic-bezier(1,0,0,1)">easeInOutExpo</option>
                            <option value="cubic-bezier(0.6,0.04,0.98,0.335)">easeInCirc</option>
                            <option value="cubic-bezier(0.075,0.82,0.165,1)">easeOutCirc</option>
                            <option value="cubic-bezier(0.785,0.135,0.15,0.86)">easeInOutCirc</option>
                            <option value="cubic-bezier(0.6,-0.28,0.735,0.045)">easeInBack</option>
                            <option value="cubic-bezier(0.175,0.885,0.32,1.275)">easeOutBack</option>
                            <option value="cubic-bezier(.68, -.55, .265, 1.55)">easeInOutBack</option>
                          </select>
                        </div>
                      </div>
                  </div>
                  <div class="row">
                    <label class="col-8 col-form-label col-form-label-sm" style="padding-left:40px">
                    - Premium plan: 50+ effects, can add Entrance page effect, more loading screen type and add loading screen to specific pages(post, page, homepage only,...).<br />
                      - All animations will active after Entrance page effect end, if Entrance page effect is 'none',
                      animations will active after Loading screen effect end.<br />
                      - For see Entrance page effect, you need hide loading screen fisrt.
                    </label>
                    <div class="col-4 button-action" style="float:right">
                      <button type="button" class="btn btn-primary display-loading-screen">Show/hide lds</button>
                      <?php 
    ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <div class="wp-full-overlay expanded preview-desktop" style="z-index:5;">
          <div id="customize-preview" class="wp-full-overlay-main iframe-ready">
            <iframe sandbox="allow-same-origin allow-scripts allow-forms" id="idIframe" onload="iframeLoaded()" src="<?php 
    echo  $urlIframe ;
    ?>" frameborder="0" style="width: 100%;height: 100%;position: absolute;z-index:-1;" scrolling="no"></iframe>
            <div id="layout-iframe-1"></div>
            <div id="layout-iframe"></div>
            <div id="customize-footer-actions" class="wp-full-overlay-footer custom" style="border: 1px solid #ddd;">
              <button type="button" class="show-all-hide" title="Show all hidden element on website">
                <span class="dashicons dashicons-welcome-view-site"></span>
              </button>
              <button type="button" id="start-button" class="collapse-sidebar button" aria-expanded="true" aria-label="Start" style="position: static;padding:9px 11px;float: right;" title="Click here or ( ctrl + c ) to start make your animations">
                <span class="dashicons dashicons-controls-play"></span>
              </button>
              <div class="loading-page-setting" style="float: right;">
                <button type="button" id="hide-loading-setting" class="collapse-sidebar button" style="position: static;padding:9px 11px;float: right;" title="hide/show setting box">
                  <span class="dashicons"></span>
                </button>
                <button type="button" id="loading-page" class="collapse-sidebar button" style="position: static;padding:9px 11px;float: right;" title="loading page setting">
                <div class="loader" style="width: 20px;height: 20px;"></div>
                </button>
              </div>
              
              <div class="devices">
                <button type="button" class="preview-desktop active" aria-pressed="true" data-device="preview-desktop" title="Enter desktop mode">
                </button>
                <button type="button" class="preview-tablet" aria-pressed="false" data-device="preview-tablet" title="Enter tablet mode">
                </button>
                <button type="button" class="preview-mobile" aria-pressed="false" data-device="preview-mobile" title="Enter mobile mode">
                </button>
              </div>
              <button type="button" id="collapse-sidebar-button" class="collapse-sidebar button" aria-expanded="true" aria-label="Start" style="position: static;padding:9px;" title="Full screen">
                <span class="collapse-sidebar-arrow"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php 
}
