<?php
/*
Plugin Name: a´Category Dropdown List
Plugin URI: http://labs.alek.be/acategory/
Description: Replaces the category checkboxes by a dropdown menu on post’s edit page
Author: Aleksei Polechin (alek´)
Version: 1.1.0
Author URI: http://alek.be
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

add_action('init', 'aCategory_init');
function aCategory_init() {
	if ( !defined('WP_PLUGIN_DIR') ) {
		load_plugin_textdomain('acategory', str_replace( ABSPATH, '', dirname(__FILE__)));
	} else {
		load_plugin_textdomain('acategory', false, dirname(plugin_basename(__FILE__)));
	}
}

require ('aCategorySettings.php');

//ACTIVATION
register_activation_hook(__FILE__, 'aCategory_activation');
function aCategory_activation($network_wide){
	global $wpdb;
   if (isMU()){ //isMU verifyes id the site is in Multisite mode
		// check if it is a network activation - if so, run the activation function for each blog id
		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids =  $wpdb->get_results("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blogid) {
				$blog_id = $blogid->blog_id;
				switch_to_blog($blog_id);
				_acategory_activate();
			}
			switch_to_blog($old_blog);
			return;
		}   
	} 
	_acategory_activate();
}

function _acategory_activate() {
	global $wpdb;	
	$options = aDefaultOptions();
		
	if(isMU()) {
		update_blog_option($wpdb -> blogid, 'aCategory', $options);
		add_blog_option($wpdb -> blogid, 'aCategory', $options);
	}
	else {
		update_option('aCategory', $options);
		add_option('aCategory', $options);
	}
}
function aDefaultOptions(){
	$options= new stdClass();
	$args=array('hierarchical' => true ); 
	$taxonomies=get_taxonomies($args, 'objects');
	foreach ($taxonomies  as $taxonomy ) {
		$tax = $taxonomy->name;
		$replace = 0;
		$none = 1;
		$orderby = 'name';
		$order = 'ASC';
		$option =  new stdClass();
			$option->slug = $tax; // taxonomy slug
			$option->replace = $replace; // 1 = replace; 0 = wordpress default
			$option->none = $none; // 1 = show; 0 = hide
			$option->orderby = $orderby; // possible: name, slug, menu_order
			$option->order = $order; // ASC or DESC
		$options->$tax = $option;
	}
	return $options;
}

add_action( 'wpmu_new_blog', 'aCat_new_blog', 10, 6); // in case of creation of a new site in WPMU
function aCat_new_blog($blog_id) {
	global $wpdb;

	if (is_plugin_active_for_network('acategory-dropdown-list/aCategory.php')) {
		$old_blog = $wpdb->blogid;
		switch_to_blog($blog_id);
		_acategory_activate();
		switch_to_blog($old_blog);
	}
}

//UNINSTALL 
register_uninstall_hook(__FILE__, 'aCategory_uninstall');
function aCategory_uninstall(){
	global $wpdb;
   if (isMU()) {
		$old_blog = $wpdb->blogid;
		// Get all blog ids
		$blogids =  $wpdb->get_results("SELECT blog_id FROM $wpdb->blogs");
		foreach ($blogids as $blogid) {
			$blog_id = $blogid->blog_id;
			switch_to_blog($blog_id);
			_acategory_uninstall();
		}
		switch_to_blog($old_blog);
		return;
	} 
	_acategory_uninstall();
}
function _acategory_uninstall(){
	global $wpdb;
	if (isMU()) delete_blog_option($wpdb->blogid, 'aCategory');
	else delete_option('aCategory');
}

// SETTINGS
add_action('admin_menu', 'aCategorySettingMenu');
function aCategorySettingMenu() {
		add_options_page('a´Category Settings', 'a´Category', 'manage_options', 'aCategory', 'aCategory_settings');
}

// Remove default categories/taxonomies boxes //	
add_action( 'admin_menu' , 'remove_default_categories_box' );
function remove_default_categories_box() {
	$options = get_option('aCategory');

	foreach($options as $taxonomy){
		$tax = get_taxonomy($taxonomy->slug);			
		$catSlug = $taxonomy->slug;
		$catBox = $catSlug.'div';
		$postType = $tax->object_type[0];
		if($taxonomy->replace == 1) remove_meta_box( $catBox, $postType, 'side' );
	}
}

// Add categories/taxonomies custom boxes //
add_action( 'add_meta_boxes', 'add_aCategories_box' );
function add_aCategories_box(){
	global $wpdb;
	
	if (isMU()) $options = get_blog_option($wpdb->blogid, 'aCategory');
	else $options = get_option('aCategory');
	foreach($options as $taxonomy){
		$tax = get_taxonomy($taxonomy->slug);			
		$catSlug = $taxonomy->slug;
		$catBox = 'a-'.$catSlug;
		$postType = $tax->object_type[0];
		if($taxonomy->replace == 1) add_meta_box( $catBox, $tax->labels->name, 'aCatSelect', $postType, 'side', 'core', array( 'catSlug' => $catSlug, 'options' => $taxonomy));
	}
}

function aCatSelect($post, $catSlug){
	global $post;
	$boxID = $catSlug['id'];
	$options = $catSlug['args']['options'];
	$catSlug = $catSlug['args']['catSlug'];
	
	$thisPostType = $post->post_type;
	$thisPOST = $post->ID;
	$theCatId = get_the_terms( $thisPOST, $catSlug );
	$theCatId = $theCatId[0];
	
	if ($options->slug == "category")	$argName = 'post_category[]';
	else $argName = 'tax_input['.$catSlug.'][]';
	
	$orderby = $options->orderby;
	$order = $options->order;
	
	if($options->none == 1 && $options->slug != "category") $none = __('None');
	else $none = 0;
		
	$args = array(
    'orderby'            => $orderby, 
    'order'              => $order,
	 'show_option_none'   => $none,
    'hide_empty'         => 0, 
    'echo'               => 1,
    'selected'           => $theCatId->term_id,
    'hierarchical'       => 1, 
    'name'               => $argName,
    'class'              => 'postform',
    'depth'              => 0,
    'tab_index'          => 0,
    'taxonomy'           => $catSlug,
    'hide_if_empty'      => false );	
	
	wp_dropdown_categories( $args );
	
	?><style type="text/css">#<?php echo $boxID;?> select{width:250px;}</style><?php	
}

function isMU(){
	if (function_exists('is_multisite') && is_multisite()) return true;
	else return false;
}
?>