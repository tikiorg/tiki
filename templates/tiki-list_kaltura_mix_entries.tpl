{* $Id$ *}
{if $cant > 0}
	<div class="table-responsive">
		<table class="table" id="selectable">
			<tr>
				<th width="100">&nbsp;</th>
				<th width="150"><a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-name'}asc_name{else}desc_name{/if}">{tr}Name{/tr}</a></th>
				<th width="100"><a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-createdAt'}asc_createdAt{else}desc_createdAt{/if}">{tr}Created{/tr}</a></th>
				<th>{tr}Tags{/tr}</th>
				<th width="100"><a href="tiki-list_kaltura_entries.php?list={$entryType}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq '-modifiedAt'}asc_modifiedAt{else}desc_modifiedAt{/if}">{tr}Modified{/tr}</a></th>
				<th width="20"><a href='#'{popup fullhtml="1" text=$smarty.capture.other_sorts} title="{tr}Other Sorts{/tr}">{icon name='list' alt="{tr}Other Sorts{/tr}"}</a></th>
			</tr>

			{foreach from=$klist key=key item=item}
				{if $item->id ne ''}
					<tr{if ($key % 2)} class="odd"{else} class="even"{/if}>

						{include file='tiki-list_kaltura_entries_actions.tpl'}

						<td class="text"><a href="#" title="{tr}Thumbnail{/tr}" {popup fullhtml="1" text=$smarty.capture.actions}><img class="athumb" src="{$item->thumbnailUrl}" alt="{$item->description}" height="80" width="120"></a></td>
						<td class="text"><a href="tiki-kaltura_video.php?{$entryType}Id={$item->id}"><img src="img/icons/application_form_magnify.png" class="icon"> {$item->name}</a></td>
						<td class="text">{$item->createdAt}</td>
						<td class="text">{$item->tags}</td>
						<td class="text">{$modifiedAt[$key]}
							{if !$prefs.kaltura_kuser}
								<br/><br/>
								{tr}Modified By:{/tr} {$modifiedBy[$key]}
							{/if}
						</td>
						{include file='tiki-list_kaltura_entries_add_info.tpl'}
						<td class="text"><a href="#" title="{tr}Information{/tr}" {popup trigger="onmouseover" fullhtml="1" sticky=true text=$smarty.capture.add_info left=true}>{icon name='information' class='' title=''}</a></td>
					</tr>
				{/if}
			{/foreach}
		</table>
	</div>
{else}
	{remarksbox type="info" title="{tr}No entries{/tr}"}
		{tr}No mix entries found.{/tr}
	{/remarksbox}
{/if}
