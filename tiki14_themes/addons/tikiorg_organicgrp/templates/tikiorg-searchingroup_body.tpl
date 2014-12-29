<!-- result numbers and show -->

<div class="srShowNum">Show: <span id="ten" class="showNumLink{if $maxRecords == 10} active{/if}">10</span> | <span id="twentyfive" class="showNumLink{if $maxRecords == 25} active{/if}">25</span> | <span id="fifty" class="showNumLink{if $maxRecords == 50} active{/if}">50</span> | <span id="hundred" class="showNumLink{if $maxRecords == 100} active{/if}">100</span></div>
<div class="clear"></div>

<!-- sort/pagination bar -->
<div class="srShowResult">
	<div class="srShow">{if $offsetplusmaxRecords>$count}{$offsetplusmaxRecords = $count}{/if}Showing results {if $count}{$offsetplusone}{else}0{/if}-{$offsetplusmaxRecords} of {$count} {if $results->getEstimate() > $count}({$results->getEstimate()} Total){/if}</div>
	<div class="srResultsNum commsearch">{pagination_links offset_jsvar="customsearch_`$customsearchid`.offset" _onclick="$('#customsearch_`$customsearchid`').submit();return false;" resultset=$results}{/pagination_links}</div>
	<div class="clear"></div>
</div>

<!-- search results listing -->
{foreach item=result from=$results}

<div class="srResultsListCS">
	<div class="modulewhite">
		<div class="moduleBox resultList">
			<div class="srListItem">
				{if $result.object_type eq 'forum post'}
					<div class="postAvatar csIdentifier">
						{$result.contributors[0]|avatarize}
					</div>
					<div class="csContent forum_post">
						<div class="addfav myNetwork"><div>{wikiplugin _name="favorite" objectType="{$result.object_type}" objectId="`$result.object_id`"}{/wikiplugin}</div></div>
						<div class="forum_title"><a href="./tiki-view_forum_thread.php?comments_parentId={$result.object_id}" class="title">{$result.title}</a></div>
						<div class="forum_post"><span>{$result.postsnippet}</span> <a href="./tiki-view_forum_thread.php?comments_parentId={$result.object_id|escape}" class="">read more</a></div>
						<div class="forum_date">Started by {$result.contributors|userlink} {$result.modification_date|tiki_short_datetime:on}</div>
					</div>
				{else}
					{$result.object_type} - {$result.title}
				{/if}
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
	{foreachelse}
	{tr}No pages matched the search criteria{/tr}
	{/foreach}
</div>

<!--  bottom pagination bar -->
<div class="srShowResult">
	<div class="srShow">{if $offsetplusmaxRecords>$count}{$offsetplusmaxRecords = $count}{/if}Showing results {if $count}{$offsetplusone}{else}0{/if}-{$offsetplusmaxRecords} of {$count} {if $results->getEstimate() > $count}({$results->getEstimate()} Total){/if}</div>
	<div class="srResultsNum commsearch">{pagination_links offset_jsvar="customsearch_`$customsearchid`.offset" _onclick="$('#customsearch_`$customsearchid`').submit();return false;" resultset=$results}{/pagination_links}</div>
	<div class="clear"></div>
</div>
