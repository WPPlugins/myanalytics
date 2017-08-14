<?php
/*
Plugin Name: My Analytics
Description: Affiche le tag Google Analytics ainsi que le message d'obligation légale sur les cookies.
Version: 1.0
Author: Tom Baumgarten
Author URI: http://www.tombgtn.fr/
License: GPL2
*/

function myanalytics_use_webmaster_tools() {
	return (get_option('myanalytics_setting_use_webmaster_tools')=='1');
}

function myanalytics_in_footer() {
	return !myanalytics_use_webmaster_tools();
}

function myanalytics_get_code() {
	return (get_option('myanalytics_setting_code')) ? addslashes('UA-'.get_option('myanalytics_setting_code').'-') : addslashes("UA-ZZZZZZ-") ;
}

function myanalytics_get_code_letter() {
	return (get_option('myanalytics_setting_code_letter')) ? addslashes(get_option('myanalytics_setting_code_letter')) : addslashes("1") ;
}

function myanalytics_get_message() {
	return (get_option('myanalytics_setting_message')) ? addslashes(get_option('myanalytics_setting_message')) : addslashes("Nous utilisons Google Analytics. En continuant à naviguer, vous nous autorisez à déposer un cookie à des fins de mesure d'audience.") ;
}

function myanalytics_get_message_dnt() {
	return (get_option('myanalytics_setting_message_dnt')) ? addslashes(get_option('myanalytics_setting_message_dnt')) : addslashes("Vous avez activé DoNotTrack, nous repectons votre choix.") ;
}

function myanalytics_get_message_decline() {
	return (get_option('myanalytics_setting_message_decline')) ? addslashes(get_option('myanalytics_setting_message_decline')) : addslashes("Vous vous êtes opposé au dépôt de cookies de mesures d'audience.") ;
}

function myanalytics_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die('Access denied'); }
	?>
	<div class="wrap myanalytics-admin">
		<h2><?php echo get_admin_page_title(); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields('myanalytics_settings'); ?>
			<?php do_settings_sections('myanalytics_settings'); ?>
			<?php submit_button(); ?>
		</form>
	</div><?php
}

function myanalytics_section_html() {}

function myanalytics_setting_code_html() { ?>
	UA-<input type="text" name="myanalytics_setting_code" maxlength="8" value="<?php echo (get_option('myanalytics_setting_code')) ? get_option('myanalytics_setting_code') : 'XXXXXXXX' ;?>"/>-<input type="text" class="small-text" name="myanalytics_setting_code_letter" maxlength="2" value="<?php echo (get_option('myanalytics_setting_code_letter')) ? get_option('myanalytics_setting_code_letter') : '1' ;?>"/>
<?php }

function myanalytics_setting_message_html() { ?>
	<input type="text" class="large-text" name="myanalytics_setting_message" value="<?php echo (get_option('myanalytics_setting_message')) ? get_option('myanalytics_setting_message') : "Nous utilisons Google Analytics. En continuant à naviguer, vous nous autorisez à déposer un cookie à des fins de mesure d'audience." ;?>"/>
<?php }

function myanalytics_setting_message_dnt_html() { ?>
	<input type="text" class="large-text" name="myanalytics_setting_message_dnt" value="<?php echo (get_option('myanalytics_setting_message_dnt')) ? get_option('myanalytics_setting_message_dnt') : "Vous avez activé DoNotTrack, nous repectons votre choix." ;?>"/>
<?php }

function myanalytics_setting_message_decline_html() { ?>
	<input type="text" class="large-text" name="myanalytics_setting_message_decline" value="<?php echo (get_option('myanalytics_setting_message_decline')) ? get_option('myanalytics_setting_message_decline') : "Vous vous êtes opposé au dépôt de cookies de mesures d'audience." ;?>"/>
<?php }

function myanalytics_setting_use_webmaster_tools_html() { ?>
	<input type="checkbox" name="myanalytics_setting_use_webmaster_tools" value="1" <?php echo (get_option('myanalytics_setting_use_webmaster_tools')=='1') ? 'checked' : '' ;?>/>
<?php }



function load_js() {
	wp_enqueue_script('myanalytics', plugin_dir_url(__FILE__).'myanalytics.min.js', array(), null, myanalytics_in_footer());
	wp_add_inline_script('myanalytics', 'var myanalytics_code = "'.myanalytics_get_code().myanalytics_get_code_letter().'";', 'before');
	wp_add_inline_script('myanalytics', 'var myanalytics_message = "'.myanalytics_get_message().'";', 'before');
	wp_add_inline_script('myanalytics', 'var myanalytics_message_dnt = "'.myanalytics_get_message_dnt().'";', 'before');
	wp_add_inline_script('myanalytics', 'var myanalytics_message_decline = "'.myanalytics_get_message_decline().'";', 'before');
}
add_action('wp_enqueue_scripts', 'load_js');

function myanalytics_add_menu() {
	add_options_page( 'MyAnalytics', 'MyAnalytics', 'manage_options', 'myanalytics', 'myanalytics_settings_page');
}
if ( is_admin() ) { add_action('admin_menu', 'myanalytics_add_menu'); }

function myanalytics_register_settings() {
	register_setting('myanalytics_settings', 'myanalytics_setting_code');
	register_setting('myanalytics_settings', 'myanalytics_setting_code_letter');
	register_setting('myanalytics_settings', 'myanalytics_setting_message');
	register_setting('myanalytics_settings', 'myanalytics_setting_message_dnt');
	register_setting('myanalytics_settings', 'myanalytics_setting_message_decline');
	register_setting('myanalytics_settings', 'myanalytics_setting_use_webmaster_tools');

	add_settings_section('myanalytics_section', 'Paramètres','myanalytics_section_html', 'myanalytics_settings');

	add_settings_field('myanalytics_setting_code', 'Code Analytics', 'myanalytics_setting_code_html', 'myanalytics_settings', 'myanalytics_section');
	add_settings_field('myanalytics_setting_message', 'Message', 'myanalytics_setting_message_html', 'myanalytics_settings', 'myanalytics_section');
	add_settings_field('myanalytics_setting_message_dnt', 'Message en cas de DNT', 'myanalytics_setting_message_dnt_html', 'myanalytics_settings', 'myanalytics_section');
	add_settings_field('myanalytics_setting_message_decline', 'Message de Refus', 'myanalytics_setting_message_decline_html', 'myanalytics_settings', 'myanalytics_section');
	add_settings_field('myanalytics_setting_use_webmaster_tools', 'Les Webmaster Tools sont utilisés', 'myanalytics_setting_use_webmaster_tools_html', 'myanalytics_settings', 'myanalytics_section');
}
add_action('admin_init', 'myanalytics_register_settings');

function myanalytics_link_settings( $links, $file ) {
	array_unshift( $links, '<a href="'.admin_url( 'options-general.php?page=myanalytics' ).'">'.__('Settings').'</a>');
	return $links;
}
add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), 'myanalytics_link_settings', 10, 2 );