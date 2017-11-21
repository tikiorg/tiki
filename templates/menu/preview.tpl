{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$menuInfo.title|escape}{/title}
{/block}

{block name="content"}
	<h2>Smarty Code</h2>
	<pre id="preview_code">
	{ldelim}menu id={$menuId} type={$preview_type} css={$preview_css} bootstrap={$preview_bootstrap}{rdelim}</pre>{* <pre> cannot have extra spaces for indenting *}
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{$menuInfo.name|escape}</h3>
		</div>
		<div class="panel-body clearfix">
			{menu id=$menuId type=$preview_type css=$preview_css bootstrap=$preview_bootstrap}
		</div>
	</div>
{/block}
