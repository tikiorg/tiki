{* $Id$ *}
{strip}
<span style="display:inline;{if $float}float:{$float}{/if}" class="poll">

<div class="pollnav">
{if $show_toggle ne 'n'}
<a onclick="javascript:toggleBlock('pollzone{$tracker.trackerId}');toggleBlock('polledit{$tracker.trackerId}');toggleSpan('pollicon{$tracker.trackerId}');toggleSpan('pollicon{$tracker.trackerId}o')" class="link" title="{tr}Toggle display{/tr}">
<span id="pollicon{$tracker.trackerId}" style="display:inline;float:left"><img src="img/icons/plus.gif" alt="{tr}Toggle{/tr}"></span>
<span id="pollicon{$tracker.trackerId}o" style="display:none;float:left;"><img src="img/icons/minus.gif" alt="{tr}Toggle{/tr}"></span>
</a>
{/if}
{if $has_already_voted ne 'y'}<span class="highlight">{/if}{$tracker.name|escape}{if $has_already_voted ne 'y'}</span>{/if}
{if $tracker_creator}<br>{$tracker_creator|userlink}}{/if}
</div>

<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId or $show_toggle eq 'n'}block{else}none{/if};" id="polledit{$tracker.trackerId}">
{if $p_create_tracker_items eq 'y'}
{$vote}
{elseif $options.start > 0 and $options.start > $date}
{tr}Start:{/tr} {$options.start|tiki_short_datetime}<br>
{/if}
{if $options.end > 0 and $options.end > $date}
{tr}Close:{/tr} {$options.end|tiki_short_datetime}<br>
{/if}
</div>

<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId or $show_toggle eq 'n'}block{else}none{/if};" id="pollzone{$tracker.trackerId}">
{$stat}
</div>

</span>
{/strip}
