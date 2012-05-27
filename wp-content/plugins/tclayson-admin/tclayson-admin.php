<?php
/*
	Plugin Name: tclayson Admin
	Plugin URI: http://tclayson.com
	Description: Plugin to brand the back end and modify some elements.
	Version: 1
	Author: Thomas Clayson
	Author URI: http://tclayson.com
*/

/*  
	Copyright 2011-2012 Thomas Clayson (http://tclayson.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//
// This function removes the top-level menu items from the admin section.
// I use this to stop people from being able to access areas of the site which
// they shouldn't be able to access.
//
// e.g. Once I've designed a website then there is no reason for users to be
// able to get to the "Appearance" menu, so I hide it from them.
// 
// If your user id is 1 (i.e. you are the first registered user) or your user
// name is "admin" (change as necessary) then the menu is not restricted.
//
// To add/remove menu items from this function then change the $restricted
// array.
//
function tc_remove_menus(){
	global $menu;
	global $current_user;
	get_currentuserinfo();
	
	global $submenu;
	
	if ($current_user->ID != 1 && $current_user->user_login != "admin") {
		// Removes 'Updates submenu'.
		unset($submenu['index.php'][10]);
		
		// Array of menu items to hide
		// Options: Dashboard, Post, Pages, Appearance, Tools, Users, Settings, Comments, Plugins
		// You can see all options by doing:
		//     var_dump($menu);
		$restricted = array(__('Appearance'), __('Tools'), __('Users'), __('Settings'), __('Comments'), __('Plugins'));
		
		// Remove each menu item
		end($menu);
		while (prev($menu)) {
			$value = explode(' ', $menu[key($menu)][0]);
			if (in_array($value[0] != null ? $value[0] : "", $restricted)) {
				unset($menu[key($menu)]);
			}
		}
	}
}
add_action('admin_menu', 'tc_remove_menus');

//
// These functions add a dashboard widget to the front of the admin section.
//
// Function: tc_instructions_content()
// This is an example of what you can do with a dashboard widget. I like to have
// an instructions panel with some key instructions for my clients, for instance
// how to add a post, or how to change the content of a page.
//
// Function tc_add_dashboard_widgets()
// This is where we add the dashboard widget using a Wordpress function. You can 
// copy and replicate the line starting wp_add_dashboard_widget(...) and replicate
// for any other dashboard widgets you wish to make.
//
function tc_instructions_content(){
	// Echo/print whatever it is you want to show in your dashboard widget
	echo "Hi there,<br />Nice to see you. To get started add a new post. To do this click on \"Posts\" on the left hand side. From here you want to click \"Add New\" next to the title. Here you can create a title, add some content and choose the attributes. Once you are finished you can click \"Publish\" and the post will be visible on your website.<br /><br />If you need any more help <a href=\"mailto:support@yourwebsite.com\">send me an email.</a>";
}

function tc_add_dashboard_widgets(){
	wp_add_dashboard_widget('instructions_dashboard_widget', __('Instructions'), 'tc_instructions_content');
}
add_action('wp_dashboard_setup', 'tc_add_dashboard_widgets');

//
// This function just removes all the other random dashboard boxes from the admin
// section. As above with the menus if you are the first user and your username
// is "admin" then you get to see them still. Obviously to hide any you don't wish
// to see, just take the remove_meta_box(...) bit out of the if(...) statement.
//
function tc_remove_dashboard_widgets(){
	global $current_user;
	get_currentuserinfo();
	
	if ($current_user->ID != 1 && $current_user->user_login != "admin") {
		remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
		remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
		
		remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
		remove_meta_box('dashboard_primary', 'dashboard', 'side');
		remove_meta_box('dashboard_secondary', 'dashboard', 'side');
	}
}
add_action('wp_dashboard_setup', 'tc_remove_dashboard_widgets');

//
// This function adds custom CSS to the admin section. Here I have used it to
// change the logo in the top left to that of my own website, rather than Wordpress.
//
// I have also included CSS to stop the update-nag from displaying. Again, I have
// included some clauses to see if the logged in user is the admin (i.e. you) so
// that you are aware of any potential updates. However your clients won't be prompted
// to "update" and potentially create compatibility issues.
//
// Now an if you want to be neat about it you could always put this style into an
// external CSS file (or two: one for all admin panels, and one for when you
// specifically aren't logged in) and change the code below for a <link ... /> tag.
// Personally I'm not too bothered. I know where my CSS is for the admin section.
//
function tc_admin_css(){
	$display = ($current_user->ID == 1 || $current_user->user_login == "admin");
	?>	
		<style type="text/css">
			/* stops update bar displaying */
			<? if(!$display): ?>
			#update-nag, .update-nag {
				display: none;
			}
			
			/* same for the footer */
			#footer-upgrade {
			display: none;
			}
			
			/* stops favourite actions dropdown displaying */
			#favorite-actions {
				display: none;
			}
			<? endif; ?>
			
			/* change the logo */
			#header-logo {
				background-image: url(<?=plugins_url('images/admin-logo.png',__FILE__)?>) !important;
				width: 82px;
			}
			
			/* change the little icon in the top-left corner */
			#wp-admin-bar-wp-logo > .ab-item .ab-icon {
				background-image: url(<?=plugins_url('images/admin-bar-sprite.png',__FILE__)?>) !important;
			}
		</style>
	<?php
}
add_action('admin_head', 'tc_admin_css');

//
// This admin changes the footer text
//
function tc_admin_footer(){
  echo "Powered by <a href='http://tclayson.com'>tclayson.com</a>";
}
add_filter('admin_footer_text', 'tc_admin_footer');

//
// This filter removes the admin bar. I like it, but feel free to uncomment.
//
//add_filter('show_admin_bar', '__return_false');
  
//
// This function and filters brand the login page.
//
// The filters are there to change the URLs that links go to.
//
function tc_login_branding(){
	?>
		<style>
			body.login #login h1 a {
				background: url('<?=plugins_url('images/login-logo.png',__FILE__)?>') no-repeat scroll center top transparent;
				height: 64px;
				width: 307px;
				margin-left: 10px;
			}
		</style>
	<?php
}
add_action("login_head", "tc_login_branding");
add_filter('login_headertitle', create_function(false, "return 'http://tclayson.com';"));
add_filter('login_headerurl', create_function(false, "return 'http://tclayson.com';"));

?>