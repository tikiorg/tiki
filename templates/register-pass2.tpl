{* $Id$ *}
{if $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl}
	<input id='pass2' type="password" name="passAgain" onkeypress="regCapsLock(event)">
	&nbsp;<strong class='mandatory_star'>*</strong>
{else}
	{if !isset($openid_associate) || $openid_associate neq 'y'}
		<div class="form-group">
			<label class="col-md-4 col-sm-3 control-label" for="pass2">{tr}Repeat password{/tr}</label>
			<div class="col-md-4 col-sm-6">
				<input class="form-control" id='pass2' type="password" name="passAgain" onkeypress="regCapsLock(event)" value="{if !empty($smarty.post.passAgain)}{$smarty.post.passAgain}{/if}"
					onkeyup="{if $prefs.feature_jquery_validation neq 'y' && !$userTrackerData}checkPasswordsMatch('#pass2', '#pass1', '#mypassword2_text');{/if}">
				<div style="float:right;margin-left:5px;">
					<div id="mypassword2_text"></div>
				</div>
				{if $prefs.feature_jquery_validation neq 'y' && !$userTrackerData}<span id="checkpass"></span>{/if}
			</div>
			<div class="col-md-1 col-sm-1">
				{if $trackerEditFormId}<span class='text-danger tips' title=":{tr}This field is manadatory{/tr}">*</span>{/if}
			</div>
			{if $prefs.generate_password eq 'y'}
				{*if !$reg_in_module}<td>&nbsp;</td>{/if*}
				<div class="col-md-3 col-sm-2{*if $reg_in_module} inmodule{/if*}">
					<span id="genPass">
						{if 0 and $prefs.feature_ajax eq 'y'}
							{button href="#" _onclick="check_pass();" _text="{tr}Generate a password{/tr}"}
						{else}
							{button href="#" _onclick="" _text="{tr}Generate a password{/tr}"}
						{/if}
					</span>
				</div>
			{/if}
		</div>
	{/if}
{/if}
