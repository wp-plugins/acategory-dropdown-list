<?php
if ($_POST['action'] == 'update'){save_aCategory_settings();}
function aCategory_settings(){
?>
<div class="wrap">
<div class="icon32"><img src="<?php echo plugins_url('icon32.png', __FILE__);?>" /></div>
<h2>aCategory Dropdown List</h2>

<form method="post" action="">
	<?php
	settings_fields('aCategory_options');
	do_settings_sections('aCategory_plugin');	
	?>
	<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</form>
</div>
<?php 
}

add_action('admin_init', 'aCategory_admin_init');
function aCategory_admin_init(){
	register_setting( 'aCategory_options', 'aCategory' );
	add_settings_section('aCategory_main', 'Settings', 'aCategory_option_main_show', 'aCategory_plugin');
	
	add_settings_field('categoryList', __('Categories'), 'aCategoryField', 'aCategory_plugin', 'aCategory_main');
}

function aCategoryField(){
	$options = get_option('aCategory');
	$Cats = explode(',',$options['categories']);
	
	$checked="";
	for($i=0; $i<count($Cats); $i++){
		if($Cats[$i]=='category') $checked='checked="checked"';
	}
	echo __('Categories').' <input type="checkbox" name="categories[0]"/ value="category" '.$checked.'><br/>';
	
	$i=1;
	$args=array('_builtin' => false); 
	$taxonomies=get_taxonomies($args,'objects', 'and'); 
	if  ($taxonomies) {
		foreach ($taxonomies  as $taxonomy ) {
			$catName = $taxonomy->labels->name;
			$catSlug = $taxonomy->rewrite['slug'];
			$catBox = 'a-'.$catSlug;
			
			$checked="";
			for($a=0; $a<count($Cats); $a++){
				if($Cats[$a]==$catSlug) $checked='checked="checked"';
			}
			echo $catName.' <input type="checkbox" name="categories['.$i.']"/ value="'.$catSlug.'" '.$checked.'><br/>';
			$i++;
		}
	}
}

function aCategory_option_main_show(){
	echo 'Chose category/taxonomy to chow as a dropdown list.';	
}

function save_aCategory_settings(){	
	if(empty($_POST['categories'])) $categories = '';
	else {
		$categories=""; $i=0;
		foreach($_POST['categories'] as $categorie){
			$categories .= $categorie;
			$i++;
			if ( $i < count($_POST['categories'])) $categories .= ",";
		}
	}
	$new_options = array('categories' => $categories);
	update_option('aCategory', $new_options);
}