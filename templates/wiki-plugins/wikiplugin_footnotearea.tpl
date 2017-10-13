<div class="footnotearea{$listName}">
	{foreach $footnotes as $number => $footnote}
		<div {if $footnote['class']}class="{$footnote['class']}" {/if}id="footnote{$footnote['unique']}">
			<span>
				<a href="#ref_footnote{$footnote['unique']}">{$number|numStyle:$listType}</a>
				{if isset($footnote['sameas'])}
					{foreach $footnote['sameas'] as $num => $unique}
						<a href="#ref_footnote{$unique}" class="sameas"> {($num+1)|numStyle:$sameType}</a>
					{/foreach}
				{/if}
			</span>
			{$footnote['data']}
		</div>
	{/foreach}
</div>