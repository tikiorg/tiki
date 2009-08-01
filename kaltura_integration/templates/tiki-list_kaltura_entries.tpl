{title}{tr}Kaltura Entries:{/tr}&nbsp;{$name}{/title}

	<script type="text/javascript" src="lib/overlib.js"></script>

    {capture name=other_sorts}{strip}
    <div class='opaque'>
    	<div class='box-title'><b>{tr}Other Sorts{/tr}</b></div>
    		<div class='box-data'>
 			<a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-views'}asc_views{else}desc_views{/if}">{tr}Loads{/tr}</a></th>
			<br />
 			<a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-plays'}asc_plays{else}desc_plays{/if}">{tr}Plays{/tr}</a></th>
		</div>
    </div>
    {/strip}{/capture}
   
	<form method="post" action="{$smarty.server.PHP_SELF}" class="findtable">
	<label class="findtitle">
		{tr}Find{/tr}
		<input type="text" name="find" value="{$find|escape}" />
	</label>
	<label class="findsubmit">
	<input type="submit" name="search" value="{tr}Go{/tr}" />
	</label>
	</form>
	
	<br/>
	<form action='tiki-kaltura_video.php' method='post'>
	<input type="hidden" id="action" name="action" value="" />
	<input type="submit" name="remix" value="{tr}Create Remix{/tr}" onClick="document.getElementById('action').value='remix';"/>
	<input type="submit" name="delete" value="{tr}Delete{/tr}" onClick="document.getElementById('action').value='delete';"/>
	<br><br>
	
	<table class="normal">
	
	{if $cant > 0}
		<tr>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th><a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-name'}asc_name{else}desc_name{/if}">{tr}Name{/tr}</a></th>
			<th><a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-media_type'}asc_media_type{else}desc_media_type{/if}">{tr}Media Type{/tr}</a></th>
			<th><a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-created_at'}asc_created_at{else}desc_created_at{/if}">{tr}Created{/tr}</a></th>
			<th><a>{tr}Tags{/tr}</a></th>
			<th><a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-modified_at'}asc_modified{else}desc_modified_at{/if}">{tr}Modified{/tr}</a></th>
			<th><a>{tr}Version{/tr}</a></th>
			<th><a href='#'{popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" text=$smarty.capture.other_sorts|escape:"javascript"|escape:"html"} title='{tr}Other Sorts{/tr}'>{icon _id='timeline_marker' alt='{tr}Other Sorts{/tr}' title=''}</a></th>
		</tr>
		<form action='tiki-list_kaltura_entries.php' method='post'>
		{foreach from=$entries key=key item=item}
		<tr {if ($key % 2)}class="odd"{else}class="even"{/if}>
		{capture name=actions}{strip}
		<div class='opaque'>
			<div class='box-title'><b>{tr}Actions{/tr}</b></div>
      			<div class='box-data'>

          			{if $tiki_p_view_kaltura_entry eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin}
           				 
           				 <a href="tiki-kaltura_video.php?videoId={$item.id}"><div class ="iconmenu" ><img src="pics/icons/application_form_magnify.png" class="icon" />View</div></a>
          			{/if}
           			{if $tiki_p_edit_kaltura_entry eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin}
           				 
           				 <a href="tiki-kaltura_video.php?videoId={$item.id}&action=edit"><div class ="iconmenu" ><img src="pics/icons/page_edit.png" class="icon"/>Edit</div></a>
          			{/if}
          			{if $tiki_p_remix_kaltura_entry eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin}
           				 <a href="tiki-kaltura_video.php?videoId={$item.id}&action=remix"><div class ="iconmenu" ><img src="pics/icons/layers.png" class="icon"/>Remix</div></a>
           				 {if $item.mediaType == 6}
           				 <a href="tiki-kaltura_video.php?videoId={$item.id}&action=dupl"><div class ="iconmenu" ><img src="pics/icons/layers.png" class="icon"/>Duplicate</div></a>
          				 {/if}
          			{/if}
          			{if $tiki_p_download_kaltura_entry eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin}
           				
           				 <a href="{if $item.downloadUrl ne ''}{$item.downloadUrl}"><div class ="iconmenu" ><img src="pics/icons/disk.png" class="icon"/>Download{else}tiki-kaltura_video.php?videoId={$item.id}&action=download"><div class ="iconmenu" ><img src="pics/icons/disk.png" class="icon"/>Add Download{/if}</div></a>
          			{/if}
          			{if $tiki_p_delete_kaltura_entry eq 'y' or $tiki_p_admin_kaltura eq 'y' or $tiki_p_admin}
           				
           				 <a href="tiki-kaltura_video.php?videoId={$item.id}&action=delete"><div class ="iconmenu" ><img src="pics/icons/cross.png" class="icon"/>Delete</div></a>
          			{/if}
            	</div>
			</div>
		</div>
		{/strip}{/capture}
			<td><input type="checkbox" name="videoId[]" value="{$item.id}" /></td>
			<td><a href="#" {popup trigger="onclick" sticky=1 mouseoff=1 fullhtml="1" text=$smarty.capture.actions|escape:"javascript"|escape:"html"}>
			<img class="athumb" src={$item.thumbnailUrl} alt="{$item.description}"  height="80" width="120"/></a></td>
			<td><a href={$item.downloadUrl}>{$item.name}</a></td>
			<td>{$item.mediaType|kaltura_media_type}</td>
			<td>{$item.createdAt|kaltura_date_format}</td>
			<td>{$item.tags}</td>
			<td>{$item.modifiedAt|kaltura_date_format}</td>
			<td>{$item.version}</td>

	{capture name=add_info}{strip}
			<div class='opaque'>
			<div class='box-title'><b>{tr}Additional Info{/tr}</b></div>
      			<div class='box-data'>

          			{if $item.description eq ''}
           				 {assign var=propval value="No Description"}
          			{else}
           				 {assign var=propval value=$item.description}
          			{/if}
           				 <b>Description</b>: {$propval}<br />
            
          			{if $item.duration eq ''}
          				 {assign var=propval value=0}
         			{else}
           				 {assign var=propval value=$item.duration}
          			{/if}
            			 <b>Duration</b>: {$propval}s<br />
            
          			{if $item.views eq ''}
            			 {assign var=propval value=0}
          			{else}
            			 {assign var=propval value=$item.views}
          			{/if}
            			 <b>Views</b>: {$propval}<br />
            
          			{if $item.plays eq ''}
            			 {assign var=propval value=0}
          			{else}
            			 {assign var=propval value=$item.plays}
          			{/if}
            			 <b>Plays</b>: {$propval}<br />

			</div>
	</div>
	{/strip}{/capture}
	
			<td><a href="#" {popup trigger="onmouseover" fullhtml="1" text=$smarty.capture.add_info|escape:"javascript"|escape:"html" left=true}>{icon _id='information' class='' title=''}</a></td>
		</tr>
		{/foreach}
	
    {else}
    <tr><td class="odd">{tr}No results to display{/tr}</td></tr>
    {/if}
    </table>

	<br />

	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
