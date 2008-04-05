{* $Id$ *}
{if $prefs.gal_image_mouseover neq 'n'}{popup_init src="lib/overlib.js"}{/if}
<h1><a class="pagetitle" href="tiki-browse_gallery.php?galleryId={$galleryId}">
{tr}Browsing Gallery{/tr}: {$name}
</a></h1>

<div class="navbar">
{if $tiki_p_list_image_galleries eq 'y'}
<span class="button2"><a href="tiki-galleries.php" class="linkbut" title="{tr}List Galleries{/tr}">{tr}List Galleries{/tr}</a></span>
{/if}
{if $system eq 'n'}
  {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
    <span class="button2"><a href="tiki-galleries.php?edit_mode=1&amp;galleryId={$galleryId}" class="linkbut">{tr}Edit Gallery{/tr}</a></span>
    <span class="button2"><a href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;rebuild={$galleryId}" class="linkbut">{tr}Rebuild Thumbnails{/tr}</a></span>
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
      <span class="button2"><a href="tiki-upload_image.php?galleryId={$galleryId}" class="linkbut">{tr}Upload Image{/tr}</a></span>
    {/if}
  {/if}
  {if $prefs.feature_gal_batch eq "y" and $tiki_p_batch_upload_image_dir eq 'y'}
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
      <span class="button2"><a href="tiki-batch_upload.php?galleryId={$galleryId}" class="linkbut">{tr}Directory batch{/tr}</a></span>
    {/if}
  {/if}
{/if}

{if $tiki_p_admin_galleries eq 'y'}
<span class="button2"><a href="tiki-list_gallery.php?galleryId={$galleryId}" class="linkbut">{tr}List Gallery{/tr}</a></span>
<span class="button2"><a href="tiki-show_all_images.php?id={$galleryId}" class="linkbut">{tr}All Images{/tr}</a></span>
{/if}
{if $prefs.rss_image_gallery eq 'y'}
  <span class="button2"><a href="tiki-image_gallery_rss.php?galleryId={$galleryId}" class="linkbut">{tr}RSS{/tr}</a></span>
{/if}
	{if $user and $prefs.feature_user_watches eq 'y'}
		{if $user_watching_gal eq 'n'}
			<a href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;watch_event=image_gallery_changed&amp;watch_object={$galleryId}&amp;watch_action=add" title="{tr}Monitor this Gallery{/tr}">{html_image file='img/icons/icon_watch.png' border='0' alt="{tr}Monitor this Gallery{/tr}"}</a>
		{else}
			<a href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;watch_event=image_gallery_changed&amp;watch_object={$galleryId}&amp;watch_action=remove" title="{tr}Stop Monitoring this Gallery{/tr}">{html_image file='img/icons/icon_unwatch.png' border='0' alt="{tr}Stop Monitoring this Gallery{/tr}"}</a>
		{/if}
	{/if}
</div>

<div class="navbar" align="right">
    {if $user and $prefs.feature_user_watches eq 'y'}
        {if $category_watched eq 'y'}
            {tr}Watched by categories{/tr}:
            {section name=i loop=$watching_categories}
			    <a href="tiki-browse_categories?parentId={$watching_categories[i].categId}">{$watching_categories[i].name}</a>&nbsp;
            {/section}
        {/if}			
    {/if}
</div>

{if $advice}
<div class="highlight simplebox">{tr}{$advice}{/tr}</div>
{/if}

{if strlen($description) > 0}
	<div class="imgaldescr">
	  {$description}
  </div>
{/if}

	<span class="sorttitle">{tr}Sort Images by{/tr}</span>
    [ <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Size{/tr}</a></span> ]


  <div class="thumbnails">
    <table class="galtable"  cellpadding="0" cellspacing="0">
      <tr>
        {if $num_objects > 0}
        {foreach from=$subgals key=key item=item}
          <td align="center" {if (($key / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
          &nbsp;&nbsp;<br />
          <a href="tiki-browse_gallery.php?galleryId={$item.galleryId}"><img alt="{tr}subgallery{/tr} {$item.name}" class="athumb" src="show_image.php?id={$item.imageId}&amp;thumb=1" /></a>
	  <br />
	  <small class="caption">
		{tr}Subgallery{/tr}: 
			{if $showname=='y' || $showfilename=='y'}{$item.name}<br />{/if}
			{if $showimageid=='y'}{tr}ID{/tr}: {$item.galleryId}<br />{/if}
			{if $showdescription=='y'}{$item.description}<br />{/if}
			{if $showcreated=='y'}{tr}Created{/tr}: {$item.created|tiki_short_date}<br />{/if}
			{if $showuser=='y'}{tr}User{/tr}: {$item.user|userlink}<br />{/if}
			{if $showxysize=='y' || $showfilesize=='y'}({$item.numimages} Images){/if}
			{if $showhits=='y'}[{$item.hits} {if $item.hits == 1}{tr}Hit{/tr}{else}{tr}Hits{/tr}{/if}]<br />{/if}
                        </small>
	  </td>
         {if $key%$rowImages eq $rowImages2}
           </tr><tr>
         {/if}
        {/foreach}
        {foreach from=$images key=key item=item}
          <td align="center" {if ((($key +$num_subgals) / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
          &nbsp;&nbsp;<br />
{if $prefs.feature_lightbox eq 'y' or $prefs.feature_shadowbox eq 'y'}
	<a href="show_image.php?id={$item.imageId}&amp;scalesize={$defaultscale}" rel="lightbox[gallery];type=img" title="{if $item.description neq ''}{$item.description}{elseif $item.name neq ''}{$item.name}{else}{$item.filename}{/if}" {if $prefs.gal_image_mouseover neq 'n'}{popup fullhtml="1" text=$over_info.$key|escape:"javascript"|escape:"html"}{/if} class="linkmenu">
	   <img class="athumb" src="show_image.php?id={$item.imageId}&amp;thumb=1" />
	</a>
{else}
	<a href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;imageId={$item.imageId}&amp;scalesize={$defaultscale}" {if $prefs.gal_image_mouseover neq 'n'}{popup fullhtml="1" text=$over_info.$key|escape:"javascript"|escape:"html"}{/if} class="linkmenu">
	   <img class="athumb" src="show_image.php?id={$item.imageId}&amp;thumb=1" />
	   </a>
{/if}
          <br />

          <small class="caption">
	  {if $prefs.gal_image_mouseover neq 'only'}
		{if $showname=='y'}{$item.name}<br />{/if}
		{if $showfilename=='y'}{tr}Filename{/tr}: {$item.filename}<br />{/if}
		{if $showimageid=='y'}{tr}ID{/tr}: {$item.imageId}<br />{/if}
		{if $showdescription=='y'}{$item.description}<br />{/if}
		{if $showcreated=='y'}{tr}Created{/tr}: {$item.created|tiki_short_date}<br />{/if}
		{if $showuser=='y'}{tr}User{/tr}: {$item.user|userlink}<br />{/if}
		{if $showxysize=='y'}({$item.xsize}x{$item.ysize}){/if}
		{if $showfilesize=='y'}({$item.filesize} Bytes){/if}
		{if $showhits=='y'}[{$item.hits} {if $item.hits == 1}{tr}Hit{/tr}{else}{tr}Hits{/tr}{/if}]{/if}
	  {else}
	  	{if $showname=='y' and $item.name neq ''}{$item.name}{else}{$item.filename}{/if}
	  {/if}
	  <br />
          {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
	    		{if $nextx!=0}
            		<a class="gallink" href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;imageId={$item.imageId}&amp;scalesize=0" title="{tr}Original Size{/tr}"><img src='img/icons2/nav_dot.gif' border='0' width='8' height='11' alt='{tr}Original Size{/tr}' title='{tr}Original Size{/tr}' /></a>
	    		{/if}
            	{if $imagerotate}
            		<a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;rotateright={$item.imageId}" title="{tr}rotate right{/tr}"><img src='img/icons2/admin_rotate.gif' border='0' width='11' height='11' alt='{tr}rotate{/tr}' title='{tr}rotate{/tr}' /></a>
            	{/if}
            	<a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;remove={$item.imageId}" title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
            	<a class="gallink" href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$item.imageId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
          {/if}
          <a {jspopup href="tiki-browse_image.php?galleryId=$galleryId&amp;sort_mode=$sort_mode&amp;imageId=`$item.imageId`&amp;scalesize=$defaultscale&amp;popup=1"} class="gallink">
{icon _id='layers' alt='{tr}popup{/tr}'}</a>
          <br />
	</small>
         </td>
         {if ($key + $num_subgals) % $rowImages eq $rowImages2}
           </tr><tr>
         {/if}
        {/foreach}
        {else}
          <tr><td colspan="6">
            <p class="norecords">{tr}No records found{/tr}</p>
          </td></tr>
        {/if}
    </table>
  </div>

{pagination_links cant=$cant step=$maxImages offset=$offset}{/pagination_links}

<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}
   <form method="get" action="tiki-browse_gallery.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="galleryId" value="{$galleryId}" />
   </form>
   </td>
</tr>
</table>
{if $prefs.feature_image_galleries_comments == 'y'
  && (($tiki_p_read_comments == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
<div id="page-bar">
<table>
<tr><td>
<div class="button2">
<a href="#" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}Add Comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
</div>
</td></tr></table>
</div>
{include file=comments.tpl}
{/if}
