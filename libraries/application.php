<?php

class OST_Application
{	

	public function init()
	{
		add_shortcode("ostoolbar", array("OST_Application", "display"));
		
		get_role('administrator')->add_cap( 'see_videos' );
		$text = get_option('toolbar_permission');
		if ($text == "")
			$text = '{"editor":0,"contributor":0,"author":0,"subscriber":0}';
		$permission = json_decode($text, true);
		foreach ($permission as $key => $value)
		{
			if ($value)
				get_role($key)->add_cap("see_videos");
			else
				get_role($key)->remove_cap("see_videos");
		}
		
		$config = new OST_Configuration();
		add_action('admin_init', array($config, 'init_settings'));
		add_action('admin_menu', array($this, 'init_admin_links'));
		add_action('admin_head', array($this, 'load_js'));
		
		if ($_GET['page'] == 'ostoolbar') {
			add_action('admin_notices', array($this, 'api_key_check'));
		}
		add_action('init', array($this, 'ostoolbar_add_editor_button'));
	}
	
	function display()
	{
		ob_start();
		if (!$_GET["id"])
			OST_Controller::action_tutorials(true);
		else
			OST_Controller::action_tutorial($_GET["id"]);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	function ostoolbar_add_editor_button() {
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;
			
		if ( get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", array($this, "ostoolbar_load_plugin"));
			add_filter('mce_buttons', array($this, 'ostoolbar_register_button'));
		}
	} 
	
	// ---
	function ostoolbar_load_plugin($plugin_array) {
		$plug = plugins_url('mce/editor_plugin.js',__FILE__);
		$plugin_array['ostoolbar_plugin'] = $plug;
		return $plugin_array;
	}
	
	// ---
	function ostoolbar_register_button($buttons) {
		$b[] = "separator";
		$b[] = "ostoolbar_plugin_button";
		if ( is_array($buttons) && !empty($buttons) ) {
			$b = array_merge( $buttons, $b );
		}
		return $b;
	}

	public function api_key_check() {
		$api_key = get_option('api_key');
		if (!$api_key) {
			//echo "<div class='error'>Please enter an API key in the <a href='options-general.php?page=options-ostoolbar'>OSToolbar settings</a>.</div>";
		}
	}

	public function init_admin_links()
	{
		$controller = OST_Factory::getInstance('OST_Controller');

		$title = get_option('toolbar_text') == "" ? "OSToolbar":  get_option('toolbar_text');
		$icon = get_option('toolbar_icon') == "" ? "ost_icon.png":  get_option('toolbar_icon');

		wp_deregister_style( 'ostoolbar_menu_css' );
		wp_register_style( 'ostoolbar_menu_css', plugins_url('ostoolbar/assets/css/menu.php?icon='.$icon));
		wp_enqueue_style( 'ostoolbar_menu_css' );
		

		add_object_page($title, $title, 'see_videos', 'ostoolbar', array($controller, 'action_tutorials'), ""); //, plugins_url('/ostoolbar/assets/images/'.get_option('toolbar_icon'))
		//add_submenu_page('ostoolbar', __($title . ' > Tutorials', 'ostoolbar'), __('Tutorials', 'ostoolbar'), 'manage_options', 'ostoolbar', array($controller, 'action_tutorials'));    
		//add_submenu_page('ostoolbar', __('OSToolbar > Help', 'ostoolbar'), __('Help', 'ostoolbar'), 'manage_options', 'ostoolbar_help', array($controller, 'action_help'));
		
		add_options_page( __($title.' Configuration', 'ostoolbar'), $title, 'manage_options', 'options-ostoolbar', array($controller, 'action_configuration'));
	}
	
	public function start_listener()
	{
		$model = OST_Factory::getInstance('OSTModel_Help');
		$model->listen();
	}
	
	public function load_js() {
		$height = get_option('popup_height');
		$width = get_option('popup_width');
		
		if (!$height) $height = 500;
		if (!$width) $width = 500;
		
		echo "
		<script type='text/javascript'>
			function ostoolbar_popup(address, title, params)
			{
				if (params == null)
				{
					params = {};
				}
			
				if (!params.height)
				{
					params.height = $height;
				}
				
				if (!params.width)
				{
					params.width = $width;
				}
				
				var attr = [];
				for (key in params)
				{
					attr.push(key+'='+params[key]);
				}
				
				attr = attr.join(',');
				
				window.open(address, title, attr);
				
			}
		</script>
		";
	}


	
}