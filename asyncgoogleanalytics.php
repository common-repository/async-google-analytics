<?php
/*
Plugin Name: Asynchronous Google Analytics for WordPress
Plugin URI: http://www.minilibra.com/wordpress/plugins/analytics.html#utm_source=wordpress&utm_medium=plugin&utm_campaign=async-google-analytics
Description: SUPPORT MULTI SITE NOW!! This plugin makes it simple to add Asynchronous Google Analytics with extra search engines and automatic clickout and download tracking to your WordPress blog.
Author: Bambang Sugiarto
Version: 3.0.7
Requires at least: 2.7
Author URI: http://www.minilibra.com/
License: GPL

*/
session_start();
global $aga_admin, $aga_filter, $agaf_link_data;

//Activated only for Google Analytic debugging purposed
//define('GA_SRC_SCRIPT', 'u/ga_debug.js');

if( isset($_GET['customfilter']) && !empty($_GET['customfilter']) && is_numeric($_GET['customfilter']) && intval($_GET['customfilter'])==1 ){

	include_once('../../../wp-load.php');
	if( !defined('GA_SRC_SCRIPT') ) define('GA_SRC_SCRIPT', 'ga.js');
	if( !defined('AGA_PLUGIN_FILENAME') ) define('AGA_PLUGIN_FILENAME', basename(__FILE__));

	require_once('aga_utils.php');
	require_once('aga_filter.php');

	$options = AGA_Filter::get_options();
	$allow = gapp_user_can_tracking( normalize_array_val($options['exclude_userrole']), normalize_array_val($options['exclude_userid']) );

	//Filter Tracking
	if( $allow && $options['customfilter'] ){

			$do_refresh_aga_session = FALSE;
			if( isset($_SESSION['aga_last_update']) ){
				if( $_SESSION['aga_last_update']<=time() ){
					$_SESSION['aga_last_update'] = time()+(900); //15 minutes
					$do_refresh_aga_session = TRUE;
				}
			}else{
				$_SESSION['aga_last_update'] = time()+(900); //15 minutes
				$do_refresh_aga_session = TRUE;
			}
			if( $do_refresh_aga_session ){
				if( isset($_SESSION['aga_excludeips']) ) unset($_SESSION['aga_excludeips']);
				if( isset($_SESSION['aga_excludecategories']) ) unset($_SESSION['aga_excludecategories']);
				if( isset($_SESSION['aga_excludetags']) ) unset($_SESSION['aga_excludetags']);
				if( isset($_SESSION['aga_excludepages']) ) unset($_SESSION['aga_excludepages']);
				if( isset($_SESSION['aga_excludeposts']) ) unset($_SESSION['aga_excludeposts']);
				if( isset($_SESSION['aga_excludeuri']) ) unset($_SESSION['aga_excludeuri']);
			}

		$val=get_my_current_ip();
		$arr=array();
		if( $val != '' ){
			if( isset( $_SESSION['aga_excludeips'] ) ){
				$arr = array_merge(array(), b2a($_SESSION['aga_excludeips']));
				if( !empty($arr) && in_array($val, $arr) ){
					$allow = FALSE;
					unset($arr);
				}
			}else{

				$arr=normalize_array_val($options['excludeips'], "\n");
				if( !empty($arr) ){
					if( is_array($arr) ){
						$ip_list = b2a(wildcard_uri($arr));
					}else{
						$ip_list = b2a(wildcard_uri(array($arr)));
					}
					$_SESSION['aga_excludeips'] = $ip_list;
					$arr = array_merge(array(), $ip_list);
					unset($ip_list);

					if( in_array($val, $arr) ){
						$allow = FALSE;
						unset($arr);
					}
				}

			}
		}

		if( $allow && isset($_GET['v']) && !empty($_GET['v']) && isset($_GET['t']) ){

			switch( $_REQUEST['t'] ){
				case 'cat':
					$x = 0;
					if( is_numeric($_GET['v']) ) $x = intval($_GET['v']);
					if( $x>0 ){
						if( isset( $_SESSION['aga_excludecategories'] ) ){
							$arr = array_merge(array(), b2a($_SESSION['aga_excludecategories']));
						}else{
							$arr=normalize_array_val($options['excludecategories']);
							$_SESSION['aga_excludecategories'] = $arr;
						}
						if( !empty($arr) && is_array($arr) && in_array($x, $arr) ) $allow = FALSE;
						unset($arr);
					}
					break;
				case 'tag':
					$x = 0;
					if( is_numeric($_GET['v']) ) $x = intval($_GET['v']);
					if( $x>0 ){
						if( isset( $_SESSION['aga_excludetags'] ) ){
							$arr = array_merge(array(), b2a($_SESSION['aga_excludetags']));
						}else{
							$arr=normalize_array_val($options['excludetags']);
							$_SESSION['aga_excludetags'] = $arr;
						}
						if( !empty($arr) && is_array($arr) && in_array($x, $arr) ) $allow = FALSE;
						unset($arr);
					}
					break;
				case 'page':
					$x = 0;
					if( is_numeric($_GET['v']) ) $x = intval($_GET['v']);
					if( $x>0 ){
						if( isset( $_SESSION['aga_excludepages'] ) ){
							$arr = array_merge(array(), b2a($_SESSION['aga_excludepages']));
						}else{
							$arr=normalize_array_val($options['excludepages']);
							$_SESSION['aga_excludepages'] = $arr;
						}
						if( !empty($arr) && is_array($arr) && in_array($x, $arr) ) $allow = FALSE;
						unset($arr);
					}
					break;
				case 'post':
					$x = 0;
					if( is_numeric($_GET['v']) ) $x = intval($_GET['v']);
					if( $x>0 ){
						if( isset( $_SESSION['aga_excludeposts'] ) ){
							$arr = array_merge(array(), b2a($_SESSION['aga_excludeposts']));
						}else{
							$arr=normalize_array_val($options['excludeposts']);
							$_SESSION['aga_excludeposts'] = $arr;
						}
						if( !empty($arr) && is_array($arr) && in_array($x, $arr) ) $allow = FALSE;
						unset($arr);
					}
					break;
				default:
					if( isset( $_SESSION['aga_excludeuri'] ) ){
						$arr = array_merge(array(), b2a($_SESSION['aga_excludeuri']));
					}else{
						$arr=normalize_uri(normalize_array_val($options['excludeuri'], "\n"));
						$_SESSION['aga_excludeuri'] = $arr;
					}
					if( !empty($arr) && is_array($arr) && in_array($_GET['v'], $arr) ) $allow = FALSE;
					unset($arr);
					break;
			}
		}
	}

	$custom_header = array(
		'Content-type'=>'application/x-javascript',
		'Pragma'=>'no-cache',
		'Cache-Control'=>'no-store, no-cache, proxy-revalidate, must-revalidate',
		'Expires'=>'Mon, 26 Jul 1997 05:00:00 GMT'
	);
	foreach($custom_header as $k=>$v){ header($k.': '.$v, TRUE); }
	die("AGA_CUSTOM_FILTER=".($allow ? "true":"false").";");

}else{
	//Include Libs
	if( !defined('GA_SRC_SCRIPT') ) define('GA_SRC_SCRIPT', 'ga.js');
	if( !defined('AGA_PLUGIN_FILENAME') ) define('AGA_PLUGIN_FILENAME', basename(__FILE__));
	require_once('aga_utils.php');
	require_once('aga_filter.php');
	require_once('aga_admin.php');
}

$options = AGA_Filter::get_options();
if ( empty($options) || !is_array($options) ){
	AGA_Filter::set_default_options();
	$options = AGA_Filter::get_options();
}

function setup_agaf_link_data(){
	global $agaf_link_data;

	if( !isset($agaf_link_data) ) $agaf_link_data = array();
	if( !isset($agaf_link_data['count']) ) $agaf_link_data['count']=0;
	if( !isset($agaf_link_data['data']) ) $agaf_link_data['data']=array();
	if( !isset($agaf_link_data['external']) ){
		$options = AGA_Filter::get_options();
		$origin = AGA_Filter::ga_get_domain(get_bloginfo("home"));
		if ( isset($options['domain']) && $options['domain'] != "" ){
			$s = trim($options['domain']);
			if ($s!="" && $s{0} == ".") $s = trim(substr($s, 1));
			if( $s!="" ) $origin['domain'] = $s;
		}

		$agaf_link_data['external']=array(
			'track' => (isset($options['trackoutbound']) && !empty($options['trackoutbound']) && $options['trackoutbound'] && isset($options['externalprefix']) && !empty($options['externalprefix'])),
			'domain' => $origin['domain'],
			'prefix' => $options['externalprefix'],
			'fulltrack'=>($options['domainorurl']=='url'),
		);
	}
	return $agaf_link_data;
}
function push_agaf_link_data($value='', $name_prefix='aga_'){
	global $agaf_link_data;
	$agaf_link_data = setup_agaf_link_data();
	$old_count = $agaf_link_data['count'];
	$agaf_link_data['count']=$agaf_link_data['count']+1;

	array_push($agaf_link_data['data'], array('name'=>$name_prefix.$old_count, 'param'=>$value) );
	return $name_prefix.$old_count;
}
function pull_agaf_link_data(){
	global $agaf_link_data;
	$agaf_link_data = setup_agaf_link_data(); ?>
<script type="text/javascript" charset="<?php bloginfo('charset'); ?>">/*<![CDATA[*/var AGA_LINKS_DATA={count:<?php echo (isset($agaf_link_data['count']) && !empty($agaf_link_data['count']) ? $agaf_link_data['count']:'0'); ?>,<?php if( isset($agaf_link_data['external']) ){ echo 'external:{track:'.($agaf_link_data['external']['track']?'true':'false').', domain:\''.$agaf_link_data['external']['domain'].'\', prefix:\''.$agaf_link_data['external']['prefix'].'\', fulltrack:'.($agaf_link_data['external']['fulltrack']?'true':'false').'},'; }?>data:[<?php foreach($agaf_link_data['data'] as $k=>$link){ echo '{name:"'.$link['name'].'",param:'.$link['param'].'},'; }?>]};/*]]>*/</script><?php
}

if( function_exists('update_option') ){
	if( !isset($options['allow_linkback']) ){
		$options['allow_linkback'] = true;
		update_option('AsyncGoogleAnalyticsPP',$options);
	}
	//version 2.0.3
	if( !isset($options['exclude_userrole']) || !isset($options['exclude_userid']) ){
		if( isset($options['admintracking']) ) unset($options['admintracking']);
		$options['exclude_userrole'] = '1,2,3,4,5';
		$options['exclude_userid'] = '';
		update_option('AsyncGoogleAnalyticsPP',$options);
	}
}

if( function_exists('add_filter') ){
	if (isset($options['trackoutbound']) && !empty($options['trackoutbound']) && $options['trackoutbound']) {
		add_filter('the_content', array('AGA_Filter','the_content'), 99);
		add_filter('the_excerpt', array('AGA_Filter','the_content'), 99);
		add_filter('comment_text', array('AGA_Filter','comment_text'), 99);
	}
	add_filter('the_excerpt_rss', array('AGA_Filter','the_excerpt_rss'), 99);
	if ($options['rsslinktagging']) add_filter ( 'the_permalink_rss', array('AGA_Filter','rsslinktagger'), 99 );
}

if( function_exists('add_action') ){
	if ($options['allowanchor']) add_action('init','ga_utm_hashtag_redirect',1);
	if ($options['trackadsense']) add_action('wp_head', array('AGA_Filter','spool_adsense'),10);
	if ($options['position'] == 'footer' || $options['position'] == ""){
		if( $options["useasync"] ){
			add_action('wp_footer', array('AGA_Filter','spool_analytics_async_foot'),99);
			add_action('wp_head', array('AGA_Filter','spool_analytics_async_head'),1);
		}else{
			add_action('wp_footer', array('AGA_Filter','spool_analytics'));
		}
	}else{
		add_action('wp_head', array('AGA_Filter','spool_analytics'),20);
	}
	if ($options['trackregistration']) add_action('login_head', array('AGA_Filter','spool_analytics'),20);
	add_action('wp_head', array('AGA_Filter','XFN_Head'), 1);
	if( $options['allow_linkback'] ) add_action('wp_footer', array('AGA_Filter','credit_link'), 999);
	add_action('init', array('AGA_Filter','init_aga_script_utils'),1);
	add_action('wp_footer', 'pull_agaf_link_data', 1);
}
?>