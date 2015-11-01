{extends 'layout_view.tpl'}
{block name='subtitle'}
	<br>
{/block}
{block name="content"}
	{include file="utilities/alert.tpl"}
{/block}
{block name="buttons"}
	<button type="button" class="btn btn-default btn-dismiss" data-dismiss="modal">{tr}Close{/tr}</button>
	<a href="{$ajaxhref}" class="btn btn-primary">{$ajaxbuttonname}</a>
{/block}
