{* $Id: tiki-browse_image.tpl 17641 2009-03-26 14:24:18Z sylvieg $ *}


  {title}{tr}Browsing Video:{/tr}&nbsp;{$name}{/title}

<div id="{$rootid}browse_video">
  <div class="navbar">
		{button href="tiki-browse_video_gallery.php?galleryId=$galleryId&amp;offset=$offset" _text="{tr}Return to Gallery{/tr}"}
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
			{button href="tiki-edit_video.php?galleryId=$galleryId&amp;edit=$videoId&amp;sort_mode=$sort_mode" _text="{tr}Edit Video{/tr}"}
    {/if}
</div>


{capture name=buttons}

{***** when not sliding buttons *****}
  <div align="center" class="noslideshow">

{* --- first image --- *}
	<a href="{$url_base}{$firstId}"
		class="gallink"{if $videoId eq $firstId} style="display: none;"{/if}>{icon _id='resultset_first' alt='{tr}First Video{/tr}'}</a>

{* --- previous image --- *}
	<a href="{$url_base}{$previmg}"
    	class="gallink" style="padding-right:6px;{if !$previmg} display: none;{/if}">    	{icon _id='resultset_previous' alt='{tr}Prev Video{/tr}'}</a>

{* --- next image --- *}
	<a href="{$url_base}{$nextimg}"
    	class="gallink" style="padding-left:6px;{if !$nextimg} display: none;{/if}">    	{icon _id='resultset_next' alt='{tr}Next Video{/tr}'}</a>

{* --- last image --- *}
	<a href="{$url_base}{$lastId}"
		class="gallink"{if $videoId eq $lastId} style="display: none;"{/if}>{icon _id='resultset_last' alt='{tr}Last Video{/tr}'}</a>    
  </div>

{/capture}
{$smarty.capture.buttons}

<object name="kaltura_player" id="kaltura_player" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" height="365" width="400" data="http://www.kaltura.com/index.php/kwidget/wid/_23929/uiconf_id/1000308">
<param name="allowScriptAccess" value="always" />
<param name="allowNetworking" value="all" />
<param name="allowFullScreen" value="true" />
<param name="bgcolor" value="#000000" />
<param name="movie" value="http://www.kaltura.com/index.php/kwidget/wid/_23929/uiconf_id/1000308"/>
<param name="flashVars" value="entryId={$entryId}"/>
<param name="wmode" value="opaque"/>
<a href="http://corp.kaltura.com">video platform</a>
<a href="http://corp.kaltura.com/technology/video_management">video management</a>
<a href="http://corp.kaltura.com/solutions/overview">video solutions</a>
<a href="http://corp.kaltura.com/technology/video_player">free video player</a>
</object>

{$smarty.capture.buttons}
  
  <br /><br />
  
  <table class="normal noslideshow">
	<tr><td class="odd">{tr}Video Title{/tr}:</td><td class="odd">{$name}</td></tr>
	<tr><td class="even">{tr}Created{/tr}:</td><td class="even">{$created|tiki_long_datetime}</td></tr>
	<tr><td class="odd">{tr}Hits{/tr}:</td><td class="odd">{$hits}</td></tr>
	<tr><td class="even">{tr}Description{/tr}:</td><td class="even">{$description}</td></tr>

	{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
	  <tr><td class="even">{tr}Move video{/tr}:</td><td class="odd">
	  <form action="tiki-browse_video.php" method="post">	
		<input type="hidden" name="sort_mode" value="{$sort_mode|escape}"/>
		<input type="hidden" name="videoId" value="{$videoId|escape}"/>
		<input type="hidden" name="galleryId" value="{$galleryId|escape}"/>
		<input type="text" name="newname" value="{$name}" />
		<select name="newgalleryId">
	    {section name=idx loop=$galleries}
	      <option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
	    {/section}
		</select>
		<input type="submit" name="move_video" value="{tr}Move{/tr}" />
	  </form>
	  </td></tr>
	{/if}
  </table>
  <br /><br />    
  <table class="normal noslideshow">
  <tr>
  	<td class="even">
  	<small>
    {tr}You can view this video in your browser using{/tr}:<br /><br />
    <a class="gallink" href="{$url_browse}?videoId={$videoId}">{$url_browse}?videoId={$videoId}</a><br />
    </small>
    </td>
  </tr>
  <tr>
    <td class="even">
    <small>
  
    </small>
    </td>
  </tr>
  <tr>
    <td class="even">
    <small>
    {tr}You can include the video in a tiki page using one of these lines{/tr}:<br /><br />
 
    </small>
    </td>
  </tr>
  </table>  

</div> {* id="{$rootid}browse_video" *}

{if $listVideoId}

<script type='text/javascript'>
<!--
var tmp = window.location.search.match(/delay=(\d+)/);
tmp = tmp ? parseInt(tmp[1]) : 3000;
var thepix = new Diaporama('thepix', [{$listImgId}], {ldelim}
	  startId: {$videoId},
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
