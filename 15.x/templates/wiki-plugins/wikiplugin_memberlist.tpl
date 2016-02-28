<form method="post" action="" id="{$execution_key|escape}_form">
	<input type="hidden" name="{$execution_key|escape}" value="1">
	{if $Need_app|escape eq $execution_key|escape}
		<input id="hid_valid_{{$execution_key|escape}}" type="hidden" name="" value=""/>
	{/if}
	<div class="memberlist">
		{tabset}
			{foreach from=$memberlist_groups key=groupName item=groupData}
				{tab name="{$groupName|addongroupname|escape}"}
					<div class="group">
						{if !empty($user) and $prefs.feature_user_watches eq 'y'}
							<div class="pull-right">
								{if not $groupData.isWatching}
									{self_link watch=$groupName}
										{icon name='watch' class='tips' title=":{tr}Group is NOT being monitored. Click icon to START monitoring.{/tr}"}
									{/self_link}
								{else}
									{self_link unwatch=$groupName}
										{icon name='stop-watching' class='tips' title=":{tr}Group IS being monitored. Click icon to STOP monitoring.{/tr}"}
									{/self_link}
								{/if}
							</div>
						{/if}
						<h2>{$groupName|addongroupname|escape}</h2>
						{if isset($groupData.info) and !empty($groupData.info.groupDesc)}
							<p class="description">{$groupData.info.groupDesc}</p>
						{/if}
						{if $groupData.members}
							{if $groupData.can_remove and $Need_app|escape neq $execution_key|escape}{tr}Check to remove:{/tr}{/if}
							<ul>
								{foreach from=$groupData.members item=memberName}
									<li>
										{if $groupData.can_remove && $memberName != $user}
											<label>
												{if $Need_app neq $execution_key|escape}
													<input type="checkbox" name="remove[{$groupName|escape}][]" value="{$memberName|escape}">
												{/if}
												{$memberName|userlink}
											</label>
										{else}
											{$memberName|userlink}
										{/if}
										{if $prefs.feature_group_transition eq 'y'}
											{foreach from=$groupData.transitions key=cand item=trans}
												{if $cand eq $memberName}
													{foreach from=$trans key=tran item=label}
														{self_link transition=$tran member=$memberName}{$label|escape}{/self_link}
													{/foreach}
												{/if}
											{/foreach}
										{/if}

										{if $groupData.can_remove && $memberName != $user}
											{if $Need_app|escape eq $execution_key|escape}
												<div style="display: none;">
													<div id="welcome">
														<h2>{tr}Contents of email to be sent to user{/tr}</h2>
														<p class="pre_con">{$welcome_content}</p><br/>
														<p class="ita_info">{tr}Append custom message{/tr} :</p>
														<textarea class="need_text text_add" name="need_text_add"></textarea>
														<div class="mail_con">
															<input type="submit" class="Email_add" value="Approve and send email"/>
															<input type="submit" class="silent_add" value="Silently approve"/>
															<input class="sub" type="button" value="Cancel" onclick="$('#cboxClose').click();"/>
														</div>
													</div>
													<div id="rejectid">
														<h2>{tr}Contents of email to be sent to user{/tr}</h2>
														<p class="pre_con">{$reject_content}</p><br/>
														<p class="ita_info">Append custom message :</p>
														<textarea class="need_text text_remove" name="need_text_remove"></textarea>
														<div class="mail_con">
															<input type="submit" class="Email_remove" value="Reject and send email"/>
															<input type="submit" class="silent_remove" value="Silently reject"/>
															<input class="sub" type="button" value="Cancel" onclick="$('#cboxClose').click();"/>
														</div>
													</div>
												</div>
												<input class="approve sub" type="button" name="add[{$groupName|escape}]" member="{$memberName|escape}" value="{tr}Approve user{/tr}"/>
												<input class="reject sub" type="button" name="remove[{$groupName|escape}][]" member="{$memberName|escape}" value="{tr}Reject user{/tr}"/>
											{/if}
										{/if}

									</li>
								{/foreach}
							</ul>
						{/if}
						{if $groupData.can_add && $defaultGroup ne 'y' and $Need_app|escape neq $execution_key|escape}
							<p class="action">{tr}Add in group:{/tr} <input type="text" name="add[{$groupName|escape}]" class="username-input"> (comma separated)</p>
						{/if}
						{if $groupData.can_add && ($defaultGroup eq 'both' || $defaultGroup eq 'y' ) }
							<p class="action">{tr}Set as default group for users:{/tr} <input type="text" name="defgroup[{$groupName|escape}]" class="username-input"> (comma separated)</p>
						{/if}
						{if $groupData.can_join}
							<p class="action">
								<input type="checkbox" name="join[]" value="{$groupName|escape}" id="join-{$groupName|escape}">
								<label for="join-{$groupName|escape}">{tr}Join myself{/tr}</label>
							</p>
						{/if}
						{if $groupData.can_leave}
							<p class="action">
								<input type="checkbox" name="leave[]" value="{$groupName|escape}" id="leave-{$groupName|escape}">
								<label for="leave-{$groupName|escape}">{tr}Leave myself{/tr}</label>
							</p>
						{/if}
					</div>
				{/tab}
			{/foreach}
		{/tabset}
		{if $Need_app|escape neq $execution_key|escape}
			{if $can_apply}<input type="submit" class="btn btn-default btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">{/if}
		{/if}
	</div>
</form>
{jq}
	$('.username-input').tiki('autocomplete','username');
{/jq}
{if $Need_app|escape eq $execution_key|escape}
	{jq}

		{*problem in colorbox form submission so we use jquery*}

		$('.approve').click(function() {
		$('#hid_valid_{{$execution_key|escape}}').val($(this).attr('member'));
		var value = $(this).attr('name');
		value = value.replace(" (Needs Approval)", "");
		$('#hid_valid_{{$execution_key|escape}}').attr('name', value);
		$.colorbox({width:"75%",height:"90%", inline:true, overlayClose:false, href:"#welcome"});
		$('#cboxClose').hide();
		return false;
		});

		$('.reject').click(function() {
		$('#hid_valid_{{$execution_key|escape}}').attr('name', $(this).attr('name'));
		$('#hid_valid_{{$execution_key|escape}}').val($(this).attr('member'));
		$.colorbox({width:"75%",height:"90%", inline:true, overlayClose:false, href:"#rejectid"});
		$('#cboxClose').hide();
		return false;
		});

		$('.Email_add').live('click', function() {
		$(this).parent().tikiModal('Loading...');
		setTimeout("$(this).parent().tikiModal();", 1000);
		var text_area = $(this).parents().find(".text_add").val();;
		$('#hid_valid_{{$execution_key|escape}}').after('<input type="hidden" name="text_area" value="' + text_area + '"/>');
		$('#{{$execution_key|escape}}_form').submit();
		return false;
		});

		$('.Email_remove').live('click', function() {
		$(this).parent().tikiModal('Loading...');
		setTimeout("$(this).parent().tikiModal();", 1000);
		var text_area = $(this).parents().find(".text_remove").val();
		$('#hid_valid_{{$execution_key|escape}}').after('<input type="hidden" name="text_area" value="' + text_area + '"/>');
		$('#{{$execution_key|escape}}_form').submit();
		return false;
		});

		$('.silent_add').live('click', function() {
		$(this).parent().tikiModal('Loading...');
		setTimeout("$(this).parent().tikiModal();", 1000);
		$('#{{$execution_key|escape}}_form').submit();
		return false;
		});

		$('.silent_remove').live('click', function() {
		$(this).parent().tikiModal('Loading...');
		setTimeout("$(this).parent().tikiModal();", 1000);
		$('#{{$execution_key|escape}}_form').submit();
		return false;
		});

	{/jq}
{/if}
