{foreach from=$field.lingualpvalue item=item}
	<strong>{tr _0=$item.lang}Language: %0{/tr}</strong>
	<div>{$item.value}</div>
{/foreach}
