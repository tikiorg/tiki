<?php

function getP ( $param_name , $default_value = null )
{
	$value = @$_REQUEST[$param_name];
	if ( $value ) return $value;
	else return $default_value;
}



//
function createGenericWidgetHtml ( $kshow_id , $user_id , $size='l' , $align='l', $version=null , $version_kshow_name=null , $version_kshow_description=null)
{
	global $partner_id, $subp_id, $partner_name;
	global $ks; // assume there is a ks for the user- there as a successful startSession
	global $WIDGET_HOST;

    $media_type = 2;
    $widget_type = 3;
    $entry_id = null;

     // add the version as an additional parameter
	$domain = $WIDGET_HOST; //"http://www.kaltura.com";
	$swf_url = "/index.php/widget/$kshow_id/" .
		( $entry_id ? $entry_id : "-1" ) . "/" .
		( $media_type ? $media_type : "-1" ) . "/" .
		( $widget_type ? $widget_type : "3" ) . "/" . // widget_type=3 -> WIKIA
		( $version ? "$version" : "-1" );

	$current_widget_kshow_id_list[] = $kshow_id;

	$kshowCallUrl = "$domain/index.php/browse?kshow_id=$kshow_id";
	$widgetCallUrl = "$kshowCallUrl&browseCmd=";
	$editCallUrl = "$domain/index.php/edit?kshow_id=$kshow_id";

/*
  widget3:
  url:  /widget/:kshow_id/:entry_id/:kmedia_type/:widget_type/:version
  param: { module: browse , action: widget }
 */
    if ( $size == "m")
    {
    	// medium size
    	$height = 198 + 105;
    	$width = 267;
    }
    else
    {
    	// large size
    	$height = 300 + 105 + 20;
    	$width = 400;
    }

	$root_url = "" ; //getRootUrl();

    $str = "";//$extra_links ; //"";

    $external_url = "http://" . @$_SERVER["HTTP_HOST"] ."$root_url";

	$share = "TODO" ; //$titleObj->getFullUrl ();

	// this is a shorthand version of the kdata
    $links_arr = array (
    		"base" => "$external_url/" ,
    		"add" =>  "Special:KalturaContributionWizard?kshow_id=$kshow_id" ,
    		"edit" => "Special:KalturaVideoEditor?kshow_id=$kshow_id" ,
    		"share" => $share ,
    	);

    $links_str = str_replace ( array ( "|" , "/") , array ( "|01" , "|02" ) , base64_encode ( serialize ( $links_arr ) ) ) ;

	$kaltura_link = "<a href='http://www.kaltura.com' style='color:#bcff63; text-decoration:none; '>Kaltura</a>";
	$kaltura_link_str = "A $partner_name collaborative video powered by  "  . $kaltura_link;

	$flash_vars = array (  "CW" => "gotoCW" ,
    						"Edit" => "gotoEdit" ,
    						"Editor" => "gotoEditor" ,
							"Kaltura" => "",//gotoKalturaArticle" ,
							"Generate" => "" , //gotoGenerate" ,
							"share" => "" , //$share ,
							"WidgetSize" => $size );

	// add only if not null
	if ( $version_kshow_name ) $flash_vars["Title"] = $version_kshow_name;
	if ( $version_kshow_description ) $flash_vars["Description"] = $version_kshow_description;

	$swf_url .= "/" . $links_str;
   	$flash_vars_str = http_build_query( $flash_vars , "" , "&" )		;

    $widget = /*$extra_links .*/
		 '<object id="kaltura_player_' . (int)microtime(true) . '" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="' . $height . '" width="' . $width . '" data="'.$domain. $swf_url . '">'.
			'<param name="allowScriptAccess" value="always" />'.
			'<param name="allowNetworking" value="all" />'.
			'<param name="bgcolor" value=#000000 />'.
			'<param name="movie" value="'.$domain. $swf_url . '"/>'.
			'<param name="flashVars" value="' . $flash_vars_str . '"/>'.
			'<param name="wmode" value="opaque"/>'.
			$kaltura_link .
			'</object>' ;

		"</td></tr><tr><td style='background-color:black; color:white; font-size: 11px; padding:5px 10px; '>$kaltura_link</td></tr></table>";

	if ( $align == 'r' )
	{
		$str .= '<div class="floatright"><span>' . $widget . '</span></div>';
	}
	elseif ( $align == 'l' )
	{
		$str .= '<div class="floatleft"><span>' . $widget . '</span></div>';
	}
	elseif ( $align == 'c' )
	{
		$str .= '<div class="center"><div class="floatnone"><span>' . $widget . '</span></div></div>';
	}
	else
	{
		$str .= $widget;
	}

	return $str ;
}

function kaltura_init_config ()
{
global $partner_id,$subp_id ,$secret ,$admin_secret , $service_url ;
	$conf = new KalturaConfiguration( $partner_id , $subp_id );
	$conf->partnerId = $partner_id;
	$conf->subPartnerId = $subp_id;
	$conf->secret = $secret;
	$conf->adminSecret = $admin_secret;
	$conf->serviceUrl = "http://www.kaltura.com";
	//$conf->setLogger( new KalturaDemoLogger());
	return $conf;
}


function kaltura_createPagerForGallery  ( $p , $current_page , $page_size , $count ,$js_callback_paging_clicked  )
{
	$p = (int)$p;
	$a = $p * $page_size + 1;
	$b = (($p+1) * $page_size) ;
	$b = min ( $b , $count ) ;// don't let the page-end be bigger than the total count
	$page_to_goto = $p + 1;
	if ( $page_to_goto == $current_page )
	{
		$str = "[<a title='{$page_to_goto}' href='javascript:{$js_callback_paging_clicked} ($page_to_goto)'>{$a}-{$b}</a>] ";
//		$str =  "[{$a}-{$b}] ";
	}
	else
		$str =  "<a title='{$page_to_goto}' href='javascript:{$js_callback_paging_clicked} ($page_to_goto)'>{$a}-{$b}</a> ";

	return $str;
}



?>