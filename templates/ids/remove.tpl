{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{if $ruleId}
	<form class="simple" method="post" action="{service controller=ids action=remove}">
		<p>{tr _0=$ruleId}Do you really want to remove the custom IDS rule with Id %0?{/tr}</p>
		<div class="submit">
			<input type="hidden" name="confirm" value="1">
			<input type="hidden" name="ruleId" value="{$ruleId|escape}">
			<input type="submit" class="btn btn-default btn-sm" value="{tr}Remove{/tr}">
		</div>
	</form>
{else}
	<a href="tiki-admin_ids.php">{tr}Back to IDS Rules list{/tr}
{/if}
{/block}
