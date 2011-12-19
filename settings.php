<?php
global $pagenow;
if ($pagenow == "options-general.php" && $_GET['page'] == 'aCategory' && isset($_POST['action'])){save_aCategory_settings();}
function aCategory_settings(){
settings_fields('aCategory_options');
?>
<div class="wrap">
<div class="icon32"><img src="<?php echo plugins_url('icon32.png', __FILE__);?>" /></div>
<h2><?php _e('a´Category Dropdown List Settings', 'acategory');?></h2>

		<div id="advanced-sortables" class="meta-box-sortables aCatInf">
			<div id="aiosp" class="postbox " ><div class="inside" style="overflow:hidden;">
			
			<div class="aCatinfo">
				<p>
					<?php _e('Choose taxonomies you want to display as a dropdown menu.', 'acategory');?><br />
					<?php _e('After enabling the replacement for a taxonomy you will able to select only one category(term) per post.', 'acategory');?>
				</p>
				<p class="description"><?php _e('Note: The plugin does not support non-hierarchical tags, they are not displayed here.', 'acategory');?></p>
			</div>
			<div class="donate">
				<h2><?php _e('Donate', 'acategory');?></h2>
				<?php 
					_e('If you like this plugin and find it useful, consider donating to support my work by clicking on "Donate" button.', 'acategory')
				?>
				<p>
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TQ47KSHNBX7HJ" target="_blank"><img src="https://www.paypalobjects.com/en_US/BE/i/btn/btn_donateCC_LG.gif" alt="Donate" /></a>
				</p>
			</div>
		
		</div></div></div>

<form method="post" action="">
	<?php
	do_settings_sections('aCategory_plugin');	
	?>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php _e('Save settings', 'acategory') ?>" />
		<input name="Submit" type="submit" class="button-primary reset" style="float:right; background:#900; border-color:#400" value="<?php _e('Reset settings', 'acategory') ?>" />
	</p>
	<input id="formaction" name="action" type="hidden" value="update" />
</form>
</div>
<?php 
}

add_action('admin_init', 'aCategory_admin_init');
function aCategory_admin_init(){
	register_setting( 'aCategory_options', 'aCategory' );
	add_settings_section('aCategory_main', '', 'aCategory_option_main_show', 'aCategory_plugin');
}

function aCategoryField(){
?>
<script>
jQuery(document).ready(function($) {
	$('.button-primary.reset').click(function(){
		$('#formaction').val('reset');
	});
	
	<?php if(isset($_GET['updated'])){?>
	setTimeout(function(){ $('#setting-error-settings_updated').fadeOut(1000);}, 3000);
	<?php }	?>
});

</script>
<style>
	.meta-box-sortables.aCatInf{margin-top:20px;}
	.aCatInf .inside{padding:10px!important;}
	p.submit{margin-top:10px;}
	p{margin:0 0 20px 0;}
	table tr:hover{background:#f2f2f2}
	.label-column, .name-column{width:180px;}	
	.options-column div.first{margin-bottom:4px; padding-bottom:3px; border-bottom: 1px solid #CCC}
	.options-column div{}
	.label-column label{display:block; height:40px;}
	.donate {float:right; width:40%;}
	.donate h2{margin:0; padding:0 0 10px 0; line-height:20px;}
	.donate p{margin:10px 0 0 0}
	.donate form{padding:0; margin:0;}
	.aCatinfo {float:left; width:55%;}
</style>
<table class="wp-list-table widefat fixed posts" cellspacing="0">
	<thead>
		<tr>
		<th scope='col' id='cb' class='manage-column column-cb check-column'  style=""></th>
		<th scope='col' id='title' class='manage-column label-column'  style="">
			<?php _e('Label'); ?>
		</th>
		<th scope='col' id='author' class='manage-column name-column'  style="">
			<?php _e('Name');?> (slug)
		</th>
		<th scope='col' id='author' class='manage-column slug-column'  style="">
			Rewrite slug
		</th>
		<th scope='col' id='categories' class='manage-column options-column'  style="">
			<?php _e('Options');?>
		</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
		<th scope='col' id='cb' class='manage-column column-cb check-column'  style=""></th>
		<th scope='col' id='title' class='manage-column label-column'  style="">
			<?php _e('Label'); ?>
		</th>
		<th scope='col' id='author' class='manage-column name-column'  style="">
			<?php _e('Name');?>
		</th>
		<th scope='col' id='author' class='manage-column slug-column'  style="">
			Rewrite slug
		</th>
		<th scope='col' id='categories' class='manage-column options-column'  style="">
			<?php _e('Options');?>
		</th>
		</tr>
	</tfoot>
	<tbody id="the-list">
<?php

	$options = get_option('aCategory');

	$i=1;
	$args=array('hierarchical' => true ); 
	$taxonomies=get_taxonomies($args, 'objects');
	$tx = "";
	if  ($taxonomies) {
		foreach ($taxonomies  as $taxonomy ) {
			$label = $taxonomy->labels->name;
			$slug = $taxonomy->rewrite['slug'];
			$name = $taxonomy->name;
			
			$tx .= $name.',';
			
			$checked="";
			if($options->$name->replace == 1) $checked = 'checked="checked"';

			?>
			<tr id="post-1" class="post-1 post type-post status-publish format-standard hentry category-uncategorized alternate iedit author-self" valign="top">
				<th scope="row" class="check-column">
					<?php echo '<input type="checkbox" id="'.$name.'" name="categories['.$name.']" value="1" '.$checked.'>';?>
				</th>
				<td class="label-column"><label for="<?php echo $name;?>"><?php echo $label; if($taxonomy->_builtin==1) echo ' <span class="description" style="color:#ababab">'.__('(built in)', 'acategory').'</span>';?></label></td>
				<td class="slug-column"><?php echo $name; ?></td>
				<td class="slug-column"><?php echo $slug; ?></td>
				<td class="options-column">
						<div class="first">
							<input type="checkbox" id="none_opt[<?php echo $name;?>]" name="none_opt[<?php echo $name;?>]" value="1" <?php if($taxonomy->_builtin==1) echo 'disabled="disabled"';?> <?php if(!empty($options) && $options->$name->none != 1) echo 'checked="checked"';?> /> <label for="none_opt[<?php echo $catSlug;?>]"><?php _e('Hide "None" option', 'acategory');?></label>
						</div>
						<div>
							orderby 
							<select name="by_opt[<?php echo $name;?>]">
								<option value="ID" <?php if($options->$name->orderby == "ID") echo 'selected="selected"';?>>ID</option>
								<option value="name" <?php if($options->$name->orderby == "name") echo 'selected="selected"';?>>name</option>
								<option value="slug" <?php if($options->$name->orderby == "slug") echo 'selected="selected"';?>>slug</option>
								<option value="menu_order" <?php if($options->$name->orderby == "menu_order") echo 'selected="selected"';?>>menu_order</option>
							</select>
							, order 
							<select name="order_opt[<?php echo $name;?>]">
								<option  value="ASC" <?php if($options->$name->order == "ASC") echo 'selected="selected"';?>>ASC</option>
								<option value="DESC" <?php if($options->$name->order == "DESC") echo 'selected="selected"';?>>DESC</option>
							</select>
						</div>
				</td>
			</tr>
			<?php
			$i++;
		}
	}
	?>
	</tbody>
</table>
<input type="hidden" name="tax" value="<?php echo $tx;?>"/>
<?php 
}

function aCategory_option_main_show(){ 
	aCategoryField();
}

function save_aCategory_settings(){	
	global $wpdb;
	
	$taxonomies = explode(',', $_POST['tax']);
	$options = new stdClass();
	
	if($_POST['action'] != "reset"){
		$taxs = $_POST['categories'];
		$none_opt = $_POST['none_opt'];
		$by_opt = $_POST['by_opt'];
		$order_opt = $_POST['order_opt'];
	
		foreach ($taxonomies as $taxonomy ) {
			if(!empty($taxonomy)){
				if($taxs[$taxonomy] == 1) $replace = 1;
				else $replace = 0;
				
				if($none_opt[$taxonomy]) $none = 0;
				else $none = 1;
				
				if($by_opt[$taxonomy]) $orderby = $by_opt[$taxonomy];
				else $orderby = 'name';
				
				if($order_opt[$taxonomy]) $order = $order_opt[$taxonomy];
				else $order = 'ASC';
				
				$option =  new stdClass();
					$option->slug = $taxonomy; // taxonomy slug
					$option->replace = $replace; // 1 = replace; 0 = wordpress default
					$option->none = $none; // 1 = show; 0 = hide
					$option->orderby = $orderby; // possible: name, slug, menu_order
					$option->order = $order; // ASC or DESC
				$options->$taxonomy = $option;
			}
		}
	}
	else $options='';
	update_option('aCategory', $options);
	Header("Location: options-general.php?page=aCategory&updated=true");	
}