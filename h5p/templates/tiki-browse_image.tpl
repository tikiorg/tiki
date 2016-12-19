{* $Id$ *}{if $popup}<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="StyleSheet" href="styles/{$prefs.style}" type="text/css">
	<script type="text/javascript" src="lib/imagegals/imagegallib.js"></script>
</head>
<body class="tiki_browse_image_popup">
<div id="{$rootid}browse_image">
{else}

	{title}{tr}Browsing Image:{/tr} {$name}{/title}
<div id="{$rootid}browse_image">
	<div class="t_navbar">
		{button href="tiki-browse_gallery.php?galleryId=$galleryId&amp;offset=$offset" class="btn btn-default" _icon_name="previous" _text="{tr}Return to Gallery{/tr}"}
		{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
			{button href="tiki-edit_image.php?galleryId=$galleryId&amp;edit=$imageId&amp;sort_mode=$sort_mode" class="btn btn-default" _icon_name="edit" _text="{tr}Edit Image{/tr}"}
		{/if}
	</div>
{/if}

{capture name=buttons}

{***** when not sliding buttons *****}
	<div align="center" class="noslideshow">

{* --- first image --- *}
		<a class="tips" title=":{tr}First{/tr}" href="{$url_base}{$firstId}{$same_scale}" {if $imageId eq $firstId} style="display: none;"{/if}>
			{icon name='backward_step'}
		</a>

{* --- previous image --- *}
		<a class="tips" title=":{tr}Previous{/tr}" href="{$url_base}{$previmg}{$same_scale}" style="padding-right:6px;{if !$previmg} display: none;{/if}">
			{icon name='backward'}
		</a>

{* --- previous scale --- *}
		{if $scaleinfo.prevscale}
			<a class="tips" title=":{tr}Smaller{/tr}"  href="{$url_base}{$imageId}&amp;scalesize={$scaleinfo.prevscale}">
				{icon name='view'}
			</a>
		{/if}

{* --- original size --- *}
		{if $resultscale}
			<a class="tips" title=":{tr}Original size{/tr}" href="{$url_base}{$imageId}&amp;scalesize=0">
				{icon name='image'}
			</a>
		{/if}

{* --- next scale --- *}
		{if $scaleinfo.nextscale}
			<a class="tips" title=":{tr}Bigger{/tr}" href="{$url_base}{$imageId}&amp;scalesize={$scaleinfo.nextscale}">
				{icon name='view'}
			</a>
		{/if}

{* --- popup launch --- *}
		{if !$popup}
			<a {jspopup height="$winy" width="$winx" href="$url_base$imageId&amp;popup=1&amp;scalesize=$defaultscale"} class="tips" title=":{tr}Popup{/tr}" >
				{icon name='popup'}
			</a>
		{/if}

{* --- next image --- *}
		<a class="tips" title=":{tr}Next{/tr}" href="{$url_base}{$nextimg}{$same_scale}" style="padding-left:6px;{if !$nextimg} display: none;{/if}">
			{icon name='forward'}
		</a>

{* --- launch slideshow --- *}
		{if $listImgId}
			<a class="tips" title=":{tr}Slideshow forward{/tr}" href="javascript:thepix.toggle('start')">
				{icon name='next'}
			</a>
		{/if}

{* --- last image --- *}
		<a class="tips" title=":{tr}Last{/tr}" href="{$url_base}{$lastId}{$same_scale}" class="gallink"{if $imageId eq $lastId} style="display: none;"{/if}>
			{icon name='forward_step' alt="{tr}Last{/tr}"}
		</a>
	</div>

{***** when sliding buttons *****}
	<div class="slideshow" style="display: none;" align="center">

{* --- stop --- *}
		<a class="tips" title=":{tr}Stop{/tr}" href="javascript:thepix.toggle('stop')">
			{icon name='stop'}
		</a>
{* --- toggle cyclic --- *}
		<a class="tips" title=":{tr}Repeat{/tr}" href="javascript:thepix.toggle('toTheEnd')">
			{icon name='repeat'}
		</a>
{* --- toggle back/forward --- *}
		<a class="tips" title=":{tr}Direction{/tr}" href="javascript:thepix.toggle('backward')">
			{icon name='move'}
		</a>
	</div>
{/capture}
{$smarty.capture.buttons}

<div class="showimage">
	{if $scaleinfo.clickscale >= 0}
		<a href="{$url_base}{$imageId}&amp;scalesize={$scaleinfo.clickscale}" title="{tr}Click to zoom{/tr}">
	{/if}
	<img src="show_image.php?id={$imageId}&amp;scalesize={$resultscale}&amp;nocount=y" alt="{tr}Image{/tr}" id="thepix">
	{if $scaleinfo.clickscale >= 0}
		</a>
	{/if}
</div>

{if !$popup}
	{$smarty.capture.buttons}
{/if}

{if $popup eq ""}
	<br><br>
	<div class="table-responsive">
		<table class="table noslideshow">
			<tr><td class="odd">{tr}Image Name:{/tr}</td><td class="odd">{$name}</td></tr>
			<tr><td class="even">{tr}Created:{/tr}</td><td class="even">{$created|tiki_long_datetime}</td></tr>
			<tr><td class="odd">{tr}Image size:{/tr}</td><td class="odd">{$xsize}x{$ysize}</td></tr>
			<tr><td class="even">{tr}Image Scale:{/tr}</td><td class="even">{if $resultscale}{$xsize_scaled}x{$ysize_scaled}{else}{tr}Original Size{/tr}{/if}</td></tr>
			<tr><td class="odd">{tr}Hits:{/tr}</td><td class="odd">{$hits}</td></tr>
			<tr><td class="even">{tr}Description:{/tr}</td><td class="even">{$description}</td></tr>
			<tr><td class="odd">{tr}Author:{/tr}</td><td class="odd">{$image_user|userlink}</td></tr>
			{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
			<tr>
				<td class="even">
					{tr}Move image:{/tr}
				</td>
				<td class="odd">
					<form action="tiki-browse_image.php" method="post">
						<input type="hidden" name="scalesize" value="{$scalesize|escape}">
						<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
						<input type="hidden" name="imageId" value="{$imageId|escape}">
						<input type="hidden" name="galleryId" value="{$galleryId|escape}">
						<input type="text" name="newname" value="{$name}">
						<select name="newgalleryId">
							{section name=idx loop=$galleries}
								<option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
							{/section}
						</select>
						<input type="submit" class="btn btn-default btn-sm" name="move_image" value="{tr}Move{/tr}">
					</form>
				</td>
			</tr>
			{/if}
		</table>
	</div>
	<br><br>
	<div class="table-responsive">
		<table class="table noslideshow" style="font-size:small">
			<tr>
				<td class="even" style="border-bottom:0px" colspan="2">
					{tr}Include the image in a tiki page using the following syntax:{/tr}
				</td>
			</tr>
			<tr>
				<td width="6px" style="border:0px">
				</td>
				<td style="border:0px">
					<code>
						{if $resultscale == $defaultscale}
							{literal}{{/literal}img id={$imageId}{literal}}{/literal}
						{elseif !$resultscale}
							{literal}{{/literal}img id={$imageId}&amp;scalesize=0){literal}}{/literal}
						{else}
							{literal}{{/literal}img id={$imageId}&amp;scaled&amp;scalesize={$resultscale}{literal}}{/literal}
						{/if}
					</code>
				</td>
			</tr>
			<tr>
				<td class="even" style="border-bottom:0px" colspan="2">
					{tr}To include the image in an HTML page:{/tr}
				</td>
			</tr>
			<tr>
				<td width="10px" style="border:0px"> </td>
				<td style="border:0px">
					<code>
						{if $resultscale == $defaultscale}
							&lt;img src="{$url_show}?id={$imageId}" /&gt;
						{elseif !$resultscale}
							&lt;img src="{$url_show}?id={$imageId}&amp;scalesize=0" /&gt;
						{else}
							&lt;img src="{$url_show}?id={$imageId}&amp;scalesize={$resultscale}" /&gt;
						{/if}
					</code>
				</td>
			</tr>
			<tr>
				<td class="even" style="border-bottom:0px" colspan="2">
					{tr}To link to this page from another tiki page:{/tr}
				</td>
			</tr>
			<tr>
				<td width="6px" style="border:0px"> </td>
				<td style="border:0px">
					<code>{literal}[{/literal}tiki-browse_image.php?imageId={$imageId}{literal}]{/literal}</code>
				</td>
			</tr>
		</table>
	</div>
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
