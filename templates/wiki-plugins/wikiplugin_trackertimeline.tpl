<script type="text/javascript">
{literal}
ttl_showdetails = function( data ) {
		Shadowbox.open({
			type: 'html',
			player: 'html',
			width: 400,
			height: 300,
			title: data.title,
			content: '<p>' + ( data.fstart ) + ' to ' + ( data.fend ) + '</p>'
			       + '<p>' + ( data.psummary ) + '</p>'
			       + '<p class="right"><a href="tiki-view_tracker_item.php?itemId=' + escape(data.item) + '">Link</a></p>'
		});
	}
{/literal}
</script>
<table style="width: 100%;">
	<col width="10%"/>
	<col width="90%"/>
	{foreach from=$layouts item=layout}
	<tr>
		<td></td>
		<td>
			<div style="display: inline-block; display: -moz-inline-stack; width: {$layout.pad}%; height: 15px; border: 0;"></div>
			{foreach from=$layout.blocks item=label}<div style="display: inline-block; display: -moz-inline-stack; width: {$layout.size}%; height: 15px; border: 0; overflow:hidden; background: lightblue; padding: 0; margin: 0;">{$label}</div>{/foreach}
		</td>
	</tr>
	{/foreach}
	{foreach from=$wp_ttl_data item=list key=datagroup}
		<tr>
			<th>{$datagroup|escape}</th>
			<td>
				<div>
				{foreach from=$list item=block}{if $block.lpad > 0}<div style="display: inline-block; display: -moz-inline-stack; width: {$block.lpad}%; height: 30px; border: 0;"></div>{/if}<div style="display: inline-block; display: -moz-inline-stack; width: {$block.lsize}%; height: 30px; border: 0; overflow:hidden; background: lightgreen;">
						{if $block.lstart neq $block.start}&lt;&lt;&lt;{/if}

						{if $prefs.feature_shadowbox eq 'y'}
							<a href="javascript:ttl_showdetails({$block.encoded|escape})">{$block.title|escape}</a>
						{else}
							<a href="tiki-view_tracker_item.php?itemId={$block.item|escape}">{$block.title|escape}</a>
						{/if}

						{if $block.lend neq $block.end}&gt;&gt;&gt;{/if}

						<div class="comment">
							{$block.fstart}&nbsp;to&nbsp;{$block.fend}
						</div>
					</div>{/foreach}
				</div>
			</td>
		</tr>
	{/foreach}
</table>
