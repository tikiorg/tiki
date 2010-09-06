{title}{tr}Edit Video Information{/tr}{/title}

<div class="navbar">
	{button href="tiki-browse_video_gallery.php" _auto_args='galleryId' _text="{tr}Return to Gallery{/tr}"}
	{button href="tiki-browse_video.php?videoId=$videoId" _text="{tr}Browse Videos{/tr}"}
</div>

<div align="center">
{if $show eq 'y'}
<br />
<hr/>
<h2>{tr}Edit successful!{/tr}</h2>
<h3>{tr}The following video was successfully edited{/tr}:</h3>
<hr/>
<br />
{/if}
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

<br /><br />

<form enctype="multipart/form-data" action="tiki-edit_video.php" method="post">
<input type="hidden" name="edit" value="{$videoId|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="galleryId" value="{$galleryId|escape}" />
<table class="formcolor">
<tr><td>{tr}Video Title{/tr}:</td><td><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td>{tr}Description{/tr}:</td><td><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></td></tr>
<tr><td>{tr}Tags{/tr}:</td><td><input typr="text" name="tags" size="80" value="{$tags}" /></td></tr>
{include file=categorize.tpl}
<tr><td>&nbsp;</td><td><input type="submit" name="editvideo" value="{tr}Save{/tr}" />&nbsp;&nbsp;{button href="tiki-browse_video.php?videoId=$videoId" _text="{tr}Cancel Edit{/tr}"}</td></tr>
</table>
</form>

<br />
<br /><br />    
  <table class="normal">
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
    {tr}You can include the video in an HTML page using one of these lines{/tr}:<br /><br />
    
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
</div>
