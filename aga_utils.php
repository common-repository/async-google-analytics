<?php

function b2a($val){
	if( empty($val) ){
		return array();
	}else{
		return $val;
	}
}
// Determine the location
function gapp_plugin_path() {
	return plugins_url('', __FILE__).'/';
}
// Determine did current user could be tracking / not
function gapp_user_can_tracking($opt_by_role=array(), $opt_by_id=array()){
	//if( !is_user_logged_in() || (empty($opt_by_role) && empty($opt_by_id)) ) return TRUE;
	if( empty($opt_by_role) && empty($opt_by_id) ) return TRUE;
	$current_user = wp_get_current_user();
	if ( /*!($current_user instanceof WP_User) ||*/ 0 == $current_user->ID ) {
		//Error or Not logged in.
		return TRUE;
	}
	if( !empty($opt_by_id) ){
		$arr = array();
		foreach( $opt_by_id as $k=>$v ) $arr[$v]=1;
		if( isset($arr[$current_user->user_email]) || isset($arr[$current_user->user_login]) || isset($arr[$current_user->ID]) ){
			unset($arr);
			return FALSE;
		}else{
			unset($arr);
		}
	}
	$result = TRUE;
	if( !empty($opt_by_role) ){
		$role_arr = array(
			array(0,0),
			array(1,1),
			array(2,4),
			array(5,7),
			array(8,10),
		);
		$x = $current_user->user_level;
		foreach( $opt_by_role as $k=>$v ){
			$role_id = $role_arr[$v-1];
			if( $x >= $role_id[0] && $x <= $role_id[1] ){
				$result = FALSE;
				break;
			}
		}
		unset($role_arr);
	}
	return $result;
}
/*
Utilities
*/
function print_gzipped_page($custom_header=array()) {

	$encoding = false;
    if( !headers_sent() && isset($_SERVER['HTTP_ACCEPT_ENCODING']) ){
		if( strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false ){
			$encoding = 'x-gzip';
		}elseif( strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') !== false ){
			$encoding = 'gzip';
		}
    }

    if( $encoding ){
        $contents = ob_get_contents();
        ob_end_clean();
		if( !empty($custom_header) ){
			foreach($custom_header as $k=>$v){
				header($k.': '.$v);
			}
		}
        header('Content-Encoding: '.$encoding);
        print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
        $size = strlen($contents);
        $contents = gzcompress($contents, 9);
        $contents = substr($contents, 0, $size);
        print($contents);
        exit();
    }else{
		if( !empty($custom_header) ){
			foreach($custom_header as $k=>$v){
				header($k.': '.$v);
			}
		}
        ob_end_flush();
        exit();
    }
}

function normalize_array_val($str='', $sep=','){
	$result = array();
	if( empty($str) ) return FALSE;
	$dummy = explode($sep, $str);
	foreach($dummy as $i){
		$i = trim($i);
		if( !empty($i) && is_numeric($i) && intval($i)>0 ){
			$i = intval($i);
			if( !in_array($i, $result) ) $result[] = $i;
		}elseif( !empty($i) && !in_array($i, $result) ){
			$result[] = $i;
		}
	}
	unset($dummy);
	if( empty($result) ){
		return FALSE;
	}else{
		return $result;
	}
}
function get_array_checkbox_el($name, $opt=array(), $selected=array()){
	$result = '';
	if( empty($name) || empty($opt) || !is_array($opt) ) return $result;

	$count = 0;
	foreach($opt as $k=>$item){
		$count++;
	}
	$count_per = round($count/3);
	$i = 0;
	$ended = false;
	foreach($opt as $k=>$item){
		if( $i == 0 ){
			$result .= '<div style="width:30%;float:left;margin-right:10px;">';
			$ended = false;
		}
		$i++;
		$is_selected = '';
		if( !empty($selected) && is_array($selected) && in_array($k, $selected) ) $is_selected = 'checked="checked" ';
		$result .= '<input type="checkbox" name="'.$name.'[]" value="'.$k.'" '.$is_selected.'/>&nbsp;'.$item.'<br />';
		if( $i>= $count_per ){
			$i = 0;
			$result .= '</div>';
			$ended = true;
		}
	}
	$result = trim($result);
	if( !empty($result) ){
		if( !$ended ) $result .= '</div>';
		$result .= '<div style="clear:both;float:none;"></div>';
	}
	return $result;
}
function array_checkbox_val($el=array(), $sep=','){
	$result = array();
	if( empty($el) || !is_array($el) ) return '';
	foreach($el as $i){
		$i = trim($i);
		if( !empty($i) && is_numeric($i) && intval($i)>0 ){
			$i = intval($i);
			if( !in_array($i, $result) ) $result[] = $i;
		}elseif( !empty($i) && !in_array($i, $result) ){
			$result[] = $i;
		}
	}
	if( empty($result) ){
		return '';
	}else{
		return implode($sep, $result);
	}
}

function get_my_current_ip(){
	$ipaddress = '';
	if (getenv(HTTP_X_FORWARDED_FOR)) {
        $ipaddress = getenv(HTTP_X_FORWARDED_FOR);
    } else {
        $ipaddress = getenv(REMOTE_ADDR);
    }
	return $ipaddress;
}

function normalize_single_uri($str=''){
	$result = '';
	$str = strtolower(trim($str));
	if( empty($str) ) return $result;
	$dummy = explode('?', $str);
	$str = trim($dummy[0]);
	$dummy = explode('#', $str);
	$str = trim($dummy[0]);
	unset($dummy);
	$result = $str;
	if( stripos($result, get_bloginfo('home')) === 0 ){
		$result = str_ireplace(get_bloginfo('home'), '', $result);
	}
	return trim($result);
}

function normalize_uri($arr=array()){
	$result = array();
	if( empty($arr) || !is_array($arr) ) return FALSE;
	foreach($arr as $s){
		$s = normalize_single_uri($s);
		if( !empty($s) && !in_array($s, $result) ) $result[] = $s;
	}
	if( empty($result) ){
		return FALSE;
	}else{
		return $result;
	}
}
function wildcard_uri($arr=array()){
	$result = array();
	if( empty($arr) || !is_array($arr) ) return FALSE;
	$wildcard = FALSE;
	foreach($arr as $arr_key=>$s){
		$dummy = explode('.', $s);
		if( stripos($s,'*')!==FALSE && count($dummy) == 4 ){
			$wildcard = TRUE;

			$new_ip = '';
			$done = FALSE;
			for($k=0;$k<count($dummy);$k++){
				if( $done ){
					break;
				}else{
					if( $k!=0 ) $new_ip .= '.';
					if( $dummy[$k] == '*' ){
						$done = TRUE;
						$suffix = '';
						if( $k<count($dummy)-1 ){
							for($z=$k+1;$z<count($dummy);$z++){
								$suffix .= '.'.$dummy[$z];
							}
						}
						for($j=0;$j<256;$j++){
							if( !in_array($new_ip.$j.$suffix, $result) ) $result[] = $new_ip.$j.$suffix;
						}
					}else{
						$new_ip .= $dummy[$k];
					}
				}
			}
			if( !$done && !empty($new_ip) && !in_array($new_ip, $result) ){
				$result[] = $new_ip;
			}
		}elseif( !empty($s) && !in_array($s, $result) && count($dummy) == 4 ){
			$result[] = $s;
		}
	}
	if( empty($result) ){
		return FALSE;
	}else{
		if( $wildcard ){
			return wildcard_uri($result);
		}else{
			return $result;
		}
	}
}

/**
 * If setAllowAnchor is set to true, GA ignores all links tagged "normally", so we redirect all "normally" tagged URL's
 * to one tagged with a hash. Needs some work as it also needs to do that when the first utm_ var is actually not the
 * first GET variable in the URL.
 */
function ga_utm_hashtag_redirect() {
	if (isset($_SERVER['REQUEST_URI'])) {
		if (strpos($_SERVER['REQUEST_URI'], "utm_") !== false) {
			if( isset($_SERVER['SERVER_NAME']) ){
				$url = aga_current_protocol().$_SERVER['SERVER_NAME'];
			}else{
				$url = get_bloginfo("home");
			}
			if ( strpos($_SERVER['REQUEST_URI'], "?utm_") !== false ) {
				$url .= str_replace("?utm_","#utm_",$_SERVER['REQUEST_URI']);
			} else if ( strpos($_SERVER['REQUEST_URI'], "&utm_") !== false ) {
				$url .= substr_replace($_SERVER['REQUEST_URI'], "#utm_", strpos($_SERVER['REQUEST_URI'], "&utm_"), 5);
			}
			wp_redirect($url, 301);
			exit;
		}
	}
}

function aga_current_protocol($echo=FALSE){
	$url = 'http://';
	if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != ""){
		$url = 'https://';
	}
	if($echo){ echo $url; }else{ return $url; }
}

function get_aga_source_async($echo=FALSE){
	$url = aga_current_protocol();
	if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != ""){
		$url .= 'ssl';
	}else{
		$url .= 'www';
	}
	$url .= '.google-analytics.com/'.GA_SRC_SCRIPT;
	if($echo){ echo $url; }else{ return $url; }
}

function get_aga_bookmarks_help(){?>
<!-- HELP US TO SHARE -->
<div class="shr-bookmarks shr-bookmarks-expand shr-bookmarks-center shr-bookmarks-bg-knowledge" style="clear:both;margin-left: 9px ! important; height: 32px; display: block;">
	<ul class="socials">
		<li class="shr-blogger"><a title="Blog this on Blogger" href="http://www.blogger.com/blog_this.pyra?t&amp;u=http%3A%2F%2Fwww.minilibra.com%2F&amp;n=WordPress+Expert+-+Professional+Web+Developer&amp;pli=1" target="_blank">&nbsp;</a></li>
		<li class="shr-comfeed"><a title="Subscribe to the comments for this post?" href="http://feeds.minilibra.com/minilibra" target="_blank">&nbsp;</a></li>
		<li class="shr-delicious"><a title="Share this on del.icio.us" href="http://delicious.com/post?url=http%3A%2F%2Fwww.minilibra.com%2F&amp;title=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-digg"><a title="Digg this!" href="http://digg.com/submit?phase=2&amp;url=http%3A%2F%2Fwww.minilibra.com%2F&amp;title=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-diigo"><a title="Post this on Diigo" href="http://www.diigo.com/post?url=http%3A%2F%2Fwww.minilibra.com%2F&amp;title=WordPress+Expert+-+Professional+Web+Developer&amp;desc=Free%20WordPress%20Installation%2C%20WordPress%20Consultation%2C%20Budget%20cutoff%20for%20your%20WordPress%20customization%2C%20WordPress%20themes%20adjustment%2C%20plugins%20customization%2C%20and%20much%20of%20WordPress%20solutions." target="_blank">&nbsp;</a></li>
		<li class="shr-facebook"><a href="http://www.facebook.com/share.php?v=4&amp;src=bm&amp;u=http%3A%2F%2Fwww.minilibra.com%2F&amp;t=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-friendfeed"><a title="Share this on FriendFeed" href="http://www.friendfeed.com/share?title=WordPress+Expert+-+Professional+Web+Developer&amp;link=http%3A%2F%2Fwww.minilibra.com%2F" target="_blank">&nbsp;</a></li>
		<li class="shr-gmail"><a title="Email this via Gmail" href="https://mail.google.com/mail/?ui=2&amp;view=cm&amp;fs=1&amp;tf=1&amp;su=WordPress+Expert+-+Professional+Web+Developer&amp;body=Link: http://www.minilibra.com/ (sent via shareaholic)%0D%0A%0D%0A----%0D%0A Free%20WordPress%20Installation%2C%20WordPress%20Consultation%2C%20Budget%20cutoff%20for%20your%20WordPress%20customization%2C%20WordPress%20themes%20adjustment%2C%20plugins%20customization%2C%20and%20much%20of%20WordPress%20solutions." target="_blank">&nbsp;</a></li>
		<li class="shr-googlebookmarks"><a title="Add this to Google Bookmarks" href="http://www.google.com/bookmarks/mark?op=add&amp;bkmk=http%3A%2F%2Fwww.minilibra.com%2F&amp;title=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-googlebuzz"><a title="Post on Google Buzz" href="http://www.google.com/buzz/post?url=http%3A%2F%2Fwww.minilibra.com%2F&amp;imageurl=" target="_blank">&nbsp;</a></li>
		<li class="shr-googlereader"><a title="Add this to Google Reader" href="http://www.google.com/reader/link?url=http%3A%2F%2Fwww.minilibra.com%2F&amp;title=WordPress+Expert+-+Professional+Web+Developer&amp;srcUrl=http%3A%2F%2Fwww.minilibra.com%2F&amp;srcTitle=WordPress+Expert+-+Professional+Web+Developer&amp;snippet=Free%20WordPress%20Installation%2C%20WordPress%20Consultation%2C%20Budget%20cutoff%20for%20your%20WordPress%20customization%2C%20WordPress%20themes%20adjustment%2C%20plugins%20customization%2C%20and%20much%20of%20WordPress%20solutions." target="_blank">&nbsp;</a></li>
		<li class="shr-hotmail"><a title="Email this via Hotmail" href="http://mail.live.com/?rru=compose?subject=WordPress+Expert+-+Professional+Web+Developer&amp;body=Link: http://www.minilibra.com/ (sent via shareaholic)%0D%0A%0D%0A----%0D%0A Free%20WordPress%20Installation%2C%20WordPress%20Consultation%2C%20Budget%20cutoff%20for%20your%20WordPress%20customization%2C%20WordPress%20themes%20adjustment%2C%20plugins%20customization%2C%20and%20much%20of%20WordPress%20solutions." target="_blank">&nbsp;</a></li>
		<li class="shr-linkedin"><a title="Share this on LinkedIn" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=http%3A%2F%2Fwww.minilibra.com%2F&amp;title=WordPress+Expert+-+Professional+Web+Developer&amp;summary=Free%20WordPress%20Installation%2C%20WordPress%20Consultation%2C%20Budget%20cutoff%20for%20your%20WordPress%20customization%2C%20WordPress%20themes%20adjustment%2C%20plugins%20customization%2C%20and%20much%20of%20WordPress%20solutions.&amp;source=WordPress Expert" target="_blank">&nbsp;</a></li>
		<li class="shr-mail"><a title="Email this to a friend?" href="mailto:?subject=%22Asynchronous%20Google%20Analytics%20for%20WordPress%22&amp;body=Link: http://www.minilibra.com/ (sent via shareaholic)%0D%0A%0D%0A----%0D%0A Free%20WordPress%20Installation%2C%20WordPress%20Consultation%2C%20Budget%20cutoff%20for%20your%20WordPress%20customization%2C%20WordPress%20themes%20adjustment%2C%20plugins%20customization%2C%20and%20much%20of%20WordPress%20solutions." target="_blank">&nbsp;</a></li>
		<li class="shr-mixx"><a title="Share this on Mixx" href="http://www.mixx.com/submit?page_url=http%3A%2F%2Fwww.minilibra.com%2F&amp;title=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-myspace"><a title="Post this to MySpace" href="http://www.myspace.com/Modules/PostTo/Pages/?u=http%3A%2F%2Fwww.minilibra.com%2F&amp;t=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-newsvine"><a title="Seed this on Newsvine" href="http://www.newsvine.com/_tools/seed&amp;save?u=http%3A%2F%2Fwww.minilibra.com%2F&amp;h=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-printfriendly"><a title="Send this page to Print Friendly" href="http://www.printfriendly.com/print?url=http%3A%2F%2Fwww.minilibra.com%2F" target="_blank">&nbsp;</a></li>
		<li class="shr-reddit"><a title="Share this on Reddit" href="http://reddit.com/submit?url=http%3A%2F%2Fwww.minilibra.com%2F&amp;title=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-stumbleupon"><a title="Stumble upon something good? Share it on StumbleUpon" href="http://www.stumbleupon.com/submit?url=http%3A%2F%2Fwww.minilibra.com%2F&amp;title=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-techmeme"><a title="Tip this to TechMeme" href="http://twitter.com/home/?status=Tip+@Techmeme+http://www.minilibra.com/+&quot;WordPress+Expert+-+Professional+Web+Developer&quot;&amp;source=shareaholic" target="_blank">&nbsp;</a></li>
		<li class="shr-technorati"><a title="Share this on Technorati" href="http://technorati.com/faves?add=http%3A%2F%2Fwww.minilibra.com%2F" target="_blank">&nbsp;</a></li>
		<li class="shr-tumblr"><a title="Share this on Tumblr" href="http://www.tumblr.com/share?v=3&amp;u=http%3A%2F%2Fwww.minilibra.com%2Fwordpress%2Fplugins%2Fanalytics.html&amp;t=WordPress+Expert+-+Professional+Web+Developer" target="_blank">&nbsp;</a></li>
		<li class="shr-twitter"><a title="Tweet This!" href="http://twitter.com/home?status=WordPress+Expert+-+Professional+Web+Developer+-+http://b2l.me/ajnhzk&amp;source=shareaholic" target="_blank">&nbsp;</a></li>
		<li class="shr-yahoobuzz"><a title="Buzz up!" href="http://buzz.yahoo.com/submit/?submitUrl=http%3A%2F%2Fwww.minilibra.com%2F&amp;submitHeadline=WordPress+Expert+-+Professional+Web+Developer&amp;submitSummary=Free%20WordPress%20Installation%2C%20WordPress%20Consultation%2C%20Budget%20cutoff%20for%20your%20WordPress%20customization%2C%20WordPress%20themes%20adjustment%2C%20plugins%20customization%2C%20and%20much%20of%20WordPress%20solutions.&amp;submitCategory=science&amp;submitAssetType=text" target="_blank">&nbsp;</a></li>
		<li class="shr-yahoomail"><a title="Email this via Yahoo! Mail" href="http://compose.mail.yahoo.com/?Subject=WordPress+Expert+-+Professional+Web+Developer&amp;body=Link: http://www.minilibra.com/ (sent via shareaholic)%0D%0A%0D%0A----%0D%0A Free%20WordPress%20Installation%2C%20WordPress%20Consultation%2C%20Budget%20cutoff%20for%20your%20WordPress%20customization%2C%20WordPress%20themes%20adjustment%2C%20plugins%20customization%2C%20and%20much%20of%20WordPress%20solutions." target="_blank">&nbsp;</a></li>
	</ul>
	<div style="clear: both;"></div>
</div>
<!-- HELP US TO SHARE -->
<?php
}
?>