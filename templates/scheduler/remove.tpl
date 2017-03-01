{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{if $schedulerId}
	<form class="simple" method="post" action="{service controller=scheduler action=remove}">
		<p>{tr _0=$name}Do you really want to remove the %0 scheduler?{/tr}</p>
		<div class="submit">
			<input type="hidden" name="confirm" value="1">
			<input type="hidden" name="schedulerId" value="{$schedulerId|escape}">
			<input type="submit" class="btn btn-default btn-sm" value="{tr}Remove{/tr}">
		</div>
	</form>
{else}
	<a href="tiki-admin_schedulers.php">{tr}Back to tracker list{/tr}
{/if}
{/block}
