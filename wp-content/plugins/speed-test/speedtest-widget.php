<?php
/* 
Plugin Name: Speedtest
Version: 2.1
Plugin URI: http://www.dsltester.de/speedtest/
Description: Test your bandwidth speed.
Author: DSLtester.de
Author URI: http://www.dsltester.de/
*/

function widget_speedtest($args) {
  extract($args);
  
	$options = get_option("widget_speedtest");
  if (!is_array( $options ))
	{
		$options = array(
      'title' => 'Speedtest',
      'center' => '1',
      'embed' => ''
      );
  }
  
  if($options['center'] == 1)
		$style = "text-align: center;";
  
  echo $before_widget;
  	echo $before_title;
    	echo $options['title'];
  	echo $after_title;
  ?>
	<ul id="speedtest" style="<?=$style ?>">
		<li><?
			if($options['embed'] == '') { ?>
		Please configure the widget in your admin panel!
	<? } else { echo $options['embed']; } ?>
		</li>
	</ul>
	<?php
  echo $after_widget;
}

function speedtest_control(){}

function speedtest_options()
{
 $options = get_option("widget_speedtest");
  if (!is_array( $options ))
	{
		$options = array(
      'title' => 'Speedtest',
      'center' => '1',
      'embed' => ''
      );
  }      

  if ($_POST['submit'])
  {
    $options['title']			= strip_tags(stripslashes($_POST['speedtest-WidgetTitle']));
    $options['center']		= (isset($_POST['speedtest-WidgetCenter'])) ? "1" : "0";
    $options['embed']			= stripslashes($_POST['speedtest-WidgetEmbed']);

    update_option("widget_speedtest", $options);
  }
?>
<div class="wrap">
<h2>Speedtest Widget Config</h2>
<form method="post">
<script type="text/javascript" src="http://cdn.clearspring.com/launchpad/v2/standalone.js"></script>
<table>
	<tbody>
	<tr>
		<th scope="row" style="text-align:right;"><label for="speedtest-WidgetTitle">Widget Title</label></th>
		<td><input type="text" id="speedtest-WidgetTitle" name="speedtest-WidgetTitle" value="<?php echo $options['title'];?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row" style="text-align:right;"><label for="speed_image_center">Position</label></th>
		<td><input type="checkbox" <? if($options['center'] == 1) echo "checked=\"checked\""; ?>" value="1" ip="speedtest-WidgetCenter" name="speedtest-WidgetCenter"/> Center?</td>
	</tr>
	<tr>
		<th scope="row" style="text-align:right;"><label>Copy</label></th>
		<td><script type="text/javascript">$Launchpad.ShowMenu({wid: "4912d7f8c9dfab81", menuWidth: 300, menuHeight: 180,servicesInclude: ['jscode']});</script></td>
	</tr>
	<tr>
		<th scope="row" style="text-align:right;"><label for="speedtest-WidgetEmbed">Insert</label></th>
		<td><input type="text" id="speedtest-WidgetEmbed" name="speedtest-WidgetEmbed" value="<?php echo htmlspecialchars($options['embed']);?>" /></td>
	</tr>
	<tr>
		<td colspan="2"><strong>1. Click "Copy code" button<br/>2. Insert code in field "Insert"<br/>3. Save changes</strong></td>
	</tr>
	</tbody>
</table>
<p class="submit">
<input type="hidden" name="textcolor" id="textcolor" value="#<?php attribute_escape(header_textcolor()) ?>" /><input name="submit" type="submit" value="<?php _e('Save Changes'); ?>" />
</p></form>
</div>
<?
}

function speedtest_config_page()
{
	if (function_exists('add_options_page')) {
		add_options_page('Speedtest', 'Speedtest', 8, basename(__FILE__), 'speedtest_options');
	}
}

function speedtest_init()
{
	register_sidebar_widget('Speedtest', 'widget_speedtest');
	register_widget_control('Speedtest', 'speedtest_control', 400, 300 );
}
add_action('init', 'speedtest_init');
add_action('admin_menu', 'speedtest_config_page');
?>