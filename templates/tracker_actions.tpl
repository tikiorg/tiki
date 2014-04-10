{* $Id$ *}
{* navbar buttons at the top of the tracker pages *}
<div class="btn-group">

{if $tiki_p_admin_trackers eq 'y' and !empty($trackerId)}
	<a class="btn btn-default" href="{service controller=tracker action=replace trackerId=$trackerId modal=1}" data-toggle="modal" data-target="#bootstrap-modal">{glyph name=cog} {tr}Properties{/tr}</a>
	<a class="btn btn-default" href="tiki-admin_tracker_fields.php?trackerId={$trackerId|escape}">{glyph name="list-alt"} {tr}Fields{/tr}</a>
{/if}

{if $tiki_p_list_trackers eq 'y'}
	<a class="btn btn-default" href="tiki-list_trackers.php">{glyph name="arrow-up"} {tr}Trackers{/tr}</a>
{/if}

{if !empty($trackerId) and $tiki_p_view_trackers eq 'y'}
	<a class="btn btn-default" href="{$trackerId|sefurl:"tracker"}">{glyph name="list"} {tr}Items{/tr}</a>
{/if}

</div>
