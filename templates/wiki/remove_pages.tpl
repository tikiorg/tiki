{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	{include file='access/include_items.tpl'}
	{$iname = ''}
	{if $extra.version === 'last'}
		{$iname = 'all'}
		{$idesc = 'all versions'}
	{elseif $extra.version === 'all'}
		{$iname = 'last'}
		{$idesc = 'last version only'}
	{/if}
	<form id='confirm-action' class='confirm-action' action="{service controller="$confirmController" action="$confirmAction"}" method="post">
		{if !empty($iname) && !$extra.one}
			<div class="chekbox">
				<label>
					<input type="checkbox" name="{$iname}"> {tr}Remove {$idesc}{/tr}
				</label>
			</div>
		{/if}
		{include file='access/include_hidden.tpl'}
	</form><br><br>
	{include file='access/include_footer.tpl'}
{/block}
