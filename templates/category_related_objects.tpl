{* $id: *}
{if !empty($category_related_objects)}
<div class="related">
<h4>{tr}Related content{/tr}</h4>
<ul>
{foreach from=$category_related_objects item=object}
	<li><a href="{$object.name|sefurl}">{$object.name}</a></li>
{/foreach}
</ul>
</div>
{/if}