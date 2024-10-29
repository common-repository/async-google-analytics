<?php

/*
 * Admin User Interface
 */

if ( ! class_exists( 'AGA_Admin' ) ) {

	require_once('aga_tools.php');

	class AGA_Admin extends Minilib_Plugin_Admin {

		var $hook 		= 'async-google-analytics';
		var $filename	= 'async-google-analytics/asyncgoogleanalytics.php';
		var $longname	= 'Async Google Analytics Configuration';
		var $shortname	= 'Async Google Analytics';
		var $ozhicon	= 'chart_curve.png';
		var $optionname = 'AsyncGoogleAnalyticsPP';
		var $homepage	= 'http://www.minilibra.com/wordpress/plugins/analytics.html';


		function AGA_Admin() {
			add_action( 'admin_menu', array(&$this, 'register_settings_page') );
			add_filter( 'plugin_action_links', array(&$this, 'add_action_link'), 10, 2 );
			add_filter( 'ozh_adminmenu_icon', array(&$this, 'add_ozh_adminmenu_icon' ) );

			add_action('admin_print_scripts', array(&$this,'config_page_scripts'));
			add_action('admin_print_styles', array(&$this,'config_page_styles'));

			add_action('wp_dashboard_setup', array(&$this,'widget_setup'));
			add_action('admin_head', array(&$this,'config_page_head'));
		}

		function config_page_head() {
			if (isset($_GET['page']) && $_GET['page'] == $this->hook) {
				wp_enqueue_script('jquery');
				$options = AGA_Filter::get_options();
				$bloglist_options = array();
				if( $options['multisite'] && $options['ismainsite'] ){
					$bloglist_options = AGA_Filter::get_blog_list();
				}
				unset($options);
			?>
				 <link media="all" type="text/css" href="<?php echo gapp_plugin_path(); ?>sbm/spritegen/shr-custom-sprite.css" id="sexy-bookmarks-css" rel="stylesheet" /><style type="text/css">div.shr-bookmarks-bg-knowledge{background:url('<?php echo gapp_plugin_path(); ?>sbm//helpus.png') no-repeat top left !important;}#wpwrap{background:url(<?php echo gapp_plugin_path(); ?>sbm/batik.png) #fff4e6 !important;}#wpbody-content{background:url(<?php echo gapp_plugin_path(); ?>sbm/logo-ic-50.png) no-repeat top right !important;}</style>
				 <script type="text/javascript" charset="<?php bloginfo('charset'); ?>">/*<![CDATA[*/
					var aga_blog_listed = new Array();
					jQuery(document).ready(function($){
						$('#explanation td').css("display","none");
						$('#trackoutbound').change(function(){
							if (($('#trackoutbound').attr('checked')) == true){
								$('#outboundgasettings').slideDown("normal",function(){
									$(this).css("display","block");
								});
							} else {
								$('#outboundgasettings').slideUp("normal",function(){
									$(this).css("display","none");
								});
							}
						}).change();
						$('#customfilter').change(function(){
							if (($('#customfilter').attr('checked')) == true){
								$('#customgafilter').slideDown("normal",function(){
									$(this).css("display","block");
								});
							} else {
								$('#customgafilter').slideUp("normal",function(){
									$(this).css("display","none");
								});
							}
						}).change();
						$('#advancedsettings').change(function(){
							if (($('#advancedsettings').attr('checked')) == true){
								$('#advancedgasettings').slideDown("normal",function(){
									$(this).css("display","block");
								});
							} else {
								$('#advancedgasettings').slideUp("normal",function(){
									$(this).css("display","none");
								});
							}
						}).change();
						$('#allow_linkback').change(function(){
							if (($('#allow_linkback').attr('checked')) == true){
								$('#credit_desc2').fadeOut('fast', function(){
									$('#credit_desc1').fadeIn('normal');
								});
								$('#credit_text').slideUp("normal",function(){
									$(this).css("display","none");
								});
							} else {
								$('#credit_desc1').fadeOut('fast', function(){
									$('#credit_desc2').fadeIn('normal');
								});
								$('#credit_text').slideDown("normal",function(){
									$(this).css("display","block");
								});
							}
						}).change();
						
						$('#explain').click(function(){
							if (($('#explanation').css("display")) == "block")  {
								$('#explanation').fadeOut("normal",function(){
									$(this).css("display","none");
								});
							} else {
								$('#explanation').fadeIn("normal",function(){
									$(this).css("display","block");
								});
							}
						});
						$('#explain_tracksubdomains').click(function(){
							if (($('#explanation_tracksubdomains').css("display")) == "block")  {
								$('#explanation_tracksubdomains').fadeOut("normal",function(){
									$(this).css("display","none");
								});
							} else {
								$('#explanation_tracksubdomains').fadeOut("fadeIn",function(){
									$(this).css("display","block");
								});
							}
						});

						<?php
						if( !empty($bloglist_options) ){?>
							
							<?php foreach($bloglist_options as $k=>$v) echo "window.aga_blog_listed.push(".$k.");"; ?>
							
							for(i=0;i<window.aga_blog_listed.length;i++){
								$("#aga_config_"+window.aga_blog_listed[i]+" .postbox").css("background-color","#FDFDFD");

								$('#trackoutbound_'+window.aga_blog_listed[i]).change(function(){
									if( $("#aga_config_page_id").val() > 1 ){
										if (($('#trackoutbound_'+$("#aga_config_page_id").val()).attr('checked')) == true){
											$('#outboundgasettings_'+$("#aga_config_page_id").val()).slideDown("normal",function(){
												$(this).css("display","block");
											});
										} else {
											$('#outboundgasettings_'+$("#aga_config_page_id").val()).slideUp("normal",function(){
												$(this).css("display","none");
											});
										}
									}
								}).change();
								$('#customfilter_'+window.aga_blog_listed[i]).change(function(){
									if( $("#aga_config_page_id").val() > 1 ){
										if (($('#customfilter_'+$("#aga_config_page_id").val()).attr('checked')) == true){
											$('#customgafilter_'+$("#aga_config_page_id").val()).slideDown("normal",function(){
												$(this).css("display","block");
											});
										} else {
											$('#customgafilter_'+$("#aga_config_page_id").val()).slideUp("normal",function(){
												$(this).css("display","none");
											});
										}
									}
								}).change();
								$('#advancedsettings_'+window.aga_blog_listed[i]).change(function(){
									if( $("#aga_config_page_id").val() > 1 ){
										if (($('#advancedsettings_'+$("#aga_config_page_id").val()).attr('checked')) == true){
											$('#advancedgasettings_'+$("#aga_config_page_id").val()).slideDown("normal",function(){
												$(this).css("display","block");
											});
										} else {
											$('#advancedgasettings_'+$("#aga_config_page_id").val()).slideDown("normal",function(){
												$(this).css("display","none");
											});
										}
									}
								}).change();
							}

							$("#aga_config_page_id").change(function(){
								
								if( $("#aga_config_page_id").val()>1 ){
									if (($('#aga_global_config').css("display")) == "block"){
										$('#aga_global_config').fadeOut('normal', function(){
											$(this).css("display","none");

											$('#aga_config_'+$("#aga_config_page_id").val()).fadeIn(1000, function(){
												$(this).css("display","block");
											});
										});
									}else{
										for(i=0;i<window.aga_blog_listed.length;i++){
											if (($("#aga_config_"+window.aga_blog_listed[i]).css("display")) == "block"){
												$("#aga_config_"+window.aga_blog_listed[i]).fadeOut('normal',function(){
													$(this).css("display","none");

													$('#aga_config_'+$("#aga_config_page_id").val()).fadeIn(1000, function(){
														$(this).css("display","block");
													});
												});
												break;
											}
										}
									}
								}else{
									found = false;
									for(i=0;i<window.aga_blog_listed.length;i++){
										if (($("#aga_config_"+window.aga_blog_listed[i]).css("display")) == "block"){
											found = true;
											$("#aga_config_"+window.aga_blog_listed[i]).fadeOut('normal',function(){
												$(this).css("display","none");
												
												$('#aga_global_config').fadeIn(1000, function(){
													$(this).css("display","block");
												});
											});
											break;
										}
									}
									if( !found ){
										$('#aga_global_config').fadeIn(1000, function(){
											$(this).css("display","block");
										});
									}
								}
							}).change();
						<?php unset($bloglist_options);
						}?>
						
					});
				 /*]]>*/</script>
				 <script src="<?php echo gapp_plugin_path(); ?>sbm/js/sexy-bookmarks-public.js" type="text/javascript"></script>
			<?php
			}
		}

		function checkbox($id){
			$options = AGA_Filter::get_options();
			return '<input type="checkbox" id="'.$id.'" name="'.$id.'"'. checked($options[$id],true,false).'/>';
		}

		function textinput($id) {
			$options = AGA_Filter::get_options();
			return '<input type="text" id="'.$id.'" name="'.$id.'" size="30" value="'.$options[$id].'"/>';
		}

		function opts_html_config_push($all_opt=array(), $group_name='', $opt_val=null){
			if( !isset($all_opt) ) $all_opt=array();
			if( !empty($opt_val) ){
				if( $group_name=='' || (is_numeric($group_name) && $group_name<0) ) $group_name = 0;
				if( !isset($all_opt[$group_name]) ) $all_opt[$group_name] = array();
				array_push($all_opt[$group_name], $opt_val);
			}
			return $all_opt;
		}

		function config_page() {
			$options = AGA_Filter::get_options();
			$bloglist_options = array();
			if( $options['multisite'] ){
				if( $options['ismainsite'] ){
					$bloglist_options = AGA_Filter::get_blog_list();
				}else{
					global $current_blog, $current_site;
					$id = 0;
					if( isset($current_blog) && !empty($current_blog->blog_id) ){
						$id=intval($current_blog->blog_id);
					}elseif( isset($current_site) && !empty($current_site->blog_id) ){
						$id=intval($current_site->blog_id);
					}
					if( $id>1 ){
						$bloglist_options[$id] = array(
							'info'=>get_blog_details($id),
							'options'=>$options
						);
					}
				}
			}
			
			if ( (isset($_POST['reset']) && $_POST['reset'] == "true") || !is_array($options) ) {
				AGA_Filter::set_default_options();
				echo "<div class=\"updated\"><p>Async Google Analytics settings reset to default.</p></div>\n";
			}

			if ( isset($_POST['submit']) ) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the Async Google Analytics for WordPress options.'));
				check_admin_referer('analyticspp-config');

				$opt_names = array('uastring', 'dlextensions', 'dlprefix', 'externalprefix', 'artprefix', 'comprefix', 'domainorurl','position','domain', 'excludeposts', 'excludeuri', 'excludeips', 'exclude_userid');
				if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
					$opt_names_blogs = array('uastring', 'dlextensions', 'dlprefix', 'externalprefix', 'artprefix', 'comprefix', 'domainorurl','position','domain','excludeips','exclude_userid');
					foreach($bloglist_options as $k=>$v){
						$dummy = array_merge(array(), $v['options']);
						foreach ($opt_names_blogs as $option_name){
							if ( isset($_POST[$option_name."_".$k]) )
								$dummy[$option_name] = $_POST[$option_name."_".$k];
						}
						$bloglist_options[$k]['options'] = array_merge($v['options'], $dummy);
					}
				}
				foreach ($opt_names as $option_name) {
					if( ($option_name == 'uastring'||$option_name == 'domain') && $options['multisite'] && !$options['ismainsite'] ) continue;
					if (isset($_POST[$option_name]))
						$options[$option_name] = $_POST[$option_name];
					else
						$options[$option_name] = '';
				}

				$opt_names = array('extrase', 'imagese', 'trackoutbound', 'trackloggedin', 'trackadsense', 'userv2', 'allowanchor', 'rsslinktagging', 'advancedsettings', 'trackregistration', 'useasync','customfilter','allow_linkback', 'tracksubdomains');
				if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
					$opt_names_blogs = array('extrase', 'imagese', 'trackoutbound', 'trackloggedin', 'trackadsense', 'userv2', 'allowanchor', 'rsslinktagging', 'advancedsettings', 'trackregistration', 'useasync','customfilter','allow_linkback');
					foreach($bloglist_options as $k=>$v){
						$dummy = array_merge(array(), $v['options']);
						foreach ($opt_names_blogs as $option_name){
							if( isset($_POST[$option_name."_".$k]) || $option_name == 'useasync' )
								$dummy[$option_name] = true;
						}
						$bloglist_options[$k]['options'] = array_merge($v['options'], $dummy);
					}
				}
				foreach ($opt_names as $option_name) {
					if ( isset($_POST[$option_name]) ){
						$options[$option_name] = true;
					}else{
						if( $option_name == 'useasync' ){
							$options[$option_name] = true;
						}else{
							$options[$option_name] = false;
						}
					}
				}

				$opt_names = array('excludecategories', 'excludetags', 'excludepages', 'exclude_userrole');
				if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
					$opt_names_blogs = array('exclude_userrole');
					foreach($bloglist_options as $k=>$v){
						$dummy = array_merge(array(), $v['options']);
						foreach ($opt_names_blogs as $option_name){
							if ( isset($_POST[$option_name."_".$k]) )
								$dummy[$option_name] = array_checkbox_val($_POST[$option_name."_".$k]);
						}
						$bloglist_options[$k]['options'] = array_merge($v['options'], $dummy);
					}
				}
				foreach ($opt_names as $option_name) {
					if (isset($_POST[$option_name])){
						$options[$option_name] = array_checkbox_val($_POST[$option_name]);
					}else{
						$options[$option_name] = '';
					}
				}

				update_option('AsyncGoogleAnalyticsPP', $options);
				if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
					foreach($bloglist_options as $k=>$v){
						update_blog_option($k, 'AsyncGoogleAnalyticsPP', $v['options'], FALSE);
					}
				}
				if( isset($_SESSION['aga_last_update']) ) unset($_SESSION['aga_last_update']);
				echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Async Google Analytics settings updated.</p></div>\n";
				echo "<script type=\"text/javascript\" charset=\"". get_bloginfo('charset'). "\">/*<![CDATA[*/setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);/*]]>*/</script>";
			}
			if( !isset($options['useasync']) ) $options['useasync'] = true;

			?>
			<div class="wrap">
				<a href="http://www.minilibra.com/" target="_blank"><div id="minilibra-icon" style="background: url(<?php echo gapp_plugin_path(); ?>sbm/logo-32.png) no-repeat;" class="icon32"><br /></div></a>
				<h2>Async Google Analytics for WordPress <?php echo ($options['multisite']?"Multisite ":"");?>Configuration</h2>
				<?php get_aga_bookmarks_help(); ?>

				<div class="postbox-container" style="width:70%;">
					<div class="metabox-holder">
						<div class="meta-box-sortables">
							<form action="" method="post" id="analytics-conf">								
								<?php
									wp_nonce_field('analyticspp-config');
									$field_readonly = '';
									if( $options['multisite'] && !$options['ismainsite'] && $options['tracksubdomains'] ){
										$field_readonly = 'readonly="readonly" style="background-color:#eee;" ';
									}
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										
										$html_select_config = '<strong style="margin-left: 10px; padding-bottom: 15px; display: inline-block;"><label for="aga_config_page_id">Async Google Analytics Multisite Configuration for:</label></strong> <select id="aga_config_page_id" name="aga_config_page_id" style="font-weight:bold;">';
										$html_select_config .= '<option value="0"'.( isset($_POST["aga_config_page_id"]) && intval($_POST["aga_config_page_id"])>1 && isset($bloglist_options[intval($_POST["aga_config_page_id"])]) ? '':' selected' ).'>Global Configuration</option>';

										foreach($bloglist_options as $k=>$v){

											$html_select_config .= '<option value="'.$k.'"'.( isset($_POST["aga_config_page_id"]) && intval($_POST["aga_config_page_id"])>1 && intval($_POST["aga_config_page_id"])==$k ? ' selected':'').'>'.$v['info']->blogname.' ('.$v['info']->domain.')</option>';

											$v['html_config']=$this->opts_html_config_push($v['html_config']);
											$bloglist_options[$k]=$v;
										}
										$html_select_config .= '</select>';

										echo $html_select_config.'<br />';
										echo '<div id="aga_global_config">';
										echo '<div class="aga-info-box-white">'.
											'<h2>Global Site Configuration (*'.$options['domain'].')</h2>'.
											'<p>Current options will be used as <strong>Global Configuration</strong> and also applied as <strong>default configuration when new site created</strong>. Each site configuration still can be override. Just choose from the dropdown menu above to set configuration for specific site.</p>'.
											'<p>The Async Google Analytics for WordPress Multisite Have been tested on <strong>multisite configuration using sub-domains</strong> instead of sub-directory. We never been testing this plugin on multisite environment with sub-directory. Please send us any feedback if you found any bugs to <a href="mailto:bambang@minilibra.com&subject=Bugs with aga plugin on multisite">bambang@minilibra.com</a></p>'.
										'</div>';
									}

									///////////////////////////////////////////////// OPTION 0 ///////////////////////////////////////////////
									$rows = array();
									$rows[] = array(
										'id' => 'uastring',
										'label' => 'Analytics Account ID',
										'desc' => '<a href="#" id="explain">What\'s this?</a>',
										'content' => '<input id="uastring" name="uastring" type="text" size="20" maxlength="40" value="'.$options['uastring'].'" '.$field_readonly.'/><br/><div id="explanation" style="background: #fff; border: 1px solid #ccc; padding: 5px; display:none;">
											<strong>Explanation</strong><br/>
											Find the Account ID, starting with UA- in your account overview, as marked below:<br/>
											<br/>
											<img src="'.gapp_plugin_path().'account-id.png" alt="Account ID"/><br/>
											<br/>
											Once you have entered your Account ID in the box above your pages will be trackable by Google Analytics.<br/>
											Still can\'t find it? <a href="'.$this->homepage.'" target="_blank">Read more</a>!
										</div>'
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										$x_field_readonly = '';
										$x_field_desc = '';
										if( $options['tracksubdomains'] ){
											$x_field_readonly = 'readonly="readonly" style="background-color:#eee;" ';
											$x_field_desc = '<br /><div class="aga-info-box"><p>Since you have been activated <strong>Track Sub Domains And Merged into One Analytics Account ID</strong> then Analytics Account ID for this site always read automatically from your <strong>Global Configuration</strong>. Manual setting is not necessary.</p></div>';
										}
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 0,
												array(
													'id' => 'uastring_'.$k,
													'label' => 'Analytics Account ID for this site',
													'content' => '<input id="uastring_'.$k.'" name="uastring_'.$k.'" type="text" size="20" maxlength="40" value="'.$v['options']['uastring'].'" '.$x_field_readonly.'/>'.$x_field_desc,
												)
											);
											$bloglist_options[$k]=$v;
										}
									}

									if( $options['multisite'] && $options['ismainsite'] ){
										$rows[] = array(
											'id' => 'tracksubdomains',
											'label' => 'Track Sub Domains And Merged into One Analytics Account ID',
											'desc' => '<a href="#" id="explain_tracksubdomains">What\'s this?</a>',
											'content' => $this->checkbox('tracksubdomains').'<br/><div id="explanation_tracksubdomains" style="background: #fff; border: 1px solid #ccc; padding: 5px; display:none;">
												<strong>Explanation</strong><br/>
												If you activate <strong>Multisite environment</strong> and <strong>using sub domains</strong> then want to <strong>merged google analytics profile into one Google Analytics Account ID</strong> for your WordPress network, you must <strong>adjust your google analytics profile as google analytics explained at <a href="http://code.google.com/apis/analytics/docs/tracking/gaTrackingSite.html#profilesKey" target="_blank">http://code.google.com/apis/analytics/docs/tracking/gaTrackingSite.html#profilesKey</a> Point 3 (Titled: &quot;<em>Modify your cross-domain profile with a filter to show the full domain in your content reports.</em>&quot;)</strong>. You can <strong>view step by step guide</strong> about how to do this in below picture:<br/>
												<br/>
												<a href="'.gapp_plugin_path().'setup-multisite.png" target="_blank" title="Click to Enlarge"><img src="'.gapp_plugin_path().'setup-multisite-small.png" alt="Setup Multisite"/></a><br/>
												<br/>
												Once you have updated your Google Analytics profile, the rest will be take care by this plugin.<br/>
												Still confuse about this? <a href="'.$this->homepage.'" target="_blank">Read more</a>!
											</div>'
										);
									}
									if( $options['multisite'] ){
										if( $options['ismainsite'] ){
											$content_field_html = $this->textinput('domain');
										}else{
											$content_field_html = '<input id="domain" name="domain" type="text" size="40" maxlength="100" value="'.$options['domain'].'" '.$field_readonly.'/>';
										}
										$rows[] = array(
											'id' => 'domain',
											'label' => 'Domain Tracking',
											'desc' => 'This allows you to set the domain that\'s set by <a href="http://code.google.com/apis/analytics/docs/gaJSApiDomainDirectory.html#_gat.GA_Tracker_._setDomainName" target="_blank"><code>setDomainName</code></a> for tracking subdomains, if empty this will not be set.',
											'content' => $content_field_html,
										);

										if( $options['ismainsite'] && !empty($bloglist_options) ){
											$x_field_readonly = '';
											$x_field_desc = '';
											if( $options['tracksubdomains'] ){
												$x_field_readonly = 'readonly="readonly" style="background-color:#eee;" ';
												$x_field_desc = '<br /><div class="aga-info-box"><p>Since you have been activated <strong>Track Sub Domains And Merged into One Analytics Account ID</strong> then Domain Tracking for this site always read automatically from your <strong>Global Configuration</strong>. Manual setting is not necessary.</p></div>';
											}
											foreach($bloglist_options as $k=>$v){
												$v['html_config'] = $this->opts_html_config_push($v['html_config'], 0,
													array(
														'id' => 'domain_'.$k,
														'label' => 'Domain Tracking for this site',
														'content' => '<input id="domain_'.$k.'" name="domain_'.$k.'" type="text" size="40" maxlength="100" value="'.$v['options']['domain'].'" '.$x_field_readonly.'/>'.$x_field_desc
													)
												);
												$bloglist_options[$k]=$v;
											}
										}
									}
									$rows[] = array(
										'id' => 'useasync',
										'label' => 'Use asynchronous tracking method instead of traditional.',
										'desc' => 'All google analytics will use <a href="http://code.google.com/intl/id/apis/analytics/docs/tracking/asyncUsageGuide.html" target="_blank">Asynchronous Tracking</a> method.',
										'content' => $this->checkbox('useasync'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 0,
												array(
													'id' => 'useasync_'.$k,
													'label' => 'Use asynchronous tracking method instead of traditional in this site.',
													'content' => '<input id="useasync_'.$k.'" name="useasync_'.$k.'" type="checkbox"'. checked($v['options']['useasync'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'position',
										'label' => 'Where should the tracking script be placed?',
										'content' => '<select name="position" id="position">
											<option value="footer" '.checked($options['position'],true,false).'>In the footer (default)</option>
											<option value="header" '.checked($options['position'],true,false).'>In the header</option>
										</select>'
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 0,
												array(
													'id' => 'position_'.$k,
													'label' => 'Where should the tracking script be placed in this site?',
													'content' => '<select name="position_'.$k.'" id="position_'.$k.'">
														<option value="footer" '.checked($v['options']['position'],"footer",false).'>In the footer (default)</option>
														<option value="header" '.checked($v['options']['position'],"header",false).'>In the header</option>
													</select>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'trackoutbound',
										'label' => 'Track outbound clicks &amp; downloads',
										'desc' => '',
										'content' => $this->checkbox('trackoutbound'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 0,
												array(
													'id' => 'trackoutbound_'.$k,
													'label' => 'Track outbound clicks &amp; downloads in this site',
													'content' => '<input id="trackoutbound_'.$k.'" name="trackoutbound_'.$k.'" type="checkbox"'. checked($v['options']['trackoutbound'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'customfilter',
										'label' => 'Enable/Disable custom filter tracking',
										'desc' => 'Enable/Disable tracking for specified categories, tags, posts, pages, URL, or Visitor with specified IP.',
										'content' => $this->checkbox('customfilter'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 0,
												array(
													'id' => 'customfilter_'.$k,
													'label' => 'Enable/Disable custom filter tracking in this site',
													'content' => '<input id="customfilter_'.$k.'" name="customfilter_'.$k.'" type="checkbox"'. checked($v['options']['customfilter'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'advancedsettings',
										'label' => 'Show advanced settings',
										'desc' => 'Only adviced for advanced users who know their way around Google Analytics',
										'content' => $this->checkbox('advancedsettings'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 0,
												array(
													'id' => 'advancedsettings_'.$k,
													'label' => 'Show advanced settings in this site',
													'content' => '<input id="advancedsettings_'.$k.'" name="advancedsettings_'.$k.'" type="checkbox"'. checked($v['options']['advancedsettings'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$this->postbox('gasettings','Async Google Analytics Settings',$this->form_table($rows));
									

									
									
									///////////////////////////////////////////////// OPTION 1 ///////////////////////////////////////////////
									$rows = array();
									$rows[] = array(
										'id' => 'dlextensions',
										'label' => 'Extensions of files to track as downloads',
										'content' => $this->textinput('dlextensions'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 1,
												array(
													'id' => 'dlextensions_'.$k,
													'label' => 'Extensions of files to track as downloads',
													'content' => '<input type="text" size="30" name="dlextensions_'.$k.'" id="dlextensions_'.$k.'" value="'.(isset($v['options']['dlextensions']) ? $v['options']['dlextensions']:$options['dlextensions']).'" />',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'dlprefix',
										'label' => 'Prefix for tracked downloads',
										'content' => $this->textinput('dlprefix'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 1,
												array(
													'id' => 'dlprefix_'.$k,
													'label' => 'Prefix for tracked downloads',
													'content' => '<input type="text" size="30" name="dlprefix_'.$k.'" id="dlprefix_'.$k.'" value="'.(isset($v['options']['dlprefix']) ? $v['options']['dlprefix']:$options['dlprefix']).'" />',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'artprefix',
										'label' => 'Prefix for outbound clicks from links in articles',
										'content' => $this->textinput('artprefix'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 1,
												array(
													'id' => 'artprefix_'.$k,
													'label' => 'Prefix for outbound clicks from links in articles',
													'content' => '<input type="text" size="30" name="artprefix_'.$k.'" id="artprefix_'.$k.'" value="'.(isset($v['options']['artprefix']) ? $v['options']['artprefix']:$options['artprefix']).'" />',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'comprefix',
										'label' => 'Prefix for outbound clicks from links in comments',
										'content' => $this->textinput('comprefix'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 1,
												array(
													'id' => 'comprefix_'.$k,
													'label' => 'Prefix for outbound clicks from links in comments',
													'content' => '<input type="text" size="30" name="comprefix_'.$k.'" id="comprefix_'.$k.'" value="'.(isset($v['options']['comprefix']) ? $v['options']['comprefix']:$options['comprefix']).'" />',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'externalprefix',
										'label' => 'Prefix for outbound clicks from links in all other sections',
										'content' => $this->textinput('externalprefix').' <br />This is <strong>prefix</strong> for all outbound clicks (<em>links that goes to outside your website</em>) that found in any sections of your web pages (except those defined in <a href="#dlprefix">downloads</a>, <a href="#artprefix">articles</a> or <a href="#comprefix">comments</a> section above), such as blogroll, widgets, etc. Any static links such as defined in your themes also will be <strong>detected automatically</strong> does it goes to external url or not.',
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 1,
												array(
													'id' => 'externalprefix_'.$k,
													'label' => 'Prefix for outbound clicks from links in all other sections',
													'content' => '<input type="text" size="30" name="externalprefix_'.$k.'" id="externalprefix_'.$k.'" value="'.(isset($v['options']['externalprefix']) ? $v['options']['externalprefix']:$options['externalprefix']).'" />',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'domainorurl',
										'label' => 'Track full URL of outbound clicks or just the domain?',
										'content' => '<select name="domainorurl" id="domainorurl">
											<option value="domain"'.selected($options['domainorurl'],'domain',false).'>Just the domain</option>
											<option value="url"'.selected($options['domainorurl'],'url',false).'>Track the complete URL</option>
										</select>',
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 1,
												array(
													'id' => 'domainorurl_'.$k,
													'label' => 'Track full URL of outbound clicks or just the domain?',
													'content' => '<select name="domainorurl_'.$k.'" id="domainorurl_'.$k.'">
														<option value="domain"'.selected($v['options']['domainorurl'],'domain',false).'>Just the domain</option>
														<option value="url"'.selected($v['options']['domainorurl'],'url',false).'>Track the complete URL</option>
													</select>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									if( !$options['multisite'] ){
										$rows[] = array(
											'id' => 'domain',
											'label' => 'Domain Tracking',
											'desc' => 'This allows you to set the domain that\'s set by <a href="http://code.google.com/apis/analytics/docs/gaJSApiDomainDirectory.html#_gat.GA_Tracker_._setDomainName" target="_blank"><code>setDomainName</code></a> for tracking subdomains, if empty this will not be set.',
											'content' => $this->textinput('domain'),
										);
									}
									$this->postbox('outboundgasettings','Outbound Specific Settings',$this->form_table($rows));
									
									
									
									///////////////////////////////////////////////// OPTION 2 ///////////////////////////////////////////////
									$rows = array();
									$rows[] = array(
										'id' => 'exclude_userrole',
										'label' => 'Disable tracking for specified logged in user role',
										'desc' => 'All users under selected user role will NOT be tracked if they logged in.',
										'content' => '<span id="exclude_userrole_point"></span>'.get_array_checkbox_el('exclude_userrole', array(1=>'Subscriber', 2=>'Contributor', 3=>'Author', 4=>'Editor', 5=>'Administrator'), normalize_array_val($options['exclude_userrole'])),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 2,
												array(
													'id' => 'exclude_userrole_'.$k,
													'label' => 'Disable tracking for specified logged in user role of this site',
													'content' => '<span id="exclude_userrole_point_'.$k.'"></span>'.get_array_checkbox_el('exclude_userrole_'.$k, array(1=>'Subscriber', 2=>'Contributor', 3=>'Author', 4=>'Editor', 5=>'Administrator'), normalize_array_val($v['options']['exclude_userrole'])),
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'exclude_userid',
										'label' => 'Disable tracking for specified logged in User',
										'desc' => 'Please enter by User ID (numeric), Username or Email seperated by commas.',
										'content' => $this->textinput('exclude_userid'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 2,
												array(
													'id' => 'exclude_userid_'.$k,
													'label' => 'Disable tracking for specified logged in User of this site',
													'content' => '<input type="text" size="30" name="exclude_userid_'.$k.'" id="exclude_userid_'.$k.'" value="'.(isset($v['options']['exclude_userid']) ? $v['options']['exclude_userid']:$options['exclude_userid']).'" />',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'trackloggedin',
										'label' => 'Segment logged in users',
										'content' =>  $this->checkbox('trackloggedin').'&nbsp;This is only applied for NON SELECTED <strong><a href="#exclude_userrole_point">USER ROLE</a></strong> and NOT IN <strong><a href="#exclude_userid">USER LIST</a></strong> above when they logged in.',
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 2,
												array(
													'id' => 'trackloggedin_'.$k,
													'label' => 'Segment logged in users of this site',
													'content' => '<input id="trackloggedin_'.$k.'" name="trackloggedin_'.$k.'" type="checkbox"'. checked($v['options']['trackloggedin'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$opt = array();
									foreach(get_categories('hide_empty=0&hierarchical=0') as $cat){
										$opt[$cat->term_id] = $cat->cat_name;
									}
									$rows[] = array(
										'id' => 'excludecategories',
										'label' => 'Disable tracking for following categories',
										'desc' => 'All checked categories will not be tracking.',
										'content' => get_array_checkbox_el('excludecategories', $opt, normalize_array_val($options['excludecategories'])),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 2,
												array(
													'id' => 'excludecategories_'.$k,
													'label' => 'Disable tracking for categories in this site',
													'content' => '<strong style="color:red">Override on Specific site Settings</strong>',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$opt = array();
									foreach(get_tags('hide_empty=0&hierarchical=0') as $tag){
										$opt[$tag->term_id] = $tag->name;
									}
									$rows[] = array(
										'id' => 'excludetags',
										'label' => 'Disable tracking for following tags',
										'desc' => 'All checked tags will not be tracking.',
										'content' => get_array_checkbox_el('excludetags', $opt, normalize_array_val($options['excludetags'])),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 2,
												array(
													'id' => 'excludetags_'.$k,
													'label' => 'Disable tracking for tags in this site',
													'content' => '<strong style="color:red">Override on Specific site Settings</strong>',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$opt = array();
									foreach(get_pages('hierarchical=0') as $page){
										$opt[$page->ID] = $page->post_title;
									}
									$rows[] = array(
										'id' => 'excludepages',
										'label' => 'Disable tracking for following pages',
										'desc' => 'All checked pages will not be tracking.',
										'content' => get_array_checkbox_el('excludepages', $opt, normalize_array_val($options['excludepages'])),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 2,
												array(
													'id' => 'excludepages_'.$k,
													'label' => 'Disable tracking for pages in this site',
													'content' => '<strong style="color:red">Override on Specific site Settings</strong>',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'excludeposts',
										'label' => 'Disable tracking for following posts',
										'desc' => 'Please enter the post ID seperated by commas.',
										'content' => $this->textinput('excludeposts'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 2,
												array(
													'id' => 'excludeposts_'.$k,
													'label' => 'Disable tracking for posts in this site',
													'content' => '<strong style="color:red">Override on Specific site Settings</strong>',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'excludeuri',
										'label' => 'Disable tracking for following URL/URI',
										'desc' => 'Please enter the url/uri. One per line.',
										'content' => '<textarea id="excludeuri" name="excludeuri" cols="60" rows="10">'.$options['excludeuri'].'</textarea>',
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 2,
												array(
													'id' => 'excludeuri_'.$k,
													'label' => 'Disable tracking for site URL/URI in this site',
													'content' => '<textarea id="excludeuri_'.$k.'" name="excludeuri_'.$k.'" cols="60" rows="10">'.$v['options']['excludeuri'].'</textarea>',
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$my_ip_is = get_my_current_ip();
									$rows[] = array(
										'id' => 'excludeips',
										'label' => 'Disable tracking for visitor from following IP',
										'desc' => 'Please enter the IP. One per line. If you want to exclude yourself from being tracking, then you can add your IP here.',
										'content' => '<strong>Your current IP Address is '.$my_ip_is.'</strong><br /><textarea id="excludeips" name="excludeips" cols="60" rows="10">'.$options['excludeips'].'</textarea>',
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 2,
												array(
													'id' => 'excludeips_'.$k,
													'label' => 'Disable tracking for visitor of this site from following IP',
													'content' => '<strong>Your current IP Address is '.$my_ip_is.'</strong><br /><textarea id="excludeips_'.$k.'" name="excludeips_'.$k.'" cols="60" rows="10">'.$$v['options']['excludeips'].'</textarea>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$this->postbox('customgafilter','Custom Filter Settings',$this->form_table($rows));

									
									
									///////////////////////////////////////////////// OPTION 3 ///////////////////////////////////////////////
									$rows = array();
									$rows[] = array(
										'id' => 'trackadsense',
										'label' => 'Track AdSense',
										'desc' => 'This requires integration of your Analytics and AdSense account, for help, <a href="https://www.google.com/adsense/support/bin/topic.py?topic=15007" target="_blank">look here</a>.',
										'content' => $this->checkbox('trackadsense'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 3,
												array(
													'id' => 'trackadsense_'.$k,
													'label' => 'Track AdSense in this site',
													'content' => '<input id="trackadsense_'.$k.'" name="trackadsense_'.$k.'" type="checkbox"'. checked($v['options']['trackadsense'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'extrase',
										'label' => 'Track extra Search Engines',
										'content' => $this->checkbox('extrase'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 3,
												array(
													'id' => 'extrase_'.$k,
													'label' => 'Track extra Search Engines in this site',
													'content' => '<input id="extrase_'.$k.'" name="extrase_'.$k.'" type="checkbox"'. checked($v['options']['extrase'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'imagese',
										'label' => 'Track Google Image Search as a Search Engine',
										'desc' => 'This functionality is in beta, and not confirmed to work yet',
										'content' => $this->checkbox('imagese'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 3,
												array(
													'id' => 'imagese_'.$k,
													'label' => 'Track Google Image Search as a Search Engine in this site',
													'content' => '<input id="imagese_'.$k.'" name="imagese_'.$k.'" type="checkbox"'. checked($v['options']['imagese'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'userv2',
										'label' => 'I use Urchin',
										'content' => $this->checkbox('userv2'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 3,
												array(
													'id' => 'userv2_'.$k,
													'label' => 'Use Urchin Trracker in this site',
													'content' => '<input id="userv2_'.$k.'" name="userv2_'.$k.'" type="checkbox"'. checked($v['options']['userv2'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'rsslinktagging',
										'label' => 'Tag links in RSS feed with campaign variables',
										'content' => $this->checkbox('rsslinktagging'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 3,
												array(
													'id' => 'rsslinktagging_'.$k,
													'label' => 'Tag links in RSS feed with campaign variables for this site',
													'content' => '<input id="rsslinktagging_'.$k.'" name="rsslinktagging_'.$k.'" type="checkbox"'. checked($v['options']['rsslinktagging'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'trackregistration',
										'label' => 'Add tracking to the login and registration forms',
										'content' => $this->checkbox('trackregistration'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 3,
												array(
													'id' => 'trackregistration_'.$k,
													'label' => 'Add tracking to the login and registration forms in this site',
													'content' => '<input id="trackregistration_'.$k.'" name="trackregistration_'.$k.'" type="checkbox"'. checked($v['options']['trackregistration'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$rows[] = array(
										'id' => 'allowanchor',
										'label' => 'Use # instead of ? for Campaign tracking?',
										'desc' => 'This adds a <a href="http://code.google.com/apis/analytics/docs/gaJSApiCampaignTracking.html#_gat.GA_Tracker_._setAllowAnchor" target="_blank">setAllowAnchor</a> call to your tracking script, and makes RSS link tagging use a # as well.',
										'content' => $this->checkbox('allowanchor'),
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 3,
												array(
													'id' => 'allowanchor_'.$k,
													'label' => 'Use # instead of ? for Campaign tracking in this site?',
													'content' => '<input id="allowanchor_'.$k.'" name="allowanchor_'.$k.'" type="checkbox"'. checked($v['options']['allowanchor'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$this->postbox('advancedgasettings','Advanced Settings',$this->form_table($rows));
									
									
									
									
									///////////////////////////////////////////////// OPTION 4 ///////////////////////////////////////////////
									$rows = array();
									$rows[] = array(
										'id' => 'allow_linkback',
										'label' => 'May I add credit link?',
										'desc' => 'Any credit link would be appreciated.',
										'content' => $this->checkbox('allow_linkback') . '&nbsp;'.
										'<span id="credit_desc1" style="display:none;"><strong>Thank you for being my friend ^_^</strong>. As soon as your website / blog also will be added to our friends blogroll then we have get connected each other ^_^</span>'.
										'<span id="credit_desc2" style="display:none;">This would help us to tell the world about this great plugin. We will be really appreciated if you can help us to share this plugin by put below text into any of your posts/pages. Your website / blog also will be added to our blogroll so we have get connected each other ^_^</span>'.
										'<br /><textarea id="credit_text" cols="85" rows="6" readonly="readonly" style="background-color:#eee;font-size:10px;" onfocus="this.select()" onclick="this.select()">'.htmlentities('<p>site tracking with <strong><a href="http://www.minilibra.com/wordpress/plugins/analytics.html" target="_blank">Asynchronous Google Analytics</a></strong> plugin for Multisite by <strong><a rel="friend" href="http://www.minilibra.com/" title="WordPress Expert" target="_blank">WordPress Expert</a></strong> at <strong><a rel="friend" href="http://www.satublogs.com/" title="Web Design Jakarta">Web Design Jakarta</a></strong>.</p>').'</textarea><br />',
									);
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										foreach($bloglist_options as $k=>$v){
											$v['html_config'] = $this->opts_html_config_push($v['html_config'], 4,
												array(
													'id' => 'allow_linkback_'.$k,
													'label' => 'May I add credit link to this site?',
													'content' => '<input id="allow_linkback_'.$k.'" name="allow_linkback_'.$k.'" type="checkbox"'. checked($v['options']['allow_linkback'],true,false).'/>'
												)
											);
											$bloglist_options[$k]=$v;
										}
									}
									$this->postbox('gacredits','Little credit link would be appreciate',$this->form_table($rows));

									
									
									if( $options['multisite'] && $options['ismainsite'] && !empty($bloglist_options) ){
										echo '</div>';

										foreach($bloglist_options as $k=>$v){
											echo '<div id="aga_config_'.$k.'" style="display:none;">';

											echo '<div class="aga-info-box-white">'.
													'<h2>'.$v['info']->blogname.' ('.$v['info']->domain.')</h2>'.
													'<p>Current options will be override <strong>Global Configuration</strong> and Only Applied to Current Site: <strong>'.$v['info']->blogname.'</strong> (<strong>'.$v['info']->domain.'</strong>) only.</p>'.
													'<p>Except for those <strong>Analytics Account ID</strong> and <strong>Domain Tracking</strong> options, the others options still can be override by Current Site\'s Administrator.</p>'.
												'</div>';
											$this->postbox('gasettings_'.$k,'Async Google Analytics Settings',$this->form_table($v['html_config'][0]));
											$this->postbox('outboundgasettings_'.$k,'Outbound Specific Settings',$this->form_table($v['html_config'][1]));
											$this->postbox('customgafilter_'.$k,'Custom Filter Settings',$this->form_table($v['html_config'][2]));
											$this->postbox('advancedgasettings_'.$k,'Advanced Settings',$this->form_table($v['html_config'][3]));
											$this->postbox('gacredits_'.$k,'Little credit link would be appreciate',$this->form_table($v['html_config'][4]));
											///next options
											echo '</div>';
										}
									}
								?>
						<div class="submit" style="display: inline-block; float: left; padding: 0pt 10px 10px 0pt;"><input type="submit" class="button-primary" name="submit" value="Update Async Google Analytics Settings &raquo;" /></div>
					</form>
					<form action="" method="post">
						<input type="hidden" name="reset" value="true"/>
						<div class="submit" style="display: inline-block; float: left; padding: 0pt 10px 10px 0pt;"><input type="submit" value="Reset Default Settings &raquo;" /></div>
					</form>
				</div>
			</div>
		</div>
		<div class="postbox-container" style="width:20%;">
			<div class="metabox-holder">
				<div class="meta-box-sortables">
					<?php
						$this->plugin_like();
						$this->plugin_support();
						$this->news();
						if( stripos(get_bloginfo('home'), 'minilibra.com') !== FALSE ) $this->plugindev_support_news();
					?>
				</div>
				<br/><br/><br/>
			</div>
		</div>
	</div>
			<?php
			if (isset($options['uastring'])) {
				if ($options['uastring'] == "") {
					add_action('admin_footer', array(&$this,'warning'));
				} else {
					if (isset($_POST['submit'])) {
						if ($_POST['uastring'] != $options['uastring'] ) {
							add_action('admin_footer', array(&$this,'success'));
						}
					}
				}
			} else {
				add_action('admin_footer', array(&$this,'warning'));
			}
		}

		function warning() {
			echo "<div id='message' class='error'><p><strong>Async Google Analytics is not active.</strong> You must <a href='plugins.php?page=asyncgoogleanalytics.php'>enter your UA String</a> for it to work.</p></div>";
		} // end warning()

	} // end class AGA_Admin

} //endif

global $aga_admin;
if( !isset($aga_admin) ) $aga_admin = new AGA_Admin();
?>