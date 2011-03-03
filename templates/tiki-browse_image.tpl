{if $popup}<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="StyleSheet"  href="styles/{$prefs.style}" type="text/css" />
<script type="text/javascript" src="lib/imagegals/imagegallib.js"></script>
</head>
<body class="tiki_browse_image_popup">
<div id="{$rootid}browse_image">
{else}

  {title}{tr}Browsing Image:{/tr}&nbsp;{$name}{/title}
<div id="{$rootid}browse_image">
  <div class="navbar">
		{button href="tiki-browse_gallery.php?galleryId=$galleryId&amp;offset=$offset" _text="{tr}Return to Gallery{/tr}"}
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
			{button href="tiki-edit_image.php?galleryId=$galleryId&amp;edit=$imageId&amp;sort_mode=$sort_mode" _text="{tr}Edit Image{/tr}"}
    {/if}
  </div>
{/if}

{capture name=buttons}

{***** when not sliding buttons *****}
  <div align="center" class="noslideshow">

{* --- first image --- *}
	<a href="{$url_base}{$firstId}{$same_scale}"
		class="gallink"{if $imageId eq $firstId} style="display: none;"{/if}>{icon _id='resultset_first' alt="{tr}First Image{/tr}"}</a>

{* --- previous image --- *}
	<a href="{$url_base}{$previmg}{$same_scale}"
    	class="gallink" style="padding-right:6px;{if !$previmg} display: none;{/if}">    	{icon _id='resultset_previous' alt="{tr}Prev Image{/tr}"}</a>

{* --- previous scale --- *}
	{if $scaleinfo.prevscale}
   	  <a href="{$url_base}{$imageId}&amp;scalesize={$scaleinfo.prevscale}" class="gallink">{html_image file='img/icons/zoom-.gif' border='none' alt="{tr}Smaller{/tr}" title="{tr}Smaller{/tr}"}</a>
	{/if}

{* --- original size --- *}
	{if $resultscale}
	  <a href="{$url_base}{$imageId}&amp;scalesize=0" class="gallink">{html_image file='img/icons/zoom_equal.gif' border='none' alt="{tr}Original Size{/tr}" title="{tr}Original Size{/tr}"}</a>
	{/if}

{* --- next scale --- *}
	{if $scaleinfo.nextscale}
	  <a href="{$url_base}{$imageId}&amp;scalesize={$scaleinfo.nextscale}" class="gallink">{html_image file='img/icons/zoom+.gif' border='none' alt="{tr}Bigger{/tr}" title="{tr}Bigger{/tr}"}</a>
	{/if}
	    
{* --- popup launch --- *}
	{if !$popup}
	  <a {jspopup height="$winy" width="$winx" href="$url_base$imageId&amp;popup=1&amp;scalesize=$defaultscale"} class="gallink">
        {icon _id='layers' alt="{tr}Popup window{/tr}"}</a>
	{/if}

{* --- next image --- *}
	<a href="{$url_base}{$nextimg}{$same_scale}"
    	class="gallink" style="padding-left:6px;{if !$nextimg} display: none;{/if}">    	{icon _id='resultset_next' alt="{tr}Next Image{/tr}"}</a>

{* --- launch slideshow --- *}
	{if $listImgId}
	  <a href="javascript:thepix.toggle('start')">{html_image file='img/icons2/cycle_next.gif' border='none' alt="{tr}Slideshow Forward{/tr}" title="{tr}Slideshow Forward{/tr}"}</a>
	{/if}

{* --- last image --- *}
	<a href="{$url_base}{$lastId}{$same_scale}"
		class="gallink"{if $imageId eq $lastId} style="display: none;"{/if}>{icon _id='resultset_last' alt="{tr}Last Image{/tr}"}</a>    
  </div>

{***** when sliding buttons *****}
  <div class="slideshow" style="display: none;" align="center">

{* --- stop --- *}
	<a href="javascript:thepix.toggle('stop')">{html_image file='img/icons2/admin_delete.gif' border='none' alt="{tr}Stop{/tr}" title="{tr}Stop{/tr}"}</a>
{* --- toggle cyclic --- *}
	<a href="javascript:thepix.toggle('toTheEnd')">{html_image file='img/icons/ico_redo.gif' border='none' alt="{tr}Cyclic{/tr}" title="{tr}Cyclic{/tr}"}</a>
{* --- toggle back/forward --- *}
	<a href="javascript:thepix.toggle('backward')">{html_image file='img/icons/ico_mode.gif' border='none' alt="{tr}Direction{/tr}" title="{tr}Direction{/tr}"}</a>
  </div>
{/capture}
{$smarty.capture.buttons}

<div class="showimage">
{if $scaleinfo.clickscale >= 0}
  <a href="{$url_base}{$imageId}&amp;scalesize={$scaleinfo.clickscale}" title="{tr}Click to zoom{/tr}">
{/if}
<img src="show_image.php?id={$imageId}&amp;scalesize={$resultscale}&amp;nocount=y" alt="{tr}Image{/tr}" id="thepix" />
{if $scaleinfo.clickscale >= 0}
</a>
{/if}
</div>
  
{if !$popup}
  {$smarty.capture.buttons}
{/if}
  
{if $popup eq ""}
	<br /><br />
	<table class="normal noslideshow">
		<tr><td class="odd">{tr}Image Name:{/tr}</td><td class="odd">{$name}</td></tr>
		<tr><td class="even">{tr}Created:{/tr}</td><td class="even">{$created|tiki_long_datetime}</td></tr>
		<tr><td class="odd">{tr}Image size:{/tr}</td><td class="odd">{$xsize}x{$ysize}</td></tr>
		<tr><td class="even">{tr}Image Scale:{/tr}</td><td class="even">{if $resultscale}{$xsize_scaled}x{$ysize_scaled}{else}{tr}Original Size{/tr}{/if}</td></tr>
		<tr><td class="odd">{tr}Hits:{/tr}</td><td class="odd">{$hits}</td></tr>
		<tr><td class="even">{tr}Description:{/tr}</td><td class="even">{$description}</td></tr>
		{if $prefs.feature_maps eq 'y' and $gal_info.geographic eq 'y'}
	  		<tr><td class="odd">{tr}Latitude (WGS84/decimal degrees):{/tr}</td><td class="odd">{$lat|escape}</td></tr>
	  		<tr><td class="even">{tr}Longitude (WGS84/decimal degrees):{/tr}</td><td class="even">{$lon|escape}</td></tr>
	  	{/if}
		<tr><td class="odd">{tr}Author:{/tr}</td><td class="odd">{$image_user|userlink}</td></tr>
		{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
		<tr>
			<td class="even">
				{tr}Move image:{/tr}
			</td>
			<td class="odd">
				<form action="tiki-browse_image.php" method="post">
					<input type="hidden" name="scalesize" value="{$scalesize|escape}" />
					<input type="hidden" name="sort_mode" value="{$sort_mode|escape}"/>
					<input type="hidden" name="imageId" value="{$imageId|escape}"/>
					<input type="hidden" name="galleryId" value="{$galleryId|escape}"/>
					<input type="text" name="newname" value="{$name}" />
					<select name="newgalleryId">
				    {section name=idx loop=$galleries}
				      <option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
				    {/section}
					</select>
					<input type="submit" name="move_image" value="{tr}Move{/tr}" />
			  </form>
			</td>
		</tr>
		{/if}
	</table>
	<br /><br />    
	<table class="normal noslideshow" style="font-size:small">
		<tr>
			<td class="even" style="border-bottom:0px" colspan="2">
				{tr}Include the image in a tiki page using the following syntax:{/tr}
			</td>
		</tr>
		<tr>
			<td width="6px" style="border:0px">
			</td>
			<td style="border:0px">
				<div class="inline_syntax">
					{if $resultscale == $defaultscale}
						{literal}{{/literal}img id={$imageId}{literal}}{/literal}
					{elseif !$resultscale}
						{literal}{{/literal}img id={$imageId}&amp;scalesize=0){literal}}{/literal}
					{else}
						{literal}{{/literal}img id={$imageId}&amp;scaled&amp;scalesize={$resultscale}{literal}}{/literal}
					{/if}
				</div>
			</td>
		</tr>
		<tr>
			<td class="even" style="border-bottom:0px" colspan="2">
				{tr}To include the image in an HTML page:{/tr}
		    </td>
		</tr>
		<tr>
			<td width="10px" style="border:0px">
			</td>
			<td style="border:0px">
				<div class="inline_syntax">
		    		{if $resultscale == $defaultscale}
		   			 	&lt;img src="{$url_show}?id={$imageId}" /&gt;
		    		{elseif !$resultscale}
		    			&lt;img src="{$url_show}?id={$imageId}&amp;scalesize=0" /&gt;
		    		{else}
		    			&lt;img src="{$url_show}?id={$imageId}&amp;scalesize={$resultscale}" /&gt;
		    		{/if}
		    	</div>
			</td>
		</tr>
		<tr>
		  	<td class="even" style="border-bottom:0px" colspan="2">
		    	{tr}To link to this page from another tiki page:{/tr}
		    </td>
		</tr>
		<tr>
			<td width="6px" style="border:0px">
		   </td>
		    <td style="border:0px">
				<div class="inline_syntax">
					{literal}[{/literal}tiki-browse_image.php?imageId={$imageId}{literal}]{/literal}
				</div>
			</td>
		</tr>
	</table>
{/if}  

</div> {* id="{$rootid}browse_image" *}

{if $listImgId}

<script type='text/javascript'>
<!--
var tmp = window.location.search.match(/delay=(\d+)/);
tmp = tmp ? parseInt(tmp[1]) : 3000;
var thepix = new Diaporama('thepix', [{$listImgId}], {ldelim}
	  startId: {$imageId},
	  root: '{$rootid}browse_image',
	  resetUrl: 1,
	  delay: tmp
	{rdelim});
//-->
</script>

{/if}

{if $popup}
</body></html>
{/if}
