<?php
/*
	Plugin Name: LiveSupporti
	Plugin URI: https://livesupporti.com
	Description: A plugin that allows to add <strong>live support chat</strong> on a WordPress website. To get started just click <strong>Activate</strong>.
	Version: 1.0.6
	Author: LiveSupporti
	Author URI: https://livesupporti.com
	License: GPL2
  
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//add_action('init', 'do_output_buffer');

add_action('wp_footer', 'livesupporti_init');

add_action('admin_menu', 'getLiveSupportiAdminMenu');

register_activation_hook( __FILE__, 'livesupporti_activate_plugin' );

add_action('admin_init', 'redirectToLiveSupportiAdminPage');

//function do_output_buffer() {
//        ob_start();
//}

function livesupporti_init() {
	if (isset($_POST["action"]) && $_POST["action"] == "changeAccount")
	{
		update_option("liveSupportiLicense", "");
	}

	$license = get_option('liveSupportiLicense');
	$skin = get_option('liveSupportiSkin');
	addLiveSupportiScript($license, $skin);
}

function addLiveSupportiScript($license, $skin) {
	if ($license != '' && $skin != '')
	{
		echo '
				<!-- Live chat by LiveSupporti - https://livesupporti.com -->
				<script type="text/javascript">
				  (function() {
					var s=document.createElement("script");s.type="text/javascript";s.async=true;s.id="lsInitScript";
					s.src = "https://livesupporti.com/Scripts/clientAsync.js?acc='.$license.'&skin='.$skin.'";
					var scr=document.getElementsByTagName("script")[0];scr.parentNode.appendChild(s, scr);
				  })();
				</script>
		';
	}	
}

function getLiveSupportiAdminMenu() {
	$icon = "https://livesupporti.com/Images/favicon.png";
	add_menu_page('LiveSupporti', 'LiveSupporti', 10, dirname( __FILE__ ) . '/livesupporti.php', '', $icon);
	add_submenu_page(dirname( __FILE__ ) . '/livesupporti.php', 'Settings', 'Settings', 'manage_options', dirname( __FILE__ ) . '/livesupporti.php', 'livesupporti_settings');
}

function livesupporti_settings() {
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	
	if (isset($_POST["action"]) && $_POST["action"] == "signupin")
	{
		if ($_POST["txtEmail"] != "" && $_POST["txtPassword"] != "")
		{
			$liveSupportiData   = array(
			  'email'    => $_POST["txtEmail"],
			  'password' => $_POST["txtPassword"]
			);
			$lsResponse = json_decode(postRequest("https://livesupporti.com/wordpress/signupsignin", $liveSupportiData));
			if (!isset($lsResponse->errorEmail) && !isset($lsResponse->errorPassword))
			{
				update_option('liveSupportiEmail', $_POST["txtEmail"]);
				update_option('liveSupportiSkin', $_POST['selectLiveSupportiSkin']);
				
				if (!isset($lsResponse->error) && isset($lsResponse->code) && $lsResponse->code != "")
				{
					update_option("liveSupportiLicense", $lsResponse->code);
				}
			}
		}
		else
		{
			// Show error.
			if ($_POST["txtEmail"] == "")
			{
				$lsResponse->errorEmail = "Invalid email";
			}
			else if ($_POST["txtPassword"] == "")
			{
				$lsResponse->errorPassword = "Please enter your password";
			}
		}
    }
	
	if (isset($_POST["action"]) && $_POST["action"] == "changeAccount")
	{
		update_option("liveSupportiLicense", "");
	}
?>
<script>
	
</script>
	<div style='padding:15px 15px 15px 0px;'>
		<div style='padding:15px;  height: 630px; background-image:url("<?php echo plugin_dir_url( __FILE__ ).'wordpress-bg.png'; ?>"); box-shadow:0px 2px 10px #878787;'>
			<form id="form1" name="form1" method="post" action="admin.php?page=livesupporti/livesupporti.php" style="width:100%;margin:0px auto;display:<?php if (get_option('liveSupportiLicense') == "") { ?> block;<?php } else { ?>none;<?php } ?>">
				<input type="hidden" name="<?php echo $hidLiveSupporti; ?>" value="IsPostBack">
				<input type="hidden" name="action" value="signupin">
				<div style="text-align:center;">
					<img src="<?php echo plugin_dir_url( __FILE__ ).'logo.png'; ?>" style="width:120px;"/>
					<h1 style="color:#2d3f50">Getting Started with LiveSupporti</h1>
					<p style="margin:0px 0px 22px 0px;font-size:14px;">Please enter your LiveSupporti details. Don't have an account? Don't worry we will create one for you.</p>
				</div>
				<div style="margin-top:20px;">
					<table style="position:relative; border-collapse:collapse;border-spacing:0; width:100%;">
						<tr>
							<td style="margin:0px;padding:0px;width:50%;" align="right"></td>
							<td style="margin:0px;padding:0px;width:50%;" align="left">
								<span id="spanError" style="display:block;height:18px;margin:4px 0px 10px -160px;color:#DC4B45;font-weight:600;visibility:<?php if (isset($lsResponse->error)){?>visible;<?php } else { ?>hidden;<?php }?>"><?php echo($lsResponse->error);?></span>
							</td>
						</tr>
						<tr>
							<td style="margin:0px;padding:0px;width:50%;" align="right">
								<span style="display:inline-block;width:70px;font-size:14px;font-weight:600;margin-right:180px;">Email</span>
							</td>
							<td style="margin:0px;padding:0px;width:50%;" align="left">
								<input id="txtEmail" name="txtEmail" type="text" style="width:320px;height:40px;margin-left:-160px;border-radius:2px" value="<?php get_option('liveSupportiEmail') ?>"/>
							</td>
						</tr>
						<tr>
							<td style="margin:0px;padding:0px;width:50%;" align="right">
							</td>
							<td style="margin:0px;padding:0px;width:50%;" align="left">
								<span id="spanEmailError" style="display:block;height:18px;margin:4px 0px 20px -160px;color:#DC4B45;font-weight:600;visibility:<?php if (isset($lsResponse->errorEmail)){?>visible;<?php } else { ?>hidden;<?php }?>"><?php echo($lsResponse->errorEmail);?></span>
							</td>
						</tr>
						<tr>
							<td style="margin:0px;padding:0px;width:50%" align="right">
								<span style="display:inline-block;width:70px;font-size:14px;font-weight:600;margin-right:180px;">Password</span>
							</td>
							<td style="margin:0px;padding:0px;width:50%" align="left">
								<input id="txtPassword" name="txtPassword" type="password" style="width:320px;height:40px;margin-left:-160px;border-radius:2px"/>
								
							</td>
						
						</tr>
						<tr>
							<td style="margin:0px;padding:0px;width:50%" align="right">
							</td>
							<td style="margin:0px;padding:0px;width:50%" align="left">
								<span id="spanPasswordError" style="display:block;height:18px;margin:4px 0px 20px -160px;color:#DC4B45;font-weight:600;visibility:<?php if (isset($lsResponse->errorPassword)){?>visible;<?php } else { ?>hidden;<?php }?>"><?php echo($lsResponse->errorPassword);?></span>
							</td>
						</tr>
						<tr>
							<td style="margin:0px;padding:0px;width:50%" align="right">
								<span style="display:inline-block;width:70px;font-size:14px;font-weight:600;margin-right:180px;">Skin:</span>
							</td>
							<td style="margin:0px;padding:0px;width:50%;" alight="left">
								<select name="selectLiveSupportiSkin" style="height:40px;margin-left:-160px;border-radius:2px;vertical-align:baseline">
									<?php
										$skin = get_option('liveSupportiSkin');
										$skins = array('Classic'=>'Classic', 'Modern'=>'Modern');
										foreach ($skins as $val => $label)
										{
											$selected = ($skin == $val) ? "selected='selected'":'';
											echo "<option value='$val' $selected>$label</option>\n";
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<div style="margin-bottom:44px">
								</div>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Done') ?>" style="width:320px;height:40px;font-size:14px;margin-left:-160px;color:#ffffff;background-color:#009fe8;border-color:#007ab2;"/>
							</td>
						</tr>
					</table>
				</div>
			</form>
			<form id="form2" name="form2" method="post" action="admin.php?page=livesupporti/livesupporti.php" style="width:640px;margin:0px auto;display:<?php if (get_option('liveSupportiLicense') == "") { ?> none;<?php } else { ?>block;<?php } ?>">
				<input type="hidden" name="action" value="changeAccount">
				<div style="text-align:center;">
					<img src="<?php echo plugin_dir_url( __FILE__ ).'logo.png'; ?>" style="width:120px;"/>
					<h1 style="color:#2d3f50">LiveSupporti is on your website.</h1>
					<div id="divStartChatting" style="margin:30px 0px;">
						<a href="https://livesupporti.com/live" class="button-primary" target="_blank" style="width:140px;height:40px;vertical-align: middle;text-align: center;padding-top: 6px;font-size:14px;color:#ffffff;background-color:#009fe8;border-color:#007ab2;">START CHATTING</a><span style="margin:0px 0px 0px 5px"> or </span><input type="submit" value="Change account" style="background: none;border: none;color:#0073aa;text-decoration: underline;cursor: pointer;"/>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php
}
?>
<?php
function livesupporti_activate_plugin() {
    add_option('redirectToLiveSupportiAdminPage', true);
}

function redirectToLiveSupportiAdminPage() {
    if (get_option('redirectToLiveSupportiAdminPage', false)) {
        delete_option('redirectToLiveSupportiAdminPage');
    	wp_redirect(admin_url('admin.php?page=livesupporti/livesupporti.php'));
    }
}

function postRequest($url, $_data )
  {
    $args = array(
      "body" => $_data,
      "user-agent" => "LiveSupporti WordPress",
    );
    $response = wp_remote_post($url, $args);
    if (is_wp_error($response)) {
      $error = array("wp_error" => $response->get_error_message() );

      return json_encode($error);
    }

    return $response["body"];
  }
?>