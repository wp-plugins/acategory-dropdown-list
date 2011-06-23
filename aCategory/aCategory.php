<?php
/*
Plugin Name: a´Category Dropdown List
Plugin URI: http://labs.alek.be/acategory/
Description: This plugin replaces the category checkboxes by a dropdown list in posts administration. You can choose only one category per post.
Author: Aleksei Polechin (alek´)
Version: 0.9
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

require ('settings.php');

//ACTIVATION
register_activation_hook(__FILE__, 'aCategory_activation');
function aCategory_activation(){
	$options=array('categories' => '');
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
	add_options_page('a´Category Options', 'a´Category', 'manage_options', 'aCategory', 'aCategory_settings');
}

// Remove default categories/taxonomies boxes //	
add_action( 'admin_menu' , 'remove_default_categories_box' );
function remove_default_categories_box() {
	$options = get_option('aCategory');
	$Cats = explode(',',$options['categories']);
	
	print_r (get_post_meta($postID, 'taxonomy'));
	
	for($a=0; $a<count($Cats); $a++){
		if($Cats[$a]=='category') remove_meta_box( 'categorydiv', 'post', 'side' );
	}
	 
	$args=array('_builtin' => false); 
	$taxonomies=get_taxonomies($args, 'objects', 'and'); 
	if ($taxonomies) {
		foreach ($taxonomies  as $taxonomy ) {
			$catSlug = $taxonomy->rewrite['slug'];
			$catBox = $catSlug.'div';
			
			$postType = $taxonomy->object_type[0];
			
			for($a=0; $a<count($Cats); $a++){
				if($Cats[$a]==$catSlug) remove_meta_box( $catBox, $postType, 'side' );
			}
		}
	}
}

// Add custom categories/taxonomies boxes //
add_action( 'add_meta_boxes', 'add_aCategories_box' );
function add_aCategories_box(){
	$options = get_option('aCategory');
	$Cats = explode(',',$options['categories']);
	
	$catSlug = 'category';
	
	for($a=0; $a<count($Cats); $a++){
		if($Cats[$a]=='category') add_meta_box( 'aCategory', __('Categories'), 'aCatSelect', 'post', 'side', 'core', array( 'catSlug' => $catSlug));
	}

	$args=array('_builtin' => false); 
	$taxonomies=get_taxonomies($args,'objects', 'and'); 
	if  ($taxonomies) {
		foreach ($taxonomies  as $taxonomy ) {
			$catSlug = $taxonomy->rewrite['slug'];
			$catBox = 'a-'.$catSlug;
			
			$postType = $taxonomy->object_type[0];
			
			for($a=0; $a<count($Cats); $a++){
				if($Cats[$a]==$catSlug) add_meta_box( $catBox, $taxonomy->labels->name, 'aCatSelect', $postType, 'side', 'core', array( 'catSlug' => $catSlug));
			}
		}
	}
}

function aCatSelect($post, $catSlug){
	global $post;
	$boxID = $catSlug['id'];
	$catSlug = $catSlug['args']['catSlug'];
	
	$thisPostType = $post->post_type;
	$thisPOST = $post->ID;
	$theCatId = get_the_terms( $thisPOST, $catSlug );
	$theCatId = $theCatId[0];
	
	if ($catSlug == "category"){
		$argNone = false;
		$argName = 'post_category[]';
	}
	else {
		$argNone = 'None';
		$argName = 'tax_input['.$catSlug.'][]';
	}
		
	$args = array(
    'orderby'            => 'ID', 
    'order'              => 'ASC',
		'show_option_none'   => $argNone,
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