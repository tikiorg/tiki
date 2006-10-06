{* $Header: /cvsroot/tikiwiki/tiki/templates/wiki-plugins/wikiplugin_vote.tpl,v 1.2 2006-10-06 12:08:38 sylvieg Exp $ *}
{strip}
<div style="display:inline;float:{$float}" class="poll">

<div class="pollnav">
{$tracker.name|escape}
<span style="display:{if $wikiplugin_tracker eq $tracker.trackerId}none{else}inline{/if};"><a onclick="javascript:toggle('pollzone{$tracker.trackerId}');toggle('polledit{$tracker.trackerId}');toggle('pollnavO{$tracker.trackerId}');" class="link" title="{tr}Toggle display{/tr}"><img src="img/icons/mo.png" border="0" alt="{tr}Toggle{/tr}" /></a>
</span>
</div>

{if $tiki_p_wiki_vote_ratings eq 'y'}
<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId}inline{else}none{/if};" id="polledit{$tracker.trackerId}">
{$vote}
</div>
{/if}

<div style="display:{if $wikiplugin_tracker eq $tracker.trackerId}inline{else}none{/if};" id="pollzone{$tracker.trackerId}">
{$stat}
</div>

</div>
{/strip}
