{if $popup ne ""}

  {if $slideshow_p ne ''}
    {if $previmg ne '' && $desp ne 0}
    <META HTTP-EQUIV="Refresh" CONTENT="{$slideshow_p};tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$previmg}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}&amp;popup={$popup}&amp;slideshow_p={$slideshow_p}">
    {/if}
  {/if}
  {if $slideshow_n ne ''}
    {if $nextimg ne ''}
    <META HTTP-EQUIV="Refresh" CONTENT="{$slideshow_n};tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$nextdesp}&amp;galleryId={$galleryId}&amp;imageId={$nextimg}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}&amp;popup={$popup}&amp;slideshow_n={$slideshow_n}">
    {/if}
  {/if}
  

{/if}

{if $popup eq ""}
  <h1><a class="pagetitle" href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}">{tr}Browsing Image{/tr}: {$name}</a></h1>
    <a class="linkbut" href="tiki-browse_gallery.php?galleryId={$galleryId}">{tr}return to gallery{/tr}</a>
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
      <a class="linkbut" href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$imageId}">{tr}edit image{/tr}</a>
    {/if}
{/if}  


{if $popup ne "" }
  <div style="vertical-align: middle" height="{$winy}" align="center">
		{if $imageId ne $firstId}
			<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp=0&amp;galleryId={$galleryId}&amp;imageId={$firstId}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}&amp;popup={$popup}" class="gallink">{html_image file='img/icons2/nav_first.gif' border='0' alt='{tr}first image{/tr}' title='{tr}first image{/tr}'}</a>    
		{/if}
    {if $scaleinfo.prevscale}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;scaled&amp;scalesize={$scaleinfo.prevscale}&amp;popup={$popup}" class="gallink">{html_image file='img/icons2/down.gif' border='0' alt='{tr}smaller{/tr}' title='{tr}smaller{/tr}'}</a>
    {/if}
    {if $itype ne 'o'}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;popup={$popup}" class="gallink">{html_image file='img/icons2/nav_dot.gif' border='0' alt='{tr}original size{/tr}' title='{tr}original size{/tr}'}</a>
    {/if}
    {if $scaleinfo.nextscale}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;scaled&amp;scalesize={$scaleinfo.nextscale}&amp;popup={$popup}" class="gallink">{html_image file='img/icons2/up.gif' border='0' alt='{tr}bigger{/tr}' title='{tr}bigger{/tr}'}</a>
    {/if}

    {if $previmg ne '' && $desp ne 0}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$previmg}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}&amp;popup={$popup}&amp;slideshow_p=5" class="gallink">{html_image file='img/icons2/nav_prev.gif' border='0' alt='{tr}slideshow_p{/tr}' title='{tr}slideshow backward{/tr}'}</a>
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$previmg}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}&amp;popup={$popup}" class="gallink">{html_image file='img/icons2/nav_dot_right.gif' border='0' alt='{tr}prev image{/tr}' title='{tr}prev image{/tr}'}</a>
    {/if}

    {if $nextimg ne ''}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$nextdesp}&amp;galleryId={$galleryId}&amp;imageId={$nextimg}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}&amp;popup={$popup}" class="gallink">{html_image file='img/icons2/nav_dot_left.gif' border='0' alt='{tr}next image{/tr}' title='{tr}next image{/tr}'}</a>
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$nextdesp}&amp;galleryId={$galleryId}&amp;imageId={$nextimg}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}&amp;popup={$popup}&amp;slideshow_n=5" class="gallink">{html_image file='img/icons2/nav_next.gif' border='0' alt='{tr}slideshow_n{/tr}' title='{tr}slideshow forward{/tr}'}</a>
    {/if}
		{if $imageId ne $lastId}
    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$lastdesp}&amp;galleryId={$galleryId}&amp;imageId={$lastId}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}&amp;popup={$popup}" class="gallink">{html_image file='img/icons2/nav_last.gif' border='0' alt='{tr}last image{/tr}' title='{tr}last image{/tr}'}</a>    
		{/if}
  </div>
  <br />
{/if}   

  <div class="showimage" {if ($popup) }style="height: 400px"{/if}>
  
  
  {literal}  
<script language="JavaScript" type="text/JavaScript">
		// (C) 2000 www.CodeLifter.com
		// http://www.codelifter.com
		// Free for all users, but leave in this  header
		// NS4-6,IE4-6
		// Fade effect only in IE; degrades gracefully
		var stopstatus = 0
		
		// Set slideShowSpeed (milliseconds)
		var slideShowSpeed = 5000
		
		// Duration of crossfade (seconds)
		var crossFadeDuration = 3
		
		// Specify the image files
		var Pic = new Array() // don't touch this
		// to add more images, just continue
		// the pattern, adding to the array below
		{/literal}
		
			{foreach from=$slide_show item=item}
			{foreach from=$item item=item_ok key=key}
		
			Pic[{$key}] = 'show_image.php?id={$item_ok.imageId}&amp;nocount=y'
			
		{/foreach}
		{/foreach}
		
		
		var t
		var j = 1
		var keyPic = 'show_image.php?id={$imageId}&amp;nocount=y'
		{literal}
		var p = Pic.length
		var pos = j
		var preLoad = new Array()
		
		function preLoadPic(index){
  			if (Pic[index] != ''){
				window.status='Loading : '+Pic[index]
				preLoad[index] = new Image()
				preLoad[index].src = Pic[index]
				Pic[index] = ''
				window.status=''
  			}
		}
		
		function runSlideShow(){
	  		if (stopstatus != '1'){
				if (document.all){
	  				document.images.zImage.style.filter="blendTrans(duration=2)"
	  				document.images.zImage.style.filter= "blendTrans(duration=crossFadeDuration)"
	      			document.images.zImage.filters.blendTrans.Apply()
				}
				document.images.zImage.src = preLoad[j].src
				if (document.all){
	  				document.images.zImage.filters.blendTrans.Play()
				}
				pos = j
				j = j + 1
				if (j > (p-1)) j=0
				t = setTimeout('runSlideShow()', slideShowSpeed)
				preLoadPic(j)
  			}
		}

		function endSlideShow(){
  			stopstatus = 1
  			document.images.zImage.src = keyPic
		}

		preLoadPic(j)
		
		</script>
  
		{/literal}
  
  
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    
    <tr>
  	<td align="center" valign="middle">
  		<div align="center">
  		<a href="javascript:stopstatus=0;runSlideShow();blocking('details', 'block')">[{tr}play{/tr}]</a>
  		<a href="javascript:endSlideShow();blocking('details', 'block')" >[{tr}stop{/tr}]</a>
  		<br />
  		<br />
  		</div>
  	</td>
  </tr>
  </table>
    		
<div align="center"><img src="show_image.php?id={$imageId}&amp;nocount=y" alt="" border="1" name="zImage" onClick="javscript:window.close()"> </center> 
  

  {*
  
  old code of slideshow
  	{if $itype eq 'o'}
    	<img src="show_image.php?id={$imageId}&amp;nocount=y" alt="{tr}image{/tr}" />
    {else}
	    <a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$desp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;scalesize={$scaleinfo.nextscale}&amp;scaled" title="{tr}Click to zoom{/tr}">
	    <img src="show_image.php?id={$imageId}&amp;scaled&amp;scalesize={$scalesize}&amp;nocount=y" alt="{tr}image{/tr}" /></a>
    {/if}
    *}
  </div>
  
{if $popup eq ""}
	<div align="center" >
			{if $imageId ne $firstId}
				<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp=0&amp;galleryId={$galleryId}&amp;imageId={$firstId}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}" class="gallink">{html_image file='img/icons2/nav_first.gif' border='0' alt='{tr}first image{/tr}' title='{tr}first image{/tr}'}</a>
			{/if}
	    {if $scaleinfo.prevscale}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$desp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;scaled&amp;scalesize={$scaleinfo.prevscale}" class="gallink">{html_image file='img/icons2/down.gif' border='0' alt='{tr}smaller{/tr}' title='{tr}smaller{/tr}'}</a>
	    {/if}
	    {if $itype ne 'o'}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$desp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}" class="gallink">{html_image file='img/icons2/nav_dot.gif' border='0' alt='{tr}original size{/tr}' title='{tr}original size{/tr}'}</a>
	    {/if}
	    {if $scaleinfo.nextscale}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$desp}&amp;galleryId={$galleryId}&amp;imageId={$imageId}&amp;scaled&amp;scalesize={$scaleinfo.nextscale}" class="gallink">{html_image file='img/icons2/up.gif' border='0' alt='{tr}bigger{/tr}' title='{tr}bigger{/tr}'}</a>
	    {/if}
	    {if $previmg ne '' && $desp ne 0}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$prevdesp}&amp;galleryId={$galleryId}&amp;imageId={$previmg}{if $itype eq 's'}&amp;scaled&amp;scalesize={$scalesize}{/if}" class="gallink">{html_image file='img/icons2/nav_dot_right.gif' border='0' alt='{tr}prev image{/tr}' title='{tr}prev image{/tr}'}</a>
	    {/if}
            {if $defaultscale eq 'o'}
	        <a {jspopup height="$winy" width="$winx" href="tiki-browse_image.php?offset=$offset&amp;sort_mode=$sort_mode&amp;desp=$nextdesp&amp;galleryId=$galleryId&amp;imageId=$imageId&amp;popup=1"} class="gallink">
            {else}
	        <a {jspopup height="$winy" width="$winx" href="tiki-browse_image.php?offset=$offset&amp;sort_mode=$sort_mode&amp;desp=$nextdesp&amp;galleryId=$galleryId&amp;imageId=$imageId&amp;scaled&amp;scalesize=$defaultscale&amp;popup=1"} class="gallink">
            {/if}{html_image file='img/icons2/admin_unhide.gif' border='0' alt='{tr}Popup window{/tr}' title='{tr}popup window{/tr}'}</a>
	    {if $nextimg ne ''}
	    	<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$nextdesp}&amp;galleryId={$galleryId}&amp;imageId={$nextimg}{if $itype eq 's'}&amp;scaled&amp;scalesize={$scalesize}{/if}" class="gallink">{html_image file='img/icons2/nav_dot_left.gif' border='0' alt='{tr}next image{/tr}' title='{tr}next image{/tr}'}</a>
	    {/if}
	   	{if $imageId ne $lastId}
				<a href="tiki-browse_image.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;desp={$lastdesp}&amp;galleryId={$galleryId}&amp;imageId={$lastId}{if $itype=='s'}&amp;scaled&amp;scalesize={$scalesize}{/if}" class="gallink">{html_image file='img/icons2/nav_last.gif' border='0' alt='{tr}last image{/tr}' title='{tr}last image{/tr}'}</a>    
			{/if}
	</div>
{/if}

  
{if $popup eq ""}
	  <br /><br />
      <table class="normal">
      <tr><td class="odd">{tr}Image Name{/tr}:</td><td class="odd">{$name}</td></tr>
      <tr><td class="even">{tr}Created{/tr}:</td><td class="even">{$created|tiki_long_datetime}</td></tr>
      <tr><td class="odd">{tr}Hits{/tr}:</td><td class="odd">{$hits}</td></tr>
      <tr><td class="even">{tr}Description{/tr}:</td><td class="even">{$description}</td></tr>
      {if $feature_maps eq 'y'}
  		<tr><td class="odd">{tr}Latitude (WGS84/decimal degrees){/tr}:</td><td class="odd">{$lat|escape}</td></tr>
  		<tr><td class="even">{tr}Longitude (WGS84/decimal degrees){/tr}:</td><td class="even">{$lon|escape}</td></tr>
  		{/if}
      <tr><td class="odd">{tr}Author{/tr}:</td><td class="odd">{$image_user}</td></tr>
      {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
        <tr><td class="even">{tr}Move image{/tr}:</td><td class="odd">
        <form action="tiki-browse_image.php" method="post">
        <input type="hidden" name="imageId" value="{$imageId|escape}"/>
        <input type="hidden" name="galleryId" value="{$galleryId|escape}"/>
        <select name="newgalleryId">
          {section name=idx loop=$galleries}
            <option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
          {/section}
        </select>
        <input type="submit" name="move_image" value="{tr}move{/tr}" />
        </form>
        </td></tr>
      {/if}
    </table>
<br /><br />    
  <table class="normal">
  <tr>
  	<td class="even">
  	<small>
    {tr}You can view this image in your browser using{/tr}:<br /><br />
    <a class="gallink" href="{$url_browse}?imageId={$imageId}">{$url_browse}?imageId={$imageId}</a><br />
    </small>
    </td>
  </tr>
  <tr>
    <td class="even">
    <small>
    {tr}You can include the image in an HTML page using one of these lines{/tr}:<br /><br />
    {if $itype eq 'o'}
    &lt;img src="{$url_show}?id={$imageId}" /&gt;<br />
    &lt;img src="{$url_show}?name={$name|escape}" /&gt;<br />
    {else}
    &lt;img src="{$url_show}?id={$imageId}&amp;scaled&amp;scalesize={$scalesize}" /&gt;<br />
    &lt;img src="{$url_show}?name={$name|escape}&amp;scaled&amp;scalesize={$scalesize}" /&gt;<br />
    {/if}
    </small>
    </td>
  </tr>
  <tr>
    <td class="even">
    <small>
    {tr}You can include the image in a tiki page using one of these lines{/tr}:<br /><br />
    {if $itype eq 'o'}
    {literal}{{/literal}img src={$url_show}?id={$imageId} {literal}}{/literal}<br />
    {literal}{{/literal}img src={$url_show}?name={$name|escape} {literal}}{/literal}<br />
    {else}
    {literal}{{/literal}img src={$url_show}?id={$imageId}&amp;scaled&amp;scalesize={$scalesize} {literal}}{/literal}<br />
    {literal}{{/literal}img src={$url_show}?name={$name|escape}&amp;scaled&amp;scalesize={$scalesize} {literal}}{/literal}<br />
    {/if}
    </small>
    </td>
  </tr>
  </table>
{/if}  

