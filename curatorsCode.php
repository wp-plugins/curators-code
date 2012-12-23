<?php
/*
Plugin Name: Curator's Code
Plugin URI: http://www.contentharmony.com/tools/curators-code-plugin/?utm_source=wordpress-plugin&utm_medium=tool-referral&utm_content=all-plugins-page&utm_campaign=curators-code
Description: The Curator's Code Plugin integrates the via (&#x1525;) and hat tip (&#x21ac;) attribution function of the Curator's Code into the Wordpress editor dashboard.
Author: <a href="http://www.contentharmony.com?utm_source=wordpress-plugin&utm_medium=tool-referral&utm_content=all-plugins-page&utm_campaign=curators-code">Content Harmony</a>
Version: 1.0

*/
class CuratorsCode{
	
	
	//Constructor Function
	function CuratorsCode(){	
	
		//Load WYSIWYG features
		add_action( 'init', array( &$this, 'init' ) );	
		
		if(is_admin()){
			//Load administration features
		//	register_activation_hook(__FILE__, array(&$this, 'defaultSettings'));
			add_action( 'admin_init', array(&$this, 'defaultSettings'));
			add_action( 'admin_init', array(&$this, 'settingsController'));
			add_action( 'admin_menu', array(&$this, 'addToAdminMenu'));
		
		}
	}
	
	//Intializer for plugin
	function init(){
	
		//Only load plugin features if user can edit posts/pages and uses the wysiwyg editor
		if( (current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing') == true){
			
			//register buttons and tinyMCE plugin
			add_filter('mce_buttons', array(&$this, 'mceFilterButtons'));
			add_filter('mce_external_plugins', array(&$this, 'mceFilterPlugins'));	
			
			//register styles
			wp_register_style('curators_code_main', $this->pluginUrl().'css/main.css');
			wp_register_style('curators_code_ui_styles', $this->pluginUrl().'css/smoothness/jquery-ui.css');
			
			//enqueue styles and jquery
			wp_enqueue_style('curators_code_main');
			wp_enqueue_style('curators_code_ui_styles');
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-dialog');
			
			
			//Load dialog element into wysiwyg editor
			add_filter('the_editor', array(&$this, 'loadDialog'));	
	
		} //endIf;
	}
	
	//Adds a link to the administration page on the admin menu
	function addToAdminMenu(){
		add_options_page('Curators Code Settings', 'Curators Code', 'manage_options', 'curators-code-settings.php', array(&$this, 'buildSettingsPage'));
		

	}
	
	//Adds plugin's buttons to wysiwyg editor
	function mceFilterButtons($buttons){
		array_push($buttons, '|', 'cc_viaButton');
		array_push($buttons, '|', 'cc_htButton'); 
		return $buttons;	
	}
	
	//Register's that a tinyMCE plugin is in use
	function mceFilterPlugins($plugin_array){
		$plugin_array['curatorsCode'] = $this->pluginUrl().'tinymce/editor_plugin.js';
		return $plugin_array;
	}
	
	//Registers the settings and creates the settings form
	function settingsController(){
		$settingsPage = 'curators-code-settings.php';
		register_setting('curatorsCodeSettings', 'cc_settings');
		add_settings_section('cc_settings', 'Curators Code Settings', array(&$this, 'outputcc_settings'), $settingsPage);
		add_settings_field('cc_letters', 'Use letters instead of symbols?', array(&$this, 'outputcc_letters'), $settingsPage, 'cc_settings');
		add_settings_field('cc_defaultAttribute', 'Link symbol to curatorscode.org?', array(&$this, 'outputcc_defaultAttribute'), $settingsPage, 'cc_settings');
		add_settings_field('cc_placeHolder', "", array(&$this, 'outputcc_placeHolder'), $settingsPage, 'cc_settings');
		
	}
	
	//Ensures that the settings exist in the database and sets their default values
	function defaultSettings(){
		
		$settings = get_option('cc_settings');
		if($settings['cc_placeHolder'] != 'default' ){
			$settings = array('cc_letters' => '0', 'cc_defaultAttribute' => '1', 'cc_placeHolder' => 'default');
			update_option('cc_settings', $settings);	
		}
		


	
	}
	
	
	/****************************************************************************************
	*
	*	Views
	*
	****************************************************************************************/
	
	//Creates the dialog used for inserting attributions
	function loadDialog($editor){
		$settings = get_option('cc_settings');

		$htmlToAdd = '<div id="cc_overlay">
					<div id="cc_dialogBox">
						<div class="topbar">
							<h3>
								
							</h3>
							<div class="escape-button">X</div>
							
						</div>
						<table class="cc_Form">';
						
		$htmlToAdd .= '<input id="cc_defaultAttribute" style="display:none;" value="'. $settings['cc_defaultAttribute'].'" />';
		$htmlToAdd .= '<input id="cc_letters" style="display:none;" value="'. $settings['cc_letters'].'"/>';		
		$htmlToAdd .= '
							<tr>
								<td class="cc_Label">Author<br /><br /></td>
								<td><input type="text" name="cc_author"/><br /><span class="cc_InputDesc">Author or creator of the content</span></td>
							</tr>
							<tr>
								<td class="cc_Label">Url<br /><br /></td>
								<td><input type="text" name="cc_source" /><br /><span class="cc_InputDesc">Url of content\'s source</span></td>
							</tr>
							
						</table>
					 <div class="errors">
                    
                    </div>
                    <div class="controls">
                    	<div id="cc_dialogAttribute" class="dialog-control">
                        	Attribute
                        </div>
                        <div id="cc_dialogClose" class="dialog-control">
                        	Close
                        </div>
                    </div>    
                </div>
					</div>';
		return $editor.$htmlToAdd;
	}
	
	
	//Outputs the settings page
	function buildSettingsPage(){
		?>
        	<div class="wrap">
                <h1><img class="cc_logo" src="<?php echo $this->pluginUrl().'images/cc_logo_medium.png';?>" alt="Curators Code Logo" /> Curator's Code</h1>
                 <div class="cc_descBox">
            	<h3>About</h3>
            	<p>
                	The internet is a vast repository of creative and intellectual works connected together by chains of hyperlinks. While there are systems in place for citing literature, imagery, and scientific works, there is yet to be a system that systemizes information discovery on the internet. Curator's Code is a suggested system to honor the creative and intellectual process of discovering and sharing information through the world wide web. 
                </p>
                <p>
                	Curator's Code uses two symbols for attributing content, ᔥ and ↬, meaning 'via' and 'hat-tip' or 'HT' respectively. The ᔥ symbol should be used when attributing a direct repost of content, i.e. you are sharing something that you found and made little to no elaboration or modifications to. The ↬ symbol should be used when indirectly using another's ideas, i.e. you got an idea from a source that you then proceeded to modify or elaborate significantly. 
                </p>
                <p>
                	<span style="font-weight:bold;">Curator's Code Plugin</span> ↬ <a href="http://www.curatorscode.org?utm_source=wordpress-plugin&utm_medium=wp-plugin-referral&utm_content=cc-plugin-settings&utm_campaign=curators-code-wp-plugin">curatorscode.org</a>
                </p>
                <p>
                	<span style="font-weight:bold;">Developed by:</span> <a href="http://www.graemebritz.com">Graeme Britz</a> &amp; <a href="http://www.kanejamison.com?utm_source=wordpress-plugin&utm_medium=tool-referral&utm_content=cc-plugin-settings&utm_campaign=curators-code">Kane Jamison</a> at <a href="http://www.contentharmony.com?utm_source=wordpress-plugin&utm_medium=tool-referral&utm_content=cc-plugin-settings&utm_campaign=curators-code">Content Harmony</a>
                </p>
            </div>
                
                <form action="options.php" method="post">
                <?php
                    settings_fields('curatorsCodeSettings');
                    do_settings_sections('curators-code-settings.php');
                	$option = get_option('cc_settings');
				 ?>
                 
                 <p>
                    <input type="submit" class="button-primary" name="Submit" value="<?php esc_attr_e('Save Changes'); ?>" />
                 </p>
                    
                </form>  
            </div>
       <?php
	}
	
	//Outputs text for the settings section (currently blank)
	function outputcc_settings(){
		echo '';
	}
	
	//Outputs cc_letters field
	function outputcc_letters(){
		$option = get_option('cc_settings');
		$checked = "";
		if($option['cc_letters']){
			$checked = 'checked="checked"';
		}
		echo '<input type="checkbox" id="cc_letters" name="cc_settings[cc_letters]" value="1" '.$checked.' />';
	}
	
	//Outputs cc_defaultAttribute field
	function outputcc_defaultAttribute(){
		$option = get_option('cc_settings');
		$checked = "";
		if($option['cc_defaultAttribute']){
			$checked = 'checked="checked"';
		}
		echo '<input type="checkbox" id="cc_defaultAttribute" name="cc_settings[cc_defaultAttribute]" value="1" '.$checked.' />';	
	}
	
	//outputs a hidden field for the placeholder value
	function outputcc_placeHolder(){
		$option = get_option('cc_settings');
		echo '<input type="hidden" id="cc_placeHolder" name="cc_settings[cc_placeHolder]" value="default" />';	
	}
	
	
	/*****************************************************************************************
	*
	* Helper Functions
	*	
	*****************************************************************************************/
	
	//returns the plugins url (with trailing slash)
	function pluginUrl(){
		return plugins_url().'/curators-code/';	
	}

}// End Curators Code class

//Instantiate an object of the class
$curatorsCode = new CuratorsCode();

?>