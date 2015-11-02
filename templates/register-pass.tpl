{* $Id$ *}
{if $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl}
	<input id='pass1' type="password" name="pass" onkeypress="regCapsLock(event)">
	&nbsp;<strong class='mandatory_star'>*</strong>
{else}
	{if !isset($openid_associate) || $openid_associate neq 'y'}
		<div class="form-group">
			<label class="col-md-4 col-sm-3 control-label" for="pass1">{tr}Password{/tr}</label>
			<div class="col-md-4 col-sm-6">
				<input
					class="form-control"
					id='pass1'
					type="password"
					name="pass"
					value="{if !empty($smarty.post.pass)}{$smarty.post.pass}{/if}"
				>
				<div style="margin-left:5px;">
					<div id="mypassword_text">{icon name='ok' istyle='display:none'}{icon name='error' istyle='display:none' } <span id="mypassword_text_inner"></span></div>
					<div id="mypassword_bar" style="font-size: 5px; height: 2px; width: 0px;"></div>
				</div>
				{if $prefs.feature_jquery_validation neq 'y'}
					<div style="margin-top:5px">
						{include file='password_help.tpl'}
					</div>
				{/if}
			</div>
			<div class="col-sm-1">
				{if $trackerEditFormId}<span class='text-danger tips' title=":{tr}This field is manadatory{/tr}">*</span>{/if}
			</div>
		</div>
	{/if}
{/if}
