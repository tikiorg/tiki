{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $birth_date}
		<form method="post" action="{service controller=user_conditions action=age_validation}">
			<p>{tr _0=$prefs.conditions_minimum_age _1=$birth_date}This site is %0+, which does not match the birth date of %1 you provided.{/tr}</p>
			<input class="btn btn-lg btn-primary" type="submit" name="decline" value="{tr}Leave the site{/tr}">
			<input name="origin" value="{$origin|escape}" type="hidden">
		</form>
	{else}
		<form method="post" action="{service controller=user_conditions action=age_validation}">
			<p>{tr _0=$prefs.conditions_minimum_age}This website is restricted to %0+. Please provide your birth date as a validation of your age.{/tr}</p>
			<div class="form-group">
				<label for="birth_date" class="form-label">
					{tr}Birth date{/tr}
				</label>
				<input type="date" name="birth_date" class="form-control">
			</div>
			<input class="btn btn-lg btn-primary" type="submit" name="accept" value="{tr}Continue{/tr}">
			<input class="btn btn-sm btn-danger" type="submit" name="decline" value="{tr}I Decline, log out{/tr}">
			<input name="origin" value="{$origin|escape}" type="hidden">
		</form>
	{/if}
{/block}
