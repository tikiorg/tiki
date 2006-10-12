{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/wikiplugin_vote.tpl,v 1.4 2006-10-12 15:30:25 sylvieg Exp $ *}
{strip}
<span style="display:inline;{if $float}float:{$float}{/if}" class="poll">

<div class="pollnav">
<a onclick="javascript:toggleBlock('pollzone{$tracker.trackerId}');toggleBlock('polledit{$tracker.trackerId}');toggleSpan('pollicon{$tracker.trackerId}');toggleSpan('pollicon{$tracker.trackerId}o')" class="link" title="{tr}Toggle display{/tr}">
<span id="pollicon{$tracker.trackerId}" style="display:inline;float:left"><img src="img/icons/plus.gif" border="0" alt="{tr}Toggle{/tr}" /></span>
<span id="pollicon{$tracker.trackerId}o" style="display:none;float:left;"><img src="img/icons/minus.gif" border="0" alt="{tr}Toggle{/tr}" /></span>
</a>
{$tracker.name|escape}
</div>

{if $p_create_tracker_items eq 'y'}
<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId}block{else}none{/if};" id="polledit{$tracker.trackerId}">
{$vote}
</div>
{/if}

<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId}block{else}none{/if};" id="pollzone{$tracker.trackerId}">
{$stat}
</div>

</span>
{/strip}
