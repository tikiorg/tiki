{* $Id: tiki-browse_gallery.tpl 18023 2009-04-15 23:29:36Z sylvieg $ *}
{if $prefs.gal_image_mouseover neq 'n'}{popup_init src="lib/overlib.js"}{/if}

{title}{tr}Browsing Gallery:{/tr} {$name}{/title}

<div class="navbar">
	{if $tiki_p_list_image_galleries eq 'y'}
		{button href="tiki-video_galleries.php" _text="{tr}List Galleries{/tr}"}
	{/if}
	{if $system eq 'n'}
	
		{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
				{button href="tiki-video_galleries.php?edit_mode=1&amp;galleryId=$galleryId" _text="{tr}Edit Gallery{/tr}"}
				
		{/if}
		
		{if $tiki_p_upload_images eq 'y'}
			{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
				{button href="tiki-upload_video.php?galleryId=$galleryId" _text="{tr}Upload Video{/tr}"}
			{/if}
		{/if}
		
		
		{if $tiki_p_assign_perm_image_gallery eq 'y'}
			{assign var=thisname value=$name|escape:"url"}
			{button href="tiki-objectpermissions.php?objectName=$thisname&amp;objectType=video+gallery&amp;permType=video+galleries&amp;objectId=$galleryId"	_text="{tr}Perms{/tr}"}
		{/if}
	{/if}

</div>

<div class="navbar" align="right">
    {if $user and $prefs.feature_user_watches eq 'y'}
        {if $category_watched eq 'y'}
            {tr}Watched by categories{/tr}:
            {section name=i loop=$watching_categories}
			    <a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}">{$watching_categories[i].name}</a>&nbsp;
            {/section}
        {/if}			
    {/if}
</div>

{if $advice}
<div class="highlight simplebox">{tr}{$advice}{/tr}</div>
{/if}

{if strlen($description) > 0}
	<div class="description">
	  {$description|escape}
  </div>
{/if}

	<span class="sorttitle">{tr}Sort Videos by{/tr}</span>
    [ <span class="sortoption"><a class="gallink" href="{$galleryId|sefurl:videogallery:with_next}offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="{$galleryId|sefurl:videogallery:with_next}offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a></span>
    | <span class="sortoption"><a class="gallink" href="{$galleryId|sefurl:videogallery:with_next}offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Views{/tr}</a></span> ]


  <div class="thumbnails">
    <table class="galtable"  cellpadding="0" cellspacing="0">
      <tr>
        {if $num_objects > 0}
        {foreach from=$subgals key=key item=item}
          <td align="center" {if (($key / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
          &nbsp;&nbsp;<br />
          <a href="{$item.galleryId|sefurl:videogallery}"><img alt="{tr}subgallery{/tr} {$item.name}" class="athumb" src="show_image.php?id={$item.imageId}&amp;thumb=1" /></a>
	  <br />
	  <small class="caption">
		{tr}Subgallery{/tr}: 
			{if $showname=='y'}{$item.name}<br />{/if}
			{if $showvideoid=='y'}{tr}ID{/tr}: {$item.galleryId}<br />{/if}
			{if $showcategories=='y'}
				{tr}Categories{/tr}:
                       		{section name=categ loop=item.categories}
                        		<li>{$item.categories[categ]}</li>
                		{/section}
                		</ul><br />
			{/if}
			{if $showdescription=='y'}{$item.description}<br />{/if}
			{if $showcreated=='y'}{tr}Created{/tr}: {$item.createdAtAsInt|tiki_short_date}<br />{/if}
			{if $showuser=='y'}{tr}User{/tr}: {$item.user|userlink}<br />{/if}
			{if $showhits=='y'}[{$item.views} {if $item.views == 1}{tr}View{/tr}{else}{tr}Views{/tr}{/if}]<br />{/if}
                        </small>
	  </td>
         {if $key%$rowImages eq $rowImages2}
           </tr><tr>
         {/if}
        {/foreach}
 
        {foreach from=$videos key=key item=item}
        
     
          <td align="center" {if ((($key +$num_subgals) / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
          &nbsp;&nbsp;<br />

	<a href="player.php?id={$item.entryId}" rel="lightbox[gallery];type=iframe" title="{if $item.description neq ''}{$item.description}{elseif $item.name neq ''}{$item.name}{else}{$item.filename}{/if}" {if $prefs.gal_image_mouseover neq 'n'}{popup fullhtml="1" text=$over_info.$key|escape:"javascript"|escape:"html"}{/if} class="linkmenu">
	  <img class="athumb" width="{thx}" height="{thy}"src={$item.thumbnailUrl}&amp;thumb=1" />
	</a>

          <br />
	  <small class="caption">
		{if $showname=='y'}{$item.name}<br />{/if}
		
		{if $showvideoid=='y'}{tr}ID{/tr}: {$item.videoId}<br />{/if}
		{if $showcategories=='y'}
		    	{tr}Categories{/tr}:
                        <ul class='categories'>
                        {section name=categ loop=$item.categories}
                        	<li>{$item.categories[categ]}</li>
                        {/section}
                        </ul><br />
                {/if}
		{if $showdescription=='y'}{$item.description}<br />{/if}
		{if $showcreated=='y'}{tr}Created{/tr}: {$item.createdAtAsInt|tiki_short_date}<br />{/if}
		{if $showuser=='y'}{tr}User{/tr}: {$item.user|userlink}<br />{/if}
		{if $showhits=='y'}[{$item.views} {if $item.views == 1}{tr}View{/tr}{else}{tr}Views{/tr}{/if}]{/if}
	
	  <br />
          {if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
	    		
            	<a class="gallink" href="{$galleryId|sefurl:videogallery:with_next}remove={$item.videoId}" title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
            	<a class="gallink" href="tiki-edit_video.php?galleryId={$galleryId}&amp;edit={$item.videoId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
          	<a class="gallink" href="tiki-browse_video.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;videoId={$item.videoId}" {if $prefs.gal_image_mouseover neq 'n'}{popup fullhtml="1" text=$over_info.$key|escape:"javascript"|escape:"html"}{/if}>{icon _id='magnifier' alt='{tr}Details{/tr}'}</a>
          
          {/if}
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

