{extends "internal/ajax.tpl"}

{block name=title}
	{title}{tr}Oops{/tr}{/title}
{/block}

{block name=content}
	<div class="alert alert-warning">
		{$detail.message|escape}
	</div>
{/block}
