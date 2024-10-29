<?php

/**
 * Backend Class for use in all minilib plugins
 * Version 0.3.1
 */

if (!class_exists('Minilib_Plugin_Admin')) {
	class Minilib_Plugin_Admin {

		var $hook 		= '';
		var $filename	= '';
		var $longname	= '';
		var $shortname	= '';
		var $ozhicon	= '';
		var $optionname = '';
		var $homepage	= '';
		var $accesslvl	= 'manage_options';
		
		function Minilib_Plugin_Admin() {
			add_action( 'admin_menu', array(&$this, 'register_settings_page') );
			add_filter( 'plugin_action_links', array(&$this, 'add_action_link'), 10, 2 );
			add_filter( 'ozh_adminmenu_icon', array(&$this, 'add_ozh_adminmenu_icon' ) );				
			
			add_action('admin_print_scripts', array(&$this,'config_page_scripts'));
			add_action('admin_print_styles', array(&$this,'config_page_styles'));	
			
			add_action('wp_dashboard_setup', array(&$this,'widget_setup'));	
		}
		
		function add_ozh_adminmenu_icon( $hook ) {
			if ($hook == $this->hook) 
				return WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname($filename)). '/'.$this->ozhicon;
			return $hook;
		}
		
		function config_page_styles() {
			if (isset($_GET['page']) && $_GET['page'] == $this->hook) {
				wp_enqueue_style('dashboard');
				wp_enqueue_style('thickbox');
				wp_enqueue_style('global');
				wp_enqueue_style('wp-admin');
				wp_enqueue_style('blogicons-admin-css', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)). '/aga_tools-min.css');
			}
		}

		function register_settings_page() {
			add_options_page($this->longname, $this->shortname, $this->accesslvl, $this->hook, array(&$this,'config_page'));
		}
		
		function plugin_options_url() {
			return admin_url( 'options-general.php?page='.$this->hook );
		}
		
		/**
		 * Add a link to the settings page to the plugins list
		 */
		function add_action_link( $links, $file ) {
			static $this_plugin;
			if( empty($this_plugin) ) $this_plugin = $this->filename;
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}
		
		function config_page() {
			
		}
		
		function config_page_scripts() {
			if (isset($_GET['page']) && $_GET['page'] == $this->hook) {
				wp_enqueue_script('postbox');
				wp_enqueue_script('dashboard');
				wp_enqueue_script('thickbox');
				wp_enqueue_script('media-upload');
			}
		}

		/**
		 * Create a Checkbox input field
		 */
		function checkbox($id, $label) {
			$options = get_option($this->optionname);
			return '<input type="checkbox" id="'.$id.'" name="'.$id.'"'. checked($options[$id],true,false).'/> <label for="'.$id.'">'.$label.'</label><br/>';
		}
		
		/**
		 * Create a Text input field
		 */
		function textinput($id, $label) {
			$options = get_option($this->optionname);
			return '<label for="'.$id.'">'.$label.':</label><br/><input size="45" type="text" id="'.$id.'" name="'.$id.'" value="'.$options[$id].'"/><br/><br/>';
		}

		/**
		 * Create a potbox widget
		 */
		function postbox($id, $title, $content) {
		?>
			<div id="<?php echo $id; ?>" class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span><?php echo $title; ?></span></h3>
				<div class="inside">
					<?php echo $content; ?>
				</div>
			</div>
		<?php
		}	


		/**
		 * Create a form table from an array of rows
		 */
		function form_table($rows) {
			$content = '<table class="form-table">';
			foreach ($rows as $row) {
				$content .= '<tr><th valign="top" scrope="row">';
				if (isset($row['id']) && $row['id'] != '')
					$content .= '<label for="'.$row['id'].'">'.$row['label'].':</label>';
				else
					$content .= $row['label'];
				if (isset($row['desc']) && $row['desc'] != '')
					$content .= '<br/><small>'.$row['desc'].'</small>';
				$content .= '</th><td valign="top">';
				$content .= $row['content'];
				$content .= '</td></tr>'; 
			}
			$content .= '</table>';
			return $content;
		}

		/**
		 * Create a "plugin like" box.
		 */
		function plugin_like() {
			$content = '<p>'.__('Why not do any or all of the following:','mlbplugin').'</p>';
			$content .= '<div style="margin:0 auto;padding:5px 0;text-align:center;"><a href="http://2010.wphonors.com/plugin/asynchronous-google-analytics-for-wordpress/" target="_blank" rel="nofollow"><img src="'.gapp_plugin_path().'sbm/wphonors3.png" alt="Vote4Me Asynchronous Google Analytics for WordPress at 2010.WPHonors.com" /></a></div>';
			$content .= '<ul>';
			$content .= '<li><a href="'.$this->homepage.'">'.__('Link to it so other folks can find out about it.','mlbplugin').'</a></li>';
			$content .= '<li><a href="http://wordpress.org/extend/plugins/'.$this->hook.'/">'.__('Give it a good rating on WordPress.org.','mlbplugin').'</a></li>';
			$content .= '<li>'.__('Donate a token of your appreciation.','mlbplugin').'<br/><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="5YRVWPQFPB8MN"><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"></form></li>';
			$content .= '</ul>';
			$this->postbox($this->hook.'like', 'Like this plugin?', $content);
		}	
		
		/**
		 * Info box with link to the support forums.
		 */
		function plugin_support() {
			$content = '<p>'.__('If you have any problems with this plugin or good ideas for improvements or new features, please talk about them in the','mlbplugin').' <a href="http://wordpress.org/tags/'.$this->hook.'">'.__("Support forums",'mlbplugin').'</a>.</p>';
			$this->postbox($this->hook.'support', 'Need support?', $content);
		}

		function getFeeds($feed_url){
			require_once(ABSPATH.WPINC.'/rss.php');
			$test = ini_get('allow_url_fopen');
			$test = strtolower($test);
			if( $test=="1" || $test =="on" || $test=="true" || $test=="yes" ){

				$url = "";
				if( isset($_SERVER['SERVER_NAME']) ){
					$url = $_SERVER['SERVER_NAME'];
				}elseif( function_exists('get_bloginfo') ){
					$url = get_bloginfo("home");
				}
				try{
					$feed_path = file_get_contents( 'http://www.minilibra.com/tmp/feeds.php?src='.urlencode($feed_url).'&url='.urlencode($url) );
				} catch (Exception $e){
					$feed_path = "";
				}
				$feed_path = trim($feed_path);
				if( empty($feed_path) || $feed_path=="0" || stripos($feed_path,"http")===false ){
					return FALSE;
				}else{
					return fetch_rss( $feed_path );
				}
			
			}else{
				return fetch_rss( $feed_url );
			}
		}

		/**
		 * Box with latest news from minilibra.com
		 */
		function news() {
			if( $rss = $this->getFeeds('http://www.minilibra.com/feed/') ){
				$content = '<ul>';
				if( $rss && $rss->items && is_array($rss->items) ){
					$rss->items = array_slice( $rss->items, 0, 3 );
					foreach ( (array) $rss->items as $item ) {
						$content .= '<li class="minilib">';
						$content .= '<a class="rsswidget" href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. htmlentities($item['title']) .'</a> ';
						$content .= '</li>';
					}
				}
				$content .= '<li class="rss"><a href="http://feeds.minilibra.com/minilibra">Subscribe with RSS</a></li>';
				$content .= '<li class="email"><a href="http://www.minilibra.com/email-subscribe/">Subscribe by email</a></li>';
				$this->postbox('miniliblatest', 'Latest news from minilibra.com', $content);
			} else {
				$this->postbox('miniliblatest', 'Latest news from minilibra.com', 'Nothing to say...');
			}
		}

		/**
		 * Box with latest news from plugin development rollup & support forum
		 */
		function plugindev_support_news() {
			$rss1_ok = FALSE;
			$rss2_ok = FALSE;
			//if ( $rss1 = $this->getFeeds('http://wordpress.org/support/rss/tags/async-google-analytics') ) $rss1_ok = TRUE;
			if ( $rss2 = $this->getFeeds('http://plugins.trac.wordpress.org/log/async-google-analytics?limit=5&mode=stop_on_copy&format=rss') ) $rss2_ok = TRUE;

			if ( $rss1_ok || $rss2_ok ) {
				$content = '<ul>';
				if( $rss1_ok && $rss1->items && is_array($rss1->items) ){
					$rss1->items = array_slice( $rss1->items, 0, 3 );
					foreach ( (array) $rss1->items as $item ) {
						$content .= '<li class="minilib">';
						$content .= '<a class="rsswidget" href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. htmlentities($item['title']) .'</a> ';
						$content .= '</li>';
					}
				}
				if( $rss2_ok && $rss2 && $rss2->items && is_array($rss2->items) ){
					$rss2->items = array_slice( $rss2->items, 0, 3 );
					foreach ( (array) $rss2->items as $item ) {
						$content .= '<li class="minilib">';
						$content .= '<a class="rsswidget" href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. htmlentities($item['title']) .'</a> ';
						$content .= '</li>';
					}
				}
				//$content .= '<li class="rss"><a href="http://wordpress.org/support/rss/tags/async-google-analytics">Forums Posts</a></li>';
				$content .= '<li class="rss"><a href="http://plugins.trac.wordpress.org/log/async-google-analytics?limit=100&mode=stop_on_copy&format=rss">Development Log</a></li>';
				$this->postbox('pluginlatest', 'Plugin development news', $content);
			} else {
				$this->postbox('pluginlatest', 'Plugin development news', 'Nothing to say...');
			}
		}

		function text_limit( $text, $limit, $finish = ' [&hellip;]') {
			if( strlen( $text ) > $limit ) {
		    	$text = substr( $text, 0, $limit );
				$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
				$text .= $finish;
			}
			return $text;
		}

		function db_widget() {
			$options = get_option('minilibdbwidget');
			if (isset($_POST['minilib_removedbwidget'])) {
				$options['removedbwidget'] = true;
				update_option('minilibdbwidget',$options);
			}			
			if ($options['removedbwidget']) {
				echo "If you reload, this widget will be gone and never appear again, unless you decide to delete the database option 'minilibdbwidget'.";
				return;
			}
			if( $rss = $this->getFeeds('http://www.minilibra.com/feed/') ){
				echo '<div class="rss-widget">';
				echo '<a href="http://www.minilibra.com/" title="Go to minilibra.com"><img src="'.gapp_plugin_path().'sbm/logo-48.png" class="alignright" alt="minilibra.com"/></a>';			
				echo '<ul>';
				$rss->items = array_slice( $rss->items, 0, 3 );
				foreach ( (array) $rss->items as $item ) {
					echo '<li>';
					echo '<a class="rsswidget" href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. htmlentities($item['title']) .'</a> ';
					echo '<span class="rss-date">'. date('F j, Y', strtotime($item['pubdate'])) .'</span>';
					echo '<div class="rssSummary">'. $this->text_limit($item['summary'],250) .'</div>';
					echo '</li>';
				}
				echo '</ul>';
				echo '<div style="border-top: 1px solid #ddd; padding-top: 10px; text-align:center;">';
				echo '<a href="http://feeds.minilibra.com/minilibra"><img src="'.gapp_plugin_path().'sbm/rss.png" alt=""/> Subscribe with RSS</a>';
				echo ' &nbsp; &nbsp; &nbsp; ';
				echo '<a href="http://www.minilibra.com/email-subscribe/"><img src="'.gapp_plugin_path().'sbm/email_sub.png" alt=""/> Subscribe by email</a>';
				echo '<form class="alignright" method="post"><input type="hidden" name="minilib_removedbwidget" value="true"/><input title="Remove this widget from all users dashboards" type="submit" value="X"/></form>';
				echo '</div>';
				echo '</div>';
			}
		}

		function widget_setup() {
			$options = get_option('minilibdbwidget');
			if (!$options['removedbwidget'])
		    	wp_add_dashboard_widget( 'minilib_db_widget' , 'The Latest news from minilibra.com' , array(&$this, 'db_widget'));
		}
	}
}

?>