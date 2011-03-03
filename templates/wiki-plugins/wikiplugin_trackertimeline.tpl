{jq notonready=true}
ttl_showdetails = function( data ) {
	$.colorbox({
		width: 400,
		height: 300,
		html:	'<h3>' + data.title + '</h3>' +
				'<p>' + ( data.fstart ) + ' to ' + ( data.fend ) + '</p>'
			   + '<p>' + ( data.psummary ) + '</p>'
			   + '<p class="right"><a href="tiki-view_tracker_item.php?itemId=' + escape(data.item) + '">Link</a></p>'
	});
}
{/jq}
<table style="width: 100%;">
	<col width="10%"/>
	<col width="90%"/>
	{foreach from=$layouts item=layout}
	<tr>
		<td></td>
		<td>
			<span style="display: inline-block; display: -moz-inline-stack; width: {$layout.pad}%; height: 15px; border: 0;"></span>
			{foreach from=$layout.blocks item=label}<span style="display: inline-block; display: -moz-inline-stack; width: {$layout.size}%; height: 15px; border: 0; overflow:hidden; background: lightblue; padding: 0; margin: 0;">{$label}</span>{/foreach}
		</td>
	</tr>
	{/foreach}
	{foreach from=$wp_ttl_data item=list key=datagroup}
		<tr>
			<th>{if $link_group_names}<a href="{$datagroup|sefurl:'wiki page'}">{/if}{$datagroup|tr_if|escape}{if $link_group_names}</a>{/if}</th>
			<td>
				<div>
					{foreach from=$list item=block}
						{if $block.lpad > 0}
							<span style="display: inline-block; display: -moz-inline-stack; width: {$block.lpad}%; height: 30px; border: 0;"></span>
						{/if}
						<span style="display: inline-block; display: -moz-inline-stack; width: {$block.lsize}%; height: 30px; border: 0; overflow:hidden; background: lightgreen;">
							{if $block.lstart neq $block.start}&lt;&lt;&lt;{/if}

							{if $block.link}
								<a href="{$block.link|sefurl:'wiki page'}">{$block.title|escape}</a>
							{else}
								{if $prefs.feature_shadowbox eq 'y'}
									<a href="#" onclick='ttl_showdetails({$block.encoded|escape});return false;'>{$block.title|escape}</a>
								{else}
									<a href="tiki-view_tracker_item.php?itemId={$block.item|escape}">{$block.title|escape}</a>
								{/if}
							{/if}

							{if $block.lend neq $block.end}&gt;&gt;&gt;{/if}

							<div class="comment">
								{$block.fstart}&nbsp;{tr}to{/tr}&nbsp;{$block.fend}
							</div>
						</span>
					{/foreach}
				</div>
			</td>
		</tr>
	{/foreach}
</table>
