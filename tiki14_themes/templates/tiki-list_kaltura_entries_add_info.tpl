{capture name=add_info}{strip}
	<table class="table-condensed">
		{if !empty($item->description)}
			<tr>
				<th class="text-right">{tr}Description{/tr}</th>
				<td>{$item->description}</td>
			</tr>
		{/if}
		<tr>
			<th class="text-right">{tr}Status{/tr}</th>
			<td>{$item->statusString}</td>
		</tr>
		<tr>
			<th class="text-right">{tr}Media Id{/tr}</th>
			<td><pre style="margin:0">{$item->id}</pre></td>
		</tr>
		<tr>
			<th class="text-right">{tr}Media Type{/tr}</th>
			<td>{$item->mediaType}</td>
		</tr>
		<tr>
			<th class="text-right">{tr}Duration{/tr}</th>
			<td>{$item->duration}s</td>
		</tr>
		<tr>
			<th class="text-right">{tr}Views{/tr}</th>
			<td>{$item->views}</td>
		</tr>
		<tr>
			<th class="text-right">{tr}Plays{/tr}</th>
			<td>{$item->plays}</td>
		</tr>
		<tr>
			<th class="text-right">{tr}Wiki plugin code{/tr}</th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2"><pre style="margin:0;font-size:1.1em;">{ldelim}kaltura id="{$item->id}"{rdelim}</pre></td>
		</tr>
	</table>
{/strip}{/capture}
