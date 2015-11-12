{* $Id$ *}
{if $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl}
	<input id='pass2' type="password" name="passAgain" onkeypress="regCapsLock(event)">
	&nbsp;<strong class='mandatory_star'>*</strong>
{else}
	{if !isset($openid_associate) || $openid_associate neq 'y'}
		<div class="form-group">
			<label class="col-md-4 col-sm-3 control-label" for="pass2">{tr}Repeat password{/tr}</label>
			<div class="col-md-4 col-sm-6">
				<input
					class="form-control"
					id='pass2'
					type="password"
					name="passAgain"
					value="{if !empty($smarty.post.passAgain)}{$smarty.post.passAgain}{/if}"
				>
				<div id="mypassword2_text">
					<div id="match" style="display:none">
						{icon name='ok' istyle='color:#0ca908'} {tr}Passwords match{/tr}
					</div>
					<div id="nomatch" style="display:none">
						{icon name='error' istyle='color:#ff0000'} {tr}Passwords do not match{/tr}
					</div>
				</div>
				{if $prefs.feature_jquery_validation neq 'y' && !$userTrackerData}<span id="checkpass"></span>{/if}
			</div>
			<div class="col-md-1 col-sm-1">
				{if $trackerEditFormId}<span class='text-danger tips' title=":{tr}This field is manadatory{/tr}">*</span>{/if}
			</div>
		</div>
		{if $prefs.generate_password eq 'y'}
			{*if !$reg_in_module}<td>&nbsp;</td>{/if*}
			<div class="form-group">
				<div class="col-sm-3 col-sm-offset-3 col-md-2 col-md-offset-4">
					<span id="genPass">{button href="#" _text="{tr}Generate a password{/tr}"}</span>
				</div>
				<div class="col-sm-3 col-md-2">
					<input id='genepass' class="form-control" name="genepass" type="text" tabindex="0" style="display:none">
				</div>
			</div>
		{/if}
	{/if}
{/if}
