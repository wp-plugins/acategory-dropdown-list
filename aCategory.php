<?php
/*
Plugin Name: a´Category Dropdown List
Plugin URI: http://labs.alek.be/acategory/
Description: Replaces the category checkboxes by a dropdown menu on post’s edit page
Author: Aleksei Polechin (alek´)
Version: 1.2.7
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

if(!function_exists('isWPMU')):
	function isWPMU(){
		if (function_exists('is_multisite') && is_multisite()) return true;
		else return false;
	}
endif;

require ('aCategorySettings.php');

//ACTIVATION
register_activation_hook(__FILE__, 'aCategory_activation');
function aCategory_activation($network_wide){
	global $wpdb;
	if (isWPMU()){ //isWPMU verifyes id the site is in Multisite mode
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

	if(isWPMU()) {
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
			$option->multi = 0; // multi-choice disabled by default
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
	if (isWPMU()) {
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
	if (isWPMU()) delete_blog_option($wpdb->blogid, 'aCategory');
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
		$postTypes = $tax->object_type;
		foreach ( $postTypes as $PostType )
			if($taxonomy->replace == 1) remove_meta_box( $catBox, $PostType, 'side' );
	}
}

// Add categories/taxonomies custom boxes //
add_action( 'add_meta_boxes', 'add_aCategories_box' );
function add_aCategories_box(){
	global $wpdb;

	if (isWPMU()) $options = get_blog_option($wpdb->blogid, 'aCategory');
	else $options = get_option('aCategory');

	foreach($options as $taxonomy){
		$tax = get_taxonomy($taxonomy->slug);
		$catSlug = $taxonomy->slug;
		$catBox = 'a-'.$catSlug;
		$postTypes = $tax->object_type;
		foreach ( $postTypes as $PostType )
			if($taxonomy->replace == 1) add_meta_box( $catBox, $tax->labels->name, 'aCatSelect', $PostType, 'side', 'core', array( 'catSlug' => $catSlug, 'options' => $taxonomy));
	}
}

function aCatSelect($post, $catSlug){
	global $post;
	$boxID = $catSlug['id'];
	$options = $catSlug['args']['options'];
	$catSlug = $catSlug['args']['catSlug'];

	//$thisPostType = $post->post_type;

	$argName = ($options->slug == "category") ? 'post_category[]' : 'tax_input['.$catSlug.'][]';

	$orderby = $options->orderby;
	$order = $options->order;

	$depth = ($options->multi) ? 1 : 0; //$options->depth; // 0 - show all levels

	$none = ($options->none == 1 && $options->slug != "category") ? __('None') : 0;

	$default_category = get_option('default_category');

	$post_categories = wp_get_post_terms( $post->ID, $catSlug );
	if (!$post_categories) $post_categories = array(0);

	echo '<div>';

	$i = 0;
	$child_of = 0;
	while( $i < count($post_categories) ){
		$c = $post_categories[$i];
		if($c->parent == $child_of){
			$selected = ($c->term_id) ? $c->term_id : $default_category;

			if($child_of > 0) $none = __('None');

			$args = array(
				'orderby'            => $orderby,
				'order'              => $order,
				'show_option_none'   => $none,
				'hide_empty'         => 0,
				'echo'               => 1,
				'selected'           => $selected,
				'hierarchical'       => 1,
				'name'               => $argName,
				'class'              => 'postform acategory '.$catSlug,
				'depth'              => $depth,
				'tab_index'          => 0,
				'child_of'			  => $child_of,
				'taxonomy'           => $catSlug,
				'hide_if_empty'      => false );
			wp_dropdown_categories( $args );

			$child_of = $selected;
			$i=0;
		}
		else $i++;
	}

	$lastcatargs = array(
		'child_of'                 => $child_of,
		'hide_empty'               => 0,
		'hierarchical'             => 1,
		'taxonomy'                 => $catSlug,
		'pad_counts'               => false );
	$lastcat = get_categories( $lastcatargs );

	if($lastcat){
		$args = array(
			'orderby'            => $orderby,
			'order'              => $order,
			'show_option_none'   => __('None'),
			'hide_empty'         => 0,
			'echo'               => 1,
			'selected'           => $selected,
			'hierarchical'       => 1,
			'name'               => $argName,
			'class'              => 'postform acategory '.$catSlug,
			'depth'              => $depth,
			'tab_index'          => 0,
			'child_of'			  => $child_of,
			'taxonomy'           => $catSlug,
			'hide_if_empty'      => false );
		wp_dropdown_categories( $args );
	}

	echo '</div><span class="spinner acat_load" style="position:absolute; bottom:5px; right:38px;"></span>';

	?><style type="text/css">select.acategory{width:250px;}</style>

	<script type="application/javascript">
		jQuery(function($) {
			$('body').on('change', 'select.acategory.<?php echo $catSlug;?>', function(){
				var $this = $(this),
					level = $this.index(),
					maxlevel = $('select.acategory.<?php echo $catSlug;?>').length;

				for(i = level+1 ; i < maxlevel; i++){
					$('select.acategory:eq('+i+')').addClass('remove');
				}

				$('select.acategory.remove').remove()
				<?php if($options->multi){?>
				$('#a-<?php echo $catSlug;?> .acat_load').show();

				var data = {
					action: "aCatGetChildren",
					parent: $(this).val(),
					catSlug: '<?php echo $catSlug;?>',
					orderby: '<?php echo $options->orderby;?>',
					order: '<?php echo $options->order;?>',
					argName: '<?php echo $argName;?>',
				};
				$.post(ajaxurl, data, function(response) {
					$this.parent().append(response);
					$('#a-<?php echo $catSlug;?> .acat_load').hide();
				});
				<?php
				}
				?>
			});
		});
	</script>
<?php
}

add_action('wp_ajax_aCatGetChildren', 'aCatGetChildren');

function aCatGetChildren(){
	global $wpdb, $post;
	$parent = $_POST['parent'];
	$catSlug = $_POST['catSlug'];
	$orderby = $_POST['orderby'];
	$order = $_POST['order'];
	$argName = $_POST['argName'];

	$acat_children = $wpdb->get_results(" SELECT * FROM $wpdb->term_taxonomy WHERE parent = $parent");
	$children = ($acat_children ? true : exit());

	$args = array(
		'orderby'            => $orderby,
		'order'              => $order,
		'show_option_none'   => __('None'),
		'hide_empty'         => 0,
		'echo'               => 1,
		'hierarchical'       => 1,
		'name'               => $argName,
		'class'              => 'postform acategory '.$catSlug,
		'depth'              => 1,
		'tab_index'          => 0,
		'child_of'				 => $parent,
		'taxonomy'           => $catSlug,
		'hide_if_empty'      => false );

	wp_dropdown_categories( $args );
	exit();
}
?>