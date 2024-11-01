<?php
/*
Plugin Name: Single Menu Active
Plugin URI: http://www.fmarie.net
Description: Add CSS for active category in single.php.
Author: Florent MARIE.
Version: 1.0.0
Author URI: http://www.fmarie.net
Text Domain: single-menu-active
*/

if (!class_exists("singlemenuactive")) {
	class singlemenuactive {
		var $adminOptionsName = "singlemenuactiveAdminOptions";
		function singlemenuactive() { //constructor
			
		}
		function init() {
			$this->getAdminOptions();
		}
		//Returns an array of admin options
		function getAdminOptions() {
			$singlemenuAdminOptions = array('content1' => '', 'content2' => '');
			$smaOptions = get_option($this->adminOptionsName);
			if (!empty($smaOptions)) {
				foreach ($smaOptions as $key => $option)
					$singlemenuAdminOptions[$key] = $option;
			}				
			update_option($this->adminOptionsName, $singlemenuAdminOptions);
			return $singlemenuAdminOptions;
		}
		
		function addHeaderCode() {
			$smaOptions = $this->getAdminOptions();
			?>
<!-- singlemenu Was Here -->
			<?php
			if(is_single()){
					$cat = get_the_category();
				echo '<style type="text/css" media="all">
		.cat-item-'.$cat[0]->cat_ID.'{ '.$smaOptions['content1'].' }
		.cat-item-'.$cat[0]->cat_ID.' a,.cat-item-'.$cat[0]->cat_ID.' a:visited{ '.$smaOptions['content2'].' }
	</style>';
			}
		
		}
		function addContent($content = '') {
			$smaOptions = $this->getAdminOptions();
			if ($smaOptions['add_content'] == "true") {
				$content .= $smaOptions['content1'];
			}
			return $content;
		}
		//Prints out the admin page
		function printAdminPage() {
			$smaOptions = $this->getAdminOptions();
			if (isset($_POST['update_singlemenuactiveSettings'])) { 
				if (isset($_POST['singlemenuContentBack'])) {
					$smaOptions['content1'] = apply_filters('content_save_pre', $_POST['singlemenuContentBack']);
				}
				if (isset($_POST['singlemenuContentHref'])) {
					$smaOptions['content2'] = apply_filters('content_save_pre', $_POST['singlemenuContentHref']);
				}
				update_option($this->adminOptionsName, $smaOptions);
				?>
				<div class="updated"><p><strong><?php _e("Settings Updated.", "singlemenuactive");?></strong></p></div>
				<?php
			}
			?>
			<div class=wrap>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<h2>Single Menu Active</h2>
			<h3>Background</h3>
			<textarea name="singlemenuContentBack" style="width: 80%; height: 100px;"><?php _e(apply_filters('format_to_edit',$smaOptions['content1']), 'singlemenuactive') ?></textarea>
			<h3>Link</h3>
			<textarea name="singlemenuContentHref" style="width: 80%; height: 100px;"><?php _e(apply_filters('format_to_edit',$smaOptions['content2']), 'singlemenuactive') ?></textarea>
			<div class="submit"><input type="submit" name="update_singlemenuactiveSettings" value="<?php _e('Update Settings', 'singlemenuactive') ?>" /></div>
			</form>
			 </div>
			<?php
		}//End function printAdminPage()
	} //End Class singlemenuactive
} //End verif exists Class

if (class_exists("singlemenuactive")) {
	$dl_pluginSeries = new singlemenuactive();
}

//Initialize the admin panel
if (!function_exists("singlemenuactive_ap")) {
	function singlemenuactive_ap() {
		global $dl_pluginSeries;
		if (!isset($dl_pluginSeries)) {
			return;
		}
		if (function_exists('add_options_page')) {
	add_options_page('Single Menu Active', 'Single Menu Active', 9, basename(__FILE__), array(&$dl_pluginSeries, 'printAdminPage'));
		}
	}	
}

//Actions and Filters	
if (isset($dl_pluginSeries)) {
	//Actions
	add_action('admin_menu', 'singlemenuactive_ap');
	add_action('wp_head', array(&$dl_pluginSeries, 'addHeaderCode'), 1);
	add_action('single-menu-active/single-menu-active.php',  array(&$dl_pluginSeries, 'init'));
}

?>