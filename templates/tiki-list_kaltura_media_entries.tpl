{* $Id$ *}
{if $cant > 0}
<div class="table-responsive">
<table class="table normal">
	<tr>
		<th width="100">&nbsp;</th>
		<th width="150"><a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-name'}asc_name{else}desc_name{/if}">{tr}Name{/tr}</a></th>
		<th width="100"><a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-created_at'}asc_createdAt{else}desc_createdAt{/if}">{tr}Created{/tr}</a></th>
		<th>{tr}Added by{/tr}</th>
		<th>{tr}Tags{/tr}</th>
		<th width="50"><a href='#'{popup trigger="onclick" sticky=1 mouseoff=1 fullhtml="1" text=$smarty.capture.other_sorts|escape:"javascript"|escape:"html"} title="{tr}Other Sorts{/tr}">{icon _id='timeline_marker' alt="{tr}Other Sorts{/tr}"}</a></th>
	</tr>		
	{foreach from=$klist key=key item=item}
		{if $item->id ne ''}
	<tr{if ($key % 2)} class="odd"{else} class="even"{/if}>	
			{include file='tiki-list_kaltura_entries_actions.tpl'}

		<td class="text">{if $tiki_p_view_videos eq 'y'}<a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}" title="{tr}View{/tr}">{/if}<img class="athumb" src="{$item->thumbnailUrl}" alt="{$item->description}" height="80" width="120" />{if $tiki_p_view_videos eq 'y'}</a>{/if}</td>
		<td class="text">{if $tiki_p_view_videos eq 'y'}<a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}" title="{tr}View{/tr}">{/if}{$item->name}{if $tiki_p_view_videos eq 'y'}</a>{/if}</td>
		<td class="date">{$item->createdAt|tiki_short_datetime}</td>
		<td class="text">{$item->userId}</td>
		<td class="text">{$item->tags}</td>
			{include file='tiki-list_kaltura_entries_add_info.tpl'}	
		<td class="text"><a href="#" title="{tr}Information{/tr}" {popup trigger="onmouseover" fullhtml="1" sticky=true text=$smarty.capture.add_info|escape:"javascript"|escape:"html" left=true}>{icon _id='information' class='' title='{tr}More Information{/tr}'}</a><a href="#" title="{tr}Actions{/tr}" {popup trigger="onclick" sticky=1 fullhtml="1" text=$smarty.capture.actions|escape:"javascript"|escape:"html"}>{icon _id='wrench' class='' title='{tr}Actions{/tr}'}</a></td>
	</tr>
		{/if}
	{/foreach}
</table>
</div>
{else}
{remarksbox type="info" title="{tr}No entries{/tr}"}
{tr}No media entries found.{/tr} <a href="tiki-kaltura_upload.php">{tr}Click here{/tr}</a> {tr}to upload.{/tr}{/remarksbox}
{/if}
