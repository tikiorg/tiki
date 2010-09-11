 {if $cant > 0}
<table class="normal">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="100">&nbsp;</th>
			<th width="150"><a href="tiki-list_kaltura_entries.php?list={$entryType}offset={$offset}&amp;sort_mode={if $sort_mode eq '-name'}asc_name{else}desc_name{/if}">{tr}Name{/tr}</a></th>
			<th width="100"><a href="tiki-list_kaltura_entries.php?list={$entryType}offset={$offset}&amp;sort_mode={if $sort_mode eq '-media_type'}asc_media_type{else}desc_media_type{/if}">{tr}Media Type{/tr}</a></th>
			<th width="100"><a href="tiki-list_kaltura_entries.php?list={$entryType}offset={$offset}&amp;sort_mode={if $sort_mode eq '-created_at'}asc_created_at{else}desc_created_at{/if}">{tr}Created{/tr}</a></th>
			<th><a>{tr}Tags{/tr}</a></th>
			<th width="30"><a href='#'{popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" text=$smarty.capture.other_sorts|escape:"javascript"|escape:"html"} title="{tr}Other Sorts{/tr}">{icon _id='timeline_marker' alt="{tr}Other Sorts{/tr}" title=''}</a></th>
		</tr>		
		{foreach from=$klist key=key item=item}
		{if $item->id ne ''}
		<tr {if ($key % 2)}class="odd"{else}class="even"{/if}>	
			{include file=tiki-list_kaltura_entries_actions.tpl}
			<td><input type="checkbox" name="mediaId[]" value="{$item->id}" /></td>
			<td><a href="#" {popup trigger="onclick" sticky=1 fullhtml="1" text=$smarty.capture.actions|escape:"javascript"|escape:"html"}>
			<img class="athumb" src={$item->thumbnailUrl} alt="{$item->description}"  height="80" width="120"/></a></td>
			<td><a href={$item->downloadUrl}>{$item->name}</a></td>
			<td>{$item->mediaType}</td>
			<td>{$item->createdAt|tiki_short_datetime}</td>
			<td height="100">{$item->tags}</td>
			{include file=tiki-list_kaltura_entries_add_info.tpl}	
			<td><a href="#" {popup trigger="onmouseover" fullhtml="1" sticky=true text=$smarty.capture.add_info|escape:"javascript"|escape:"html" left=true}>{icon _id='information' class='' title=''}</a></td>
		</tr>
		{/if}
		{/foreach}
</table>
{else}
{remarksbox type="info" title="{tr}No entries{/tr}" }
{tr}No media entries found. {/tr}<a href="tiki-kaltura_upload.php">{tr}Click here {/tr}</a>{tr}to upload.{/tr}{/remarksbox}
{/if}
