<? 
/*
	Plugin Name: Rich Category Editor
	Plugin URI: http://andreapernici.com/wordpress/rich-category-editor/
	Description: Add TinyMce to the Category Description.
	Version: 1.0.1
	Author: Andrea Pernici
	Author URI: http://www.andreapernici.com/
	
	Copyright 2009 Andrea Pernici (andreapernici@gmail.com)
	
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

if (!class_exists("RichCategoryEditor")) {

	class RichCategoryEditor {
		/**
		 * Class Constructor
		 */
		function RichCategoryEditor(){
		
		}
		
		/**
		 * Enabled the RichCategoryEditor plugin with registering all required hooks
		 */
		function Enable() {

			add_action('load-categories.php', array("RichCategoryEditor",'SetAdminConfiguration'));
			add_action('load-edit-tags.php', array("RichCategoryEditor",'SetAdminConfiguration'));

		}
		
		/**
		 * Set the TinyMCE editor if the user can and only in right pages
		 */
		 
		function SetAdminConfiguration() {
			
			if ( user_can_richedit() && isset($_GET['action']) 
									 && 'edit' === $_GET['action'] 
									 && ( !empty($_GET['cat_ID']) || 
											( !empty($_GET['taxonomy']) && !empty($_GET['tag_ID']) ) 
										) 
									 ) {
				add_filter( 'tiny_mce_before_init', array('RichCategoryEditor','SetTinyMceButtons'));
				add_action('admin_footer', 'wp_tiny_mce');
			}
		}
		
		/**
		 * Setup TinyMce Buttons
		 */
		
		function SetTinyMceButtons($tmce) {
		
			$tmce['mode'] = 'textareas';
			$tmce['editor_selector'] = '';
			$tmce['elements'] = 'category_description,description';
			$tmce['plugins'] = 'safari,inlinepopups,autosave,spellchecker,paste,wordpress,media,fullscreen';
			$tmce['theme_advanced_buttons1'] .= ',image';
			$tmce['theme_advanced_buttons2'] .= ',code';
			$tmce['onpageload'] = '';
			$tmce['save_callback'] = '';
		
			return $tmce;
		}		
		
		/**
		 * Returns the plugin version
		 *
		 * Uses the WP API to get the meta data from the top of this file (comment)
		 *
		 * @return string The version like 1.0.1
		 */
		function GetVersion() {
			if(!function_exists('get_plugin_data')) {
				if(file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) require_once(ABSPATH . 'wp-admin/includes/plugin.php'); //2.3+
				else if(file_exists(ABSPATH . 'wp-admin/admin-functions.php')) require_once(ABSPATH . 'wp-admin/admin-functions.php'); //2.1
				else return "0.ERROR";
			}
			$data = get_plugin_data(__FILE__);
			return $data['Version'];
		}
		
		// By Max Bond - http://www.q2w3.ru/
		function display_desc() {
			if (is_tax() || is_tag() || is_category()) {
				global $wp_query;
				$term = $wp_query->get_queried_object();
				$taxonomy = $term->taxonomy;
				$term = $term->term_id;

				$term = get_term( $term, $taxonomy );

				echo $term->description.'<div class="clear"></div>'.PHP_EOL;
			}
		}
	}
}

/*
 * Plugin activation
 */
 
if (class_exists("RichCategoryEditor")) {
	$rce = new RichCategoryEditor();
}


if (isset($rce)) {
	add_action("init",array("RichCategoryEditor","Enable"),1000,0);
}

remove_filter('pre_term_description', 'wp_filter_kses');
?>