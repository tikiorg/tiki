{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	{if $birth_date}
		<form class="age-validation" method="post" action="{service controller=user_conditions action=age_validation}">
			<p>{tr _0=$prefs.conditions_minimum_age _1=$birth_date}To login to this site you must be %0 years old, which does not match the birthdate of %1 you provided.{/tr}</p>
			<input class="btn btn-lg btn-primary" type="submit" name="decline" value="{tr}Cancel login process{/tr}">
			<input name="origin" value="{$origin|escape}" type="hidden">
		</form>
	{else}
		<form class="age-validation" method="post" action="{service controller=user_conditions action=age_validation}">
			<p>{tr _0=$prefs.conditions_minimum_age}You must be at least %0 years old to login into this site. Please provide your birthdate before proceed to login.{/tr}</p>
			<div class="form-group">
				<label for="birth_date" class="form-label">
					{tr}Birth date{/tr}
				</label>
				<input type="date" name="birth_date" class="form-control">
			</div>
			<input class="btn btn-lg btn-primary" type="submit" name="accept" value="{tr}Validate your age and proceed to login{/tr}">
			<input class="btn btn-sm btn-danger" type="submit" name="decline" value="{tr}Cancel login process{/tr}">
			<input name="origin" value="{$origin|escape}" type="hidden">
		</form>
	{/if}
{/block}
