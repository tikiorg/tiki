{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/wikiplugin_vote.tpl,v 1.3 2006-10-11 21:49:26 sylvieg Exp $ *}
{strip}
<div style="display:inline;float:{$float}" class="poll">

<div class="pollnav">
{$tracker.name|escape}
<span style="display:{if $wikiplugin_tracker eq $tracker.trackerId}none{else}inline{/if};"><a onclick="javascript:toggle('pollzone{$tracker.trackerId}');toggle('polledit{$tracker.trackerId}');toggle('pollnavO{$tracker.trackerId}');" class="link" title="{tr}Toggle display{/tr}"><img src="img/icons/mo.png" border="0" alt="{tr}Toggle{/tr}" /></a>
</span>
</div>

{if $p_create_tracker_items eq 'y'}
<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId}inline{else}none{/if};" id="polledit{$tracker.trackerId}">
{$vote}
</div>
{/if}

<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId}inline{else}none{/if};" id="pollzone{$tracker.trackerId}">
{$stat}
</div>

</div>
{/strip}
