<div class="blogtools{if !empty($container_class)} {$container_class}{/if}">
<table class="normal">
<tr><th>{tr}Date{/tr}</th><th>{tr}Title{/tr}</th><th>{tr}Author{/tr}</th></tr>
{cycle values="odd,even" print=false}
{foreach from=$blogItems item=blogItem}
	<tr class="{cycle}">
		<td>{$blogItem.created|tiki_short_date}</td>
		<td><a class="link" href="{$blogItem.postId|sefurl:blogpost}">{$blogItem.title}</a></td>
		<td>{$blogItem.user|username}</td>
	</tr>
{/foreach}
</table>
</div>