<?php
/*
Plugin Name: a´Category Dropdown List
Plugin URI: http://labs.alek.be/acategory/
Description: Replaces the category checkboxes by a dropdown menu on post’s edit page
Author: Aleksei Polechin (alek´)
Version: 1.0r8
Author URI: http://alek.be
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/
$aCatV = "1.0"; // release
$aCatR = "8"; // revision

add_action('init', 'aCategory_init');
function aCategory_init() {
	if ( !defined('WP_PLUGIN_DIR') ) {
		load_plugin_textdomain('acategory', str_replace( ABSPATH, '', dirname(__FILE__)));
	} else {
		load_plugin_textdomain('acategory', false, dirname(plugin_basename(__FILE__)));
	}
}

require ('settings.php');


//ACTIVATION
register_activation_hook(__FILE__, 'aCategory_activation');
function aCategory_activation(){
	$options= '';
	add_option('aCategory', $options);
}
//UNINSTALL 
register_uninstall_hook(__FILE__, 'aCategory_uninstall');
function aCategory_uninstall(){
	delete_option('aCategory');
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
	$options = get_option('aCategory');
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
?>