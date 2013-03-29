<form method="post" action="" id="{$execution_key|escape}_form">
	<input type="hidden" name="{$execution_key|escape}" value="1">
	<div class="memberlist">
		{tabset}
			{foreach from=$memberlist_groups key=groupName item=groupData}
				{tab name="{$groupName|escape}"}
					<div class="group">
						{if !empty($user) and $prefs.feature_user_watches eq 'y'}
							<div class="floatright">
								{if not $groupData.isWatching}
									{self_link watch=$groupName}
										{icon _id='eye' alt="{tr}Group is NOT being monitored. Click icon to START monitoring.{/tr}"}
									{/self_link}
								{else}
									{self_link unwatch=$groupName}
										{icon _id='no_eye' alt="{tr}Group IS being monitored. Click icon to STOP monitoring.{/tr}"}
									{/self_link}
								{/if}
							</div>
						{/if}
						<h2>{$groupName|escape}</h2>
						{if isset($groupData.info) and !empty($groupData.info.groupDesc)}
							<p class="description">{$groupData.info.groupDesc}</p>
						{/if}
						{if $groupData.members}
							{if $groupData.can_remove}{tr}Check to remove:{/tr}{/if}
							<ul>
								{foreach from=$groupData.members item=memberName}
									<li>
										{if $groupData.can_remove && $memberName != $user}
											<label>
												<input type="checkbox" name="remove[{$groupName|escape}][]" value="{$memberName|escape}">
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
									</li>
								{/foreach}
							</ul>
						{/if}
						{if $groupData.can_add && $defaultGroup ne 'y' }
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
	</div>
	{if $can_apply}
		<input type="submit" value="{tr}Apply{/tr}">
	{/if}
</form>
{jq}
$('.username-input').tiki('autocomplete','username');
{/jq}
