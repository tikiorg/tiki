{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{if $trackerId}
	<form class="simple" method="post" action="{service controller=tracker action=clear}">
		<p>{tr _0=$name}Do you really want to clear all the items from tracker "%0"? (N.B. there is no undo and notifications will not be sent){/tr}</p>
		<div class="submit">
			<input type="hidden" name="confirm" value="1">
			<input type="hidden" name="trackerId" value="{$trackerId|escape}">
			<input type="submit" class="btn btn-default btn-sm" value="{tr}Clear All{/tr}">
		</div>
	</form>
{else}
	<a href="tiki-list_trackers.php">{tr}Back to tracker list{/tr}
{/if}
{/block}
