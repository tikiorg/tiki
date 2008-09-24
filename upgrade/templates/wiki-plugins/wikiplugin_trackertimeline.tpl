<table style="width: 100%;">
	<col width="10%"/>
	<col width="90%"/>
	{foreach from=$wp_ttl_data item=list key=datagroup}
		<tr>
			<th>{$datagroup|escape}</th>
			<td>
				<div>
				{foreach from=$list item=block}
					{if $block.lpad > 0}
					<div style="display: inline-block; display: -moz-inline-stack; width: {$block.lpad}%; height: 30px; border: 0;"></div>
					{/if}
					<div style="display: inline-block; display: -moz-inline-stack; width: {$block.lsize}%; height: 30px; border: 0; overflow:hidden; background: lightgreen;" onmouseover="this.style.overflow=''" onmouseout="this.style.overflow='hidden'">
						{$block.title|escape}
						<div class="comment">
							{'H:i'|date:$block.start}&nbsp;to&nbsp;{'H:i'|date:$block.end}
						</div>
					</div>
				{/foreach}
				</div>
			</td>
		</tr>
	{/foreach}
</table>
