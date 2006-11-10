{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/wikiplugin_vote.tpl,v 1.6 2006-11-10 15:22:27 sylvieg Exp $ *}
{strip}
<span style="display:inline;{if $float}float:{$float}{/if}" class="poll">

<div class="pollnav">
<a onclick="javascript:toggleBlock('pollzone{$tracker.trackerId}');toggleBlock('polledit{$tracker.trackerId}');toggleSpan('pollicon{$tracker.trackerId}');toggleSpan('pollicon{$tracker.trackerId}o')" class="link" title="{tr}Toggle display{/tr}">
<span id="pollicon{$tracker.trackerId}" style="display:inline;float:left"><img src="img/icons/plus.gif" border="0" alt="{tr}Toggle{/tr}" /></span>
<span id="pollicon{$tracker.trackerId}o" style="display:none;float:left;"><img src="img/icons/minus.gif" border="0" alt="{tr}Toggle{/tr}" /></span>
</a>
{if $has_already_voted ne 'y'}<span class="highlight">{/if}{$tracker.name|escape}{if $has_already_voted ne 'y'}</span>{/if}
</div>

<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId}block{else}none{/if};" id="polledit{$tracker.trackerId}">
{if $p_create_tracker_items eq 'y'}
{$vote}
{elseif $options.start > 0 and $options.start > $date}
{tr}Start:{/tr} {$options.start|tiki_short_datetime}<br />
{/if}
{if $options.end > 0 and $options.end > $date}
{tr}Close:{/tr} {$options.end|tiki_short_datetime}<br />
{/if}
</div>

<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId}block{else}none{/if};" id="pollzone{$tracker.trackerId}">
{$stat}
</div>

</span>
{/strip}
