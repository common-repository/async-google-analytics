jQuery(document).ready(function($){

		var do_track_outbound = ( typeof window.AGA_LINKS_DATA!='undefined' && window.AGA_LINKS_DATA.count>0 );
		var do_track_external = ( typeof window.AGA_LINKS_DATA!='undefined' && typeof window.AGA_LINKS_DATA.external!='undefined' && window.AGA_LINKS_DATA.external!=null && typeof window.AGA_LINKS_DATA.external.track!='undefined' && window.AGA_LINKS_DATA.external.track );
		
		if( do_track_external ){
			var current_domain = window.AGA_LINKS_DATA.external.domain;
			var external_prefix = window.AGA_LINKS_DATA.external.prefix+'/';
			var full_track = window.AGA_LINKS_DATA.external.fulltrack;
		}

		if( do_track_outbound || do_track_external ){

			$('a').each(function(i,el){
				
				if( $(el).hasClass('aga') && !$(el).hasClass('aga_no') && do_track_outbound ){
					
					for(var x=0;x<window.AGA_LINKS_DATA.count-1;x++){
						
						var link_item=window.AGA_LINKS_DATA.data[x];
						
						if( typeof link_item.name!='undefined' && $(el).hasClass(link_item.name) ){
							$(el).bind('click', link_item, function(ev){
								if( 
									typeof window.AGA_CUSTOM_FILTER != 'undefined' && window.AGA_CUSTOM_FILTER && 
									typeof ev.data!='undefined' && ev.data!=null && 
									typeof ev.data.param!='undefined' && ev.data.param!=null
								){
									if( typeof ev.data.func!='undefined' ){
										ev.data.func(ev.data.param);
									}else if(typeof window._gaq != 'undefined'){
										window._gaq.push(ev.data.param);
									}else if(typeof window.pageTracker != 'undefined'){
										window.pageTracker._trackPageview(ev.data.param);
									}
								}
							});

							break;
						}

					}

				}else if( !$(el).hasClass('aga_no') && do_track_external ){
					var s = ''+$(el).attr('href');
					if( typeof s!='undefined' && s!='null' && s!='undefined' && s!=undefined && s!=null && s!='' && ( s.indexOf('http')==0 || s.indexOf('ftp')==0 ) ){
						if( s.indexOf('ftp://')==0 ){
							s = s.substr(6);
						}else if( s.indexOf('http://')==0 || s.indexOf('ftps://')==0 ){
							s = s.substr(7);
						}else if( s.indexOf('https://')==0 ){
							s = s.substr(8);
						}else{
							s = '';
						}
						if( s != '' && s.indexOf(current_domain)<0 ){
							if( full_track ){
								track_url = external_prefix + s;
							}else{
								arr = s.split('/');
								track_url = external_prefix + arr[0];
							}
							if( typeof window._gaq!='undefined' && window._gaq != null ){
								data_param={param:new Array('_trackPageview', track_url)};
							}else{
								data_param={param:track_url};
							}
							$(el).bind('click', data_param, function(ev){
								if( 
									typeof window.AGA_CUSTOM_FILTER != 'undefined' && window.AGA_CUSTOM_FILTER && 
									typeof ev.data!='undefined' && ev.data!=null && 
									typeof ev.data.param!='undefined' && ev.data.param!=null
								){
									if( typeof ev.data.func!='undefined' ){
										ev.data.func(ev.data.param);
									}else if(typeof window._gaq != 'undefined'){
										window._gaq.push(ev.data.param);
									}else if(typeof window.pageTracker != 'undefined'){
										window.pageTracker._trackPageview(ev.data.param);
									}
								}
							});
						}
					}
				}

			});
		
		} //End of do_track_outbound OR do_track_external
});