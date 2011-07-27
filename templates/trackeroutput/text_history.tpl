{foreach from=$field.lingualpvalue item=item}
	<strong>{tr 0=$item.lang}Language: %0{/tr}</strong>
	<div>{$item.value}</div>
{/foreach}
