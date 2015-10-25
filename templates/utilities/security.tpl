{extends 'layout_view.tpl'}
{block name='subtitle'}
	<br>
{/block}
{block name="content"}
	{remarksbox type="{$ajaxtype}" close="n" title="{$ajaxheading}"}{/remarksbox}
	{tr}{$ajaxmsg|escape}{/tr}
{/block}
{block name="buttons"}
	<button type="button" class="btn btn-default btn-dismiss" data-dismiss="modal">{tr}Close{/tr}</button>
	<a href="{$ajaxaction}" class="btn btn-primary">{tr}Reload{/tr}</a>
{/block}
