{* $Id: category_related_objects.tpl 37328 2011-09-16 18:09:02Z lphuberdeau $ *}
{if !empty($category_related_objects)}
<div class="related">
	<h4>{tr}Related content{/tr}</h4>
	<ul>
	{foreach from=$category_related_objects item=object}
		<li><a href="{$object.href|escape}">{$object.name|escape}</a></li>
	{/foreach}
	</ul>
</div>
{/if}
