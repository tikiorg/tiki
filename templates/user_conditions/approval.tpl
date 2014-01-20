{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<div class="well">
		{$content}
	</div>
	<form method="post" action="{service controller=user_conditions action=approval}">
		<div class="checkbox">
			<label>
				<input name="approve" type="checkbox" value="{$hash|escape}">
				{tr}I approve the above terms and conditions{/tr}
			</label>
		</div>
		<input class="btn btn-lg btn-primary" type="submit" name="accept" value="{tr}Continue{/tr}">
		<input class="btn btn-lg btn-danger" type="submit" name="decline" value="{tr}I Decline{/tr}">
		<input name="origin" value="{$origin|escape}" type="hidden">
	</form>
{/block}
