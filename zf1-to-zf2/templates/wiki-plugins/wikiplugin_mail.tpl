{if !empty($sents) || !empty($nbSentTo)}
	{remarksbox type='feedback' title="{tr}Message sent to{/tr}"}
		{tr}Email sent to:{/tr}
		<p>{$nbSentTo|escape} {tr}recipients{/tr}</p>
		<ul>
			{foreach from=$sents item=sent}
				<li>{$sent|escape}</li>
			{/foreach}
		<ul>
	{/remarksbox}
{/if}
{if !empty($mail_error)}
	{remarksbox type='errors' title="{tr}Errors{/tr}"}
		{tr}Error{/tr}
	{/remarksbox}
{/if}
{if $preview}
	<form method="post">
		{tr}The message will be sent to{/tr}
		<ul>
			<li>{tr}Number of Recipients:{/tr} {$nbTo}</li>
			<li>{tr}Subject:{/tr} {$mail_subject|truncate:100:"..."}</li>
			<li>{tr}Message:{/tr} {$mail_mess|truncate:100:"..."}</li>
		</ul>
		<input type="hidden" name="mail_subject" value="{$mail_subject|escape}">
		<input type="hidden" name="mail_mess" value="{$mail_mess|escape}">
		<input type="submit" class="btn btn-default" name="mail_send{$ipluginmail}" value="{tr}Send Mail{/tr}">
	</form>
	<form method="post">
		<input type="submit" class="btn btn-default" name="mail_cancel{$ipluginmail}" value="{tr}Cancel{/tr}">
	</form>
{else}
	<div>
		{if $mail_popup == 'y'}
			<p><input name="sendmailload{{$ipluginmail}}" type="submit" class="btn btn-default" value="{$mail_label_name|escape}" /></p>
			<div style="display: none;">
		{/if}
		<div id="wikiplugin_mail" class="col-md-6 centered">
			<form method="post">
				{if $params.showuserdd eq 'y' or $params.showrealnamedd eq 'y'}
					<div class="row">
						<label for="mail_user_dd{$ipluginmail}">{tr}Send to users:{/tr}</label>
					</div>
				{/if}

				{if $params.showuserdd eq 'y'}
					<div class="row">
						<select name="mail_user_dd[]" id="mail_user_dd{$ipluginmail}" multiple="multiple" size="8">
							<option value="" />
							{foreach from=$users item=muser}
								<option value="{$muser.userId}"{if in_array($muser.userId, $mail_user_dd)} selected="selected"{/if}>{$muser.login|escape}</option>
							{/foreach}
						</select>
					</div>
					<div class="row">
						{remarksbox type='tip' title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
					</div>
				{/if}

				{if $params.showrealnamedd eq 'y'}
					<div class="row">
						<select name="mail_user_dd[]" id="mail_user_dd{$ipluginmail}" multiple="multiple" size="8">
							<option value="" />
							{foreach from=$names item=muser}
								<option value="{$muser.userId}"{if in_array($muser.userId, $mail_user_dd)} selected="selected"{/if}>{$muser.login|username:true:false}</option>
							{/foreach}
						</select>
					</div>
					<div class="row">
						{remarksbox type='tip' title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
					</div>
				{/if}

				{if $params.showuser eq 'y'}
					<div class="row">
						<label for="mail_user{$ipluginmail}">{tr}Send to:{/tr}</label>
					</div>
				{/if}
				{if $params.showuser eq 'y'}
					<div class="row">
						<input type="text" size="80" name="mail_user" value="{$mail_user}">
						{remarksbox type='tip' title="{tr}Tip{/tr}"}{tr}Email separated by comma{/tr}{/remarksbox}
					</div>
				{/if}
				{if $params.showgroupdd eq 'y'}
					<div class="row">
						<label for="mail_group_dd{$ipluginmail}">{tr}Send to groups:{/tr}</label>
					</div>
					<div class="row">
						{foreach from=$groups key=groupname item=gps name=mailgroups}
							<div class="wpmailgroup" style="vertical-align: top;">
								{if !empty($groupname)}{$groupname|escape}{/if}
								<select name="mail_group_dd[][]" id="mail_group_dd{$ipluginmail}" multiple="multiple" size="8">
									<option value="" />
									{foreach from=$gps item=mgroup}
										{if $mgroup eq 'Anonymous'}
										{elseif $mgroup eq 'Registered'}
											{* <option value="all"{if in_array('All', $mail_group_dd[$smarty.foreach.mailgroups.index])} selected="selected"{/if}>{tr}All users{/tr}</option> *}
										{else}
											<option value="{$mgroup}"{if in_array($mgroup, $mail_group_dd[$smarty.foreach.mailgroups.index])} selected="selected"{/if}>{$mgroup|escape}</option>
										{/if}
									{/foreach}
								</select>
							</div>
						{/foreach}
						{remarksbox type='tip' title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
					</div>
				{/if}

				<div class="row">
					<label for="mail_subject{$ipluginmail}">{tr}Subject:{/tr}</label><br />
					<input type="text" id="mail_subject{$ipluginmail}" name="mail_subject" value="{$mail_subject}" size="80">
				</div>

				<div class="row">
					<label for="mail_mess{$ipluginmail}">{tr}Message:{/tr}</label><br />
					<textarea id="mail_mess{$ipluginmail}" name="mail_mess" value="{$mail_mess}" rows="20" cols="80"></textarea>
				</div>

				<div class="row">
					{if $bypass_preview != 'y'}
						<input type="submit" class="btn btn-default" name="mail_preview{$ipluginmail}" value="{tr}Preview Mail{/tr}">
					{else}
						<input type="submit" class="btn btn-default" value="{tr}Send Mail{/tr}">
						<input type="hidden" name="mail_send{$ipluginmail}" value="{tr}Send Mail{/tr}">
					{/if}
					{remarksbox type='info'}{tr}You will receive a copy of the email yourself. Please give it a few minutes.{/tr}{/remarksbox}
				</div>
			</form>
		</div>
		{if $mail_popup == 'y'}
			</div>
		{/if}
	</div>

{/if}

{jq}
	$("input[name='sendmailload{{$ipluginmail}}']").click(function() {
		$.colorbox({overlayClose: true, width:"620px", inline:true, href:"#wikiplugin_mail"});
		return false;
	});

	{{if $bypass_preview == 'y' && $mail_popup == 'y'}}
	$("input[name='mail_send{{$ipluginmail}}']").click(function() {
		if ($("textarea#mail_mess{{$ipluginmail}}").val()) {
			var mailform = $(this).closest('form');
			mailform.tikiModal("{tr}Please wait while your email is being sent...{/tr}");
			var postData = mailform.serializeArray();
			var formURL = "{{$smarty.server.PHP_SELF}}?{{query _urlencode=n}}";
			$.ajax({
				url : formURL,
				type: "POST",
				data : postData,
				success:function(data, textStatus, jqXHR) {
					mailform.tikiModal('');
					$.colorbox.close();
				}
			});
		}
		return false;
	});
	{{/if}}
{/jq}

