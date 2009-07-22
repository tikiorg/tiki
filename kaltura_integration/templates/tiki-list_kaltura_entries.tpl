{title}{tr}Kaltura Entries:{/tr}&nbsp;{$name}{/title}

<div id="{$rootid}browse_image">
	<div class="navbar">
	</div>
	
	<script type="text/javascript" src="lib/overlib.js"></script>

    {capture name=other_sorts}{strip}
    <div class='opaque'>
    	<div class='box-title'>{tr}Other Sorts{/tr}</div>
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
	
	<table class="normal">
		<tr>
			<th>&nbsp;</th>
			<th><a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-name'}asc_name{else}desc_name{/if}">{tr}Name{/tr}</a></th>
			<th><a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-media_type'}asc_media_type{else}desc_media_type{/if}">{tr}Media Type{/tr}</a></th>
			<th><a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-created_at'}asc_created_at{else}desc_created_at{/if}">{tr}Created{/tr}</a></th>
			<th><a>{tr}Tags{/tr}</a></th>
			<th><a href="tiki-list_kaltura_entries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq '-modified_at'}asc_modified{else}desc_modified_at{/if}">{tr}Modified{/tr}</a></th>
			<th><a>{tr}Version{/tr}</a></th>
			<th><a href='#'{popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" text=$smarty.capture.other_sorts|escape:"javascript"|escape:"html" "html"} title='{tr}Other Sorts{/tr}'>{icon _id='timeline_marker' alt='{tr}Other Sorts{/tr}' title=''}</a></th>
		</tr>
		
		{foreach from=$entries key=key item=item}
		<tr {if ($key % 2)}class="odd"{else}class="even"{/if}>
			<td><img class="athumb" src={$item.thumbnailUrl} alt="{$item.description}"  height="80" width="120"/></td>
			<td><a href={$item.downloadUrl}>{$item.name}</a></td>
			<td>{$item.mediaType|kaltura_media_type}</td>
			<td>{$item.createdAt|kaltura_date_format}</td>
			<td>{$item.tags}</td>
			<td>{$item.modifiedAt|kaltura_date_format}</td>
			<td>{$item.version}</td>

	{capture name=add_info}{strip}
			<div class='opaque'>
			<div class='box-title'>{tr}Additional Info{/tr}</div>
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
	</table>
  
	<br />

	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

</div>
