{* $id: *}
{if !empty($category_related_objects)}
{tr}Related content{/tr}
<ul>
{foreach from=$category_related_objects item=object}
	<li><a href="{$object.name|sefurl}">{$object.name}</a></li>
{/foreach}
</ul>
{/if}