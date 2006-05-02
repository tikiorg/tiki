<div class="tabt2"> {* div added and buttons moved up *}
<span class="button2"><a href="tiki-galleries.php" class="linkbut" title="{tr}list galleries{/tr}">{tr}list galleries{/tr}</a></span>
{if $system eq 'n'}
  {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
    <span class="button2"><a href="tiki-galleries.php?edit_mode=1&amp;galleryId={$galleryId}" class="linkbut">{tr}edit gallery{/tr}</a></span>
    <span class="button2"><a href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;rebuild={$galleryId}" class="linkbut">{tr}rebuild thumbnails{/tr}</a></span>
  {/if}
  {if $tiki_p_upload_images eq 'y'}
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
      <span class="button2"><a href="tiki-upload_image.php?galleryId={$galleryId}" class="linkbut">{tr}upload image{/tr}</a></span>
    {/if}
  {/if}
  {if $feature_gal_batch eq "y" and $tiki_p_batch_upload_image_dir eq 'y'}
    {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
      <span class="button2"><a href="tiki-batch_upload.php?galleryId={$galleryId}" class="linkbut">{tr}Directory batch{/tr}</a></span>
    {/if}
  {/if}
{/if}

{if $tiki_p_admin_galleries eq 'y'}
<span class="button2"><a href="tiki-list_gallery.php?galleryId={$galleryId}" class="linkbut">{tr}list gallery{/tr}</a></span>
{/if}
{if $rss_image_gallery eq 'y'}
  <span class="button2"><a href="tiki-image_gallery_rss.php?galleryId={$galleryId}" class="linkbut">RSS</a></span>
{/if}
</div>
<h1><a class="pagetitle" href="tiki-browse_gallery.php?galleryId={$galleryId}">
{tr}Browsing Gallery{/tr}: {$name}
</a></h1>

<br /><br />
{if strlen($description) > 0}
	<div class="imgaldescr">
	  {$description}
  </div>
{/if}
<br />

	<span class="sorttitle">{tr}Sort Images by{/tr}</span>
    [ <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Size{/tr}</a></span> ]

<br /><br />
<div align="center">
<div class="mini">
      {if $prev_offset >= 0}
        [<a  class="galprevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;&nbsp;[<a class="galprevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
  </div>
</div>

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
			{if $showuser=='y'}{tr}User{/tr}: <a href="tiki-user_information.php?user={$item.user|escape}">{$item.user}</a><br />{/if}
			{if $showxysize=='y' || $showfilesize=='y'}({$item.numimages} Images){/if}
			{if $showhits=='y'}[{$item.hits} {if $item.hits == 1}{tr}hit{/tr}{else}{tr}hits{/tr}{/if}]<br />{/if}
                        </small>
	  </td>
         {if $key%$rowImages eq $rowImages2}
           </tr><tr>
         {/if}
        {/foreach}
        {foreach from=$images key=key item=item}
          <td align="center" {if ((($key +$num_subgals) / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
          &nbsp;&nbsp;<br />
          
          {if $defaultscale=='o'}
          <a href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;desp={$key}&amp;offset={$offset}&amp;imageId={$item.imageId}"><img alt="thumbnail" class="athumb" src="show_image.php?id={$item.imageId}&amp;thumb=1" /></a>
	  {else}
          <a href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;desp={$key}&amp;offset={$offset}&amp;imageId={$item.imageId}&amp;scaled&amp;scalesize={$defaultscale}"><img alt="thumbnail" class="athumb" src="show_image.php?id={$item.imageId}&amp;thumb=1" /></a>
	  {/if}
          <br />
          <small class="caption">
		{if $showname=='y'}{$item.name}<br />{/if}
		{if $showfilename=='y'}{tr}Filename{/tr}: {$item.filename}<br />{/if}
		{if $showimageid=='y'}{tr}ID{/tr}: {$item.imageId}<br />{/if}
		{if $showdescription=='y'}{$item.description}<br />{/if}
		{if $showcreated=='y'}{tr}Created{/tr}: {$item.created|tiki_short_date}<br />{/if}
		{if $showuser=='y'}{tr}User{/tr}: <a href="tiki-user_information.php?user={$item.user|escape}">{$item.user}</a><br />{/if}
		{if $showxysize=='y'}({$item.xsize}x{$item.ysize}){/if}
		{if $showfilesize=='y'}({$item.filesize} Bytes){/if}
		{if $showhits=='y'}[{$item.hits} {if $item.hits == 1}{tr}hit{/tr}{else}{tr}hits{/tr}{/if}]{/if}
          <br />
          {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
	    		{if $nextx!=0}
            		<a class="gallink" href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;desp={$key}&amp;offset={$offset}&amp;imageId={$item.imageId}" title="{tr}original size{/tr}"><img src='img/icons2/nav_dot.gif' border='0' width='8' height='11' alt='{tr}original size{/tr}' title='{tr}original size{/tr}' /></a>
	    		{/if}
            	{if $imagerotate}
            		<a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;rotateright={$item.imageId}" title="{tr}rotate right{/tr}"><img src='img/icons2/admin_rotate.gif' border='0' width='11' height='11 alt='{tr}rotate{/tr}' title='{tr}rotate{/tr}' /></a>
            	{/if}
            	<a class="gallink" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;remove={$item.imageId}" title="{tr}delete{/tr}"><img src='img/icons2/admin_delete.gif' border='0' width='11' height='11 alt='{tr}delete{/tr}' title='{tr}delete{/tr}' /></a>
            	<a class="gallink" href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$item.imageId}" title="{tr}edit{/tr}"><img src='img/icons2/admin_move.gif' border='0' width='11' height='11 alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
          {/if}
					{assign var=desp value=$key}
                                        {assign var=THEimageId value=$item.imageId}
          {if $defaultscale eq 'o'}
              <a {jspopup href="tiki-browse_image.php?galleryId=$galleryId&amp;sort_mode=$sort_mode&amp;desp=$desp&amp;offset=$offset&amp;imageId=$THEimageId&amp;popup=1"} class="gallink">
          {else}
              <a {jspopup href="tiki-browse_image.php?galleryId=$galleryId&amp;sort_mode=$sort_mode&amp;desp=$desp&amp;offset=$offset&amp;imageId=$THEimageId&amp;scaled&amp;scalesize=$defaultscale&amp;popup=1"} class="gallink">
          {/if}
<img src='img/icons2/admin_unhide.gif' border='0' width='11' height='11 alt='{tr}popup{/tr}' title='{tr}popup{/tr}' /></a>
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
      </tr>
    </table>
  </div>

<div align="center">
<div class="mini">
      {if $prev_offset >= 0}
        [<a  class="galprevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;&nbsp;
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;&nbsp;[<a class="galprevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
      {if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxImages}
<a class="prevnext" href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
  </div>
</div>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}
   <form method="get" action="tiki-browse_gallery.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="galleryId" value="{$galleryId}" />
   </form>
   </td>
</tr>
</table>
{if $feature_image_galleries_comments == 'y'
  && (($tiki_p_read_comments == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
<div id="page-bar">
<table>
<tr><td>
<div class="button2">
      <a href="#comments" onclick="javascript:flip('comzone{if $comments_show eq 'y'}open{/if}');" class="linkbut">
	{if $comments_cant == 0}
          {tr}add comment{/tr}
        {elseif $comments_cant == 1}
          <span class="highlight">{tr}1 comment{/tr}</span>
        {else}
          <span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
        {/if}
      </a>
</div>
</td></tr></table>
</div>
{include file=comments.tpl}
{/if}
