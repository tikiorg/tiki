{* $Id:$ *}
{if empty($user)}

	{include file='password_jq.tpl'}
	{if $openid_associate neq 'n'}
		<h1>{tr}Your OpenID identity is valid{/tr}</h1>
		<p>{tr}However, no account is associated to the OpenID identifier.{/tr}</p>
	{/if}
	<div class="alert alert-warning" id="divRegCapson" style="display: none;">{icon name='error' style="vertical-align:middle"} {tr}CapsLock is on.{/tr}</div>
	{if $allowRegister eq 'y'}
		<div class="row">
			{if $userTrackerData}
				{$userTrackerData}
			{else}
				<form action="tiki-register.php{if !empty($prefs.registerKey)}?key={$prefs.registerKey|escape:'url'}{/if}" class="form-horizontal" method="post" name="RegForm">
					{if $smarty.request.invite}<input type='hidden' name='invite' value='{$smarty.request.invite|escape}'>{/if}
						{include file="register-form.tpl"}
						{if $merged_prefs.feature_antibot eq 'y'}{include file='antibot.tpl' td_style='formcolor' form='register'}{/if}
						<div class="form-group col-sm-9 col-sm-offset-3 text-center">
							<button class="btn btn-primary registerSubmit submit" name="register" type="submit">{tr}Register{/tr} <!--i class="fa fa-check"></i--></button>
						</div>
				</form>
			{/if}
		</div>
		<div class="col-sm-9 col-sm-offset-2">
			{remarksbox type="note" title="{tr}Note{/tr}"}
				{if $prefs.feature_wiki_protect_email eq 'y'}
					{assign var=sender_email value=$prefs.sender_email|default:"this domain"|escape:'hexentity'}
				{else}
					{assign var=sender_email value=$prefs.sender_email|default:"this domain"|escape}
				{/if}
				{tr _0="$sender_email"}If you use an email filter, be sure to add %0 to your accepted list{/tr}
			{/remarksbox}
		</div>
	{/if}

	{if $openid_associate eq 'y'}
		<p>
			{tr}Associate OpenID with an existing Tiki account{/tr}
		</p>
		{include file="modules/mod-login_box.tpl"}
	{/if}
{else}
	{include file='modules/mod-login_box.tpl' nobox='y'}
{/if}

