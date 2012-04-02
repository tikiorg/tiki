{capture name=add_info}{strip}
<div class='opaque'>
	<div class='box-title'><strong>{tr}Additional Info{/tr}</strong></div>
	<div class='box-data'>
		<table>
			{if !empty($item->description)}
				<tr>
					<td><strong>{tr}Description{/tr}</strong>:</td>
					<td>{$item->description}</td>
				</tr>
			{/if}
			<tr>
				<td><strong>{tr}Status{/tr}</strong>:</td>
				<td>{$item->statusString}</td>
			</tr>
			<tr>
				<td><strong>{tr}Media Id{/tr}</strong>:</td>
				<td><pre style="margin:0">{$item->id}</pre></td>
			</tr>
			<tr>
				<td><strong>{tr}Media Type{/tr}</strong>:</td>
				<td>{$item->mediaType}</td>
			</tr>
			<tr>
				<td><strong>{tr}Duration{/tr}</strong>:</td>
				<td>{$item->duration}s</td>
			</tr>
			<tr>
				<td><strong>{tr}Views{/tr}</strong>:</td>
				<td>{$item->views}</td>
			</tr>
			<tr>
				<td><strong>{tr}Plays{/tr}</strong>:</td>
				<td>{$item->plays}</td>
			</tr>
			<tr>
				<td><strong>{tr}Wiki plugin code{/tr}</strong>:</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"><pre style="margin:0;font-size:1.1em;">{ldelim}kaltura id="{$item->id}"{rdelim}</pre></td>
			</tr>
		</table>
	</div>
</div>
{/strip}{/capture}