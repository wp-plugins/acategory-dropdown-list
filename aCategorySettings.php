<?php
if ((isset($_GET['page']) && $_GET['page'] == 'aCategory') && isset($_POST['aCatAction'])){save_aCategory_settings();}

add_action('admin_init', 'aCategory_admin_init');
function aCategory_admin_init(){
	register_setting( 'aCategory_options', 'aCategory' );
	add_settings_section('aCategory_main', '', 'aCategory_option_main_show', 'aCategory_plugin');
}
function aCategory_settings(){
	settings_fields('aCategory_options');
	?>
	<div class="wrap">
	<div class="icon32"><img src="<?php echo plugins_url('icon32.png', __FILE__);?>" /></div>
	<h2><?php _e('a´Category Dropdown List Settings', 'acategory');?></h2>
	
	<style type="text/css">
p{margin:0 0 20px 0;}
		table tr:hover{background:#f2f2f2}
		.options-column div.first{margin-bottom:4px; padding-bottom:3px; border-bottom: 1px solid #CCC}
		.options-column{width:290px;}
		.label-column label{display:block; height:40px;}
		.button-primary.reset{			
			background-color: #BA032E;
			background-image: -webkit-gradient(linear,left top,left bottom,from(#BA032E),to(#8C0002));
			background-image: -webkit-linear-gradient(top,#BA032E,#8C0002);
			background-image: -moz-linear-gradient(top,#BA032E,#8C0002);
			background-image: -ms-linear-gradient(top,#BA032E,#8C0002);
			background-image: -o-linear-gradient(top,#BA032E,#8C0002);
			background-image: linear-gradient(to bottom,#BA032E,#8C0002);
			border-color: #800;			
			float:right;
		}
		.button-primary.reset:hover, .button-primary.reset:focus{			
			background-color: #D30434;
			background-image: -webkit-gradient(linear,left top,left bottom,from(#D30434),to(#8C0002));
			background-image: -webkit-linear-gradient(top,#D30434,#8C0002);
			background-image: -moz-linear-gradient(top,#D30434,#8C0002);
			background-image: -ms-linear-gradient(top,#D30434,#8C0002);
			background-image: -o-linear-gradient(top,#D30434,#8C0002);
			background-image: linear-gradient(to bottom,#D30434,#8C0002);
			border-color: #800;			
		}
		.button-primary.reset.disabled, .button-primary.reset.disabled:hover{			
			background-color: #8C0002;
			background-image: none;
			border-color: #800;		
			color:rgba(255,255,255,.5);
			cursor:default;
		}
	</style>
	<script>
	jQuery(document).ready(function($) {
		$('body').on('click', '.button-primary.reset:not(.confirm, .disabled)', function(e){
			e.preventDefault();
			$this = $(this);
			$this.addClass('confirm');
			var resetLabel = $this.val();
			var confirmLabel = '<?php _e('Confirm reset', 'acategory');?>';
			var timer = 5;
			$this.val(confirmLabel + ' (' + timer + ')');
			t = setInterval(
				function(){
					timer -= 1;
					$this.val(confirmLabel + ' (' + timer + ')');
					if(timer == 0){
						clearInterval(t);
						$this.removeClass('confirm').val(resetLabel);	
					}
				}
				, 1000
			);
		});
		
		$('body').on('click', '.button-primary.reset.confirm:not(.disabled)', function(){
			clearInterval(t);
			$('#formaction').val('aCategoryReset');
			$(this).val('<?php _e('Resetting...', 'acategory');?>').addClass('disabled');
		});
		
		<?php if(isset($_GET['updated'])){?>
		$('#setting-error-settings_updated').delay(3000).slideUp(400);
		<?php }	?>
	});
	</script>
	
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<p>
					<?php _e('Choose taxonomies you want to display as a dropdown menu.', 'acategory');?> 
					<?php _e('Check "Multiple choice" if you want to be able to select a category and its subcategories.', 'acategory');?><br />
					<span class="description"><?php _e('Note: The plugin does not support non-hierarchical tags, they are not displayed here.', 'acategory');?></span>
				</p>
		
				<form method="post" action="">
					<?php
					do_settings_sections('aCategory_plugin');	
					?>
					<input id="formaction" name="aCatAction" type="hidden" value="aCategoryUpdate" /><br />

					<p>
						<input name="Submit" type="submit" class="button-primary" value="<?php _e('Save settings', 'acategory') ?>" />
						<input name="Submit" type="submit" class="button-primary reset" value="<?php _e('Reset settings', 'acategory') ?>" />
					</p>
				</form>
			</div>

			
			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox">
					<div class="inside" style="padding:15px;">
						<?php 
						$feed_url = 'http://labs.alek.be/category/acategory/feed/';
						if (!$fp = curl_init($feed_url)){
							$feed = (array) simplexml_load_file($feed_url);
							$items = $feed['channel']->item;
							$count = count($items);
						}
						else $count = 0;
						
						if($count > 0):
						?>
						<h2 style="font-size:24px; margin:0;"><?php _e('News/Updates', 'acategory');?> <span class="description">RSS feed</span></h2>
						<p>
						<?php
							$i=0;									
							foreach($items as $item){
								if($i < 3){
									$date = strtotime($item->pubDate);
									echo '<p style="overflow:hidden; margin-bottom:0; margin-top:4px;">';
									echo '<label class="date" style="display:block; width: 20%; float:left;"><strong>'.date('d/m', $date).'</strong></label>';
									echo '<a style="display:block; width: 80%; float:right;" href="'.$item->guid.'" class="title" target="_blank">'.$item->title.'</a>';
									echo '</p>';
								}
								$i++;
							}	
							if($count > 3){
								$cat_url = 'http://labs.alek.be/category/archives-calendar/';
								echo '<p style="margin-bottom:0; margin-top:6px;">';
								echo '<strong><a href="'. $cat_url .'" class="title" target="_blank">'. __('More') .' ...</a></strong>';
								echo '</p>';	
							}
							if($count <= 0) _e( 'No posts', 'acategory' );
						?>
						</p>
						<?php
						endif;
						?>
						<h2 style="font-size:24px; margin:0;"><?php _e('Also', 'acategory');?></h2>
						<h4>
							<strong><?php _e('My other projects on ', 'acategory');?></strong>
						</h4>
						<p>
							<a href="https://github.com/alekart?tab=repositories" target="_blank">GitHub</a><br>
							<a href="http://profiles.wordpress.org/alekart/" target="_blank">WordPress Plugins</a><br>
							<a href="http://labs.alek.be/" target="_blank">Labs by alek´</a>
						</p>
						<h4>
							<strong><?php _e('My portfolio:', 'acategory');?></strong>
						</h4>
						<p>
							<a href="http://alek.be/" target="_blank">alek.be</a>
						</p><br>
						<hr>
						<p>
							<?php _e('If you like this plugin please <strong>support my work</strong>, buy me <strong>a beer or a coffee</strong>. Click Donate and specify your amount.', 'acategory');?>
						</p>
						<p style="text-align:center">
							<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3AKT87C9SYN4U" target="_blank"><img src="https://www.paypalobjects.com/en_US/BE/i/btn/btn_donateCC_LG.gif" alt="Donate" /></a><br>
							<span class="description" style="font-size:10px;"><?php _e('In Belgium 1 coffee or 1 beer costs about 2€', 'acategory');?></span>
						</p>
					</div>
				</div>

				
			</div>
		</div>
	</div>
<?php 
}
					
function aCategoryField(){
	global $wpdb;
	?>
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
								<label><input type="checkbox" id="none_opt[<?php echo $name;?>]" name="none_opt[<?php echo $name;?>]" value="1" <?php if($taxonomy->_builtin==1) echo 'disabled="disabled"';?> <?php if(!empty($options) && $options->$name->none != 1) echo 'checked="checked"';?> /> <?php _e('Hide "None" option', 'acategory');?></label>
								&emsp;
								<label><input type="checkbox" id="multi_opt[<?php echo $name;?>]" name="multi_opt[<?php echo $name;?>]" value="1" <?php if(!empty($options) && $options->$name->multi == 1) echo 'checked="checked"';?> /> <?php _e('Multiple choice', 'acategory');?></label>
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
	
	if($_POST['aCatAction'] != "aCategoryReset"){
		if(isset($_POST['categories'])) $taxs = $_POST['categories'];
		else $taxs = array();
		if(isset($_POST['none_opt'])) $none_opt = $_POST['none_opt'];
		else $none_opt = array();
		if(isset($_POST['multi_opt'])) $multi_opt = $_POST['multi_opt'];
		else $multi_opt = array();
		$by_opt = $_POST['by_opt'];
		$order_opt = $_POST['order_opt'];
	
		foreach ($taxonomies as $taxonomy ) {
			if(!empty($taxonomy)){
				if($taxs[$taxonomy] == 1) $replace = 1;
				else $replace = 0;
				
				if(!empty($none_opt[$taxonomy])) $none = 0;
				else $none = 1;
				
				if($by_opt[$taxonomy]) $orderby = $by_opt[$taxonomy];
				else $orderby = 'name';
				
				if($order_opt[$taxonomy]) $order = $order_opt[$taxonomy];
				else $order = 'ASC';

				if($multi_opt[$taxonomy]) $multi = $multi_opt[$taxonomy];
				else $multi = 0;
				
				$option =  new stdClass();
					$option->slug = $taxonomy; // taxonomy slug
					$option->replace = $replace; // 1 = replace; 0 = wordpress default
					$option->none = $none; // 1 = show; 0 = hide
					$option->orderby = $orderby; // possible: name, slug, menu_order
					$option->order = $order; // ASC or DESC
					$option->multi = $multi; // 1 or 0 // allow multiple choice or not
				$options->$taxonomy = $option;
			}
		}
	}
	else {
		foreach ($taxonomies as $taxonomy ) {
			if(!empty($taxonomy)){
				$replace = 0;
				$none = 1;
				$orderby = 'name';
				$order = 'ASC';
				$multi = 0;
				$option =  new stdClass();
					$option->slug = $taxonomy; // taxonomy slug
					$option->replace = $replace; // 1 = replace; 0 = wordpress default
					$option->none = $none; // 1 = show; 0 = hide
					$option->orderby = $orderby; // possible: name, slug, menu_order
					$option->order = $order; // ASC or DESC
					$option->multi = $multi; // 1 or 0 // allow multiple choice or not
				$options->$taxonomy = $option;
			}
		}
	}

	if(isWPMU()) update_blog_option($wpdb->blogid, 'aCategory', $options);
	else update_option('aCategory', $options);
	Header("Location: options-general.php?page=aCategory&updated=true");	
}