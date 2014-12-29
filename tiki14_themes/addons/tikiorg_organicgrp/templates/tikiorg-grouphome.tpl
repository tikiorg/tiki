{strip}
	<div>
	{include file="tikiorg-groupnav.tpl"}
	{jq}$('.here_grouphome').removeClass('btn-default').addClass('btn-info');{/jq}
	{foreach item=result from=$results}
		{assign var=grpname value="tikiorg_organicgrp_`$result.object_id`"}
		{assign var=mgrpname value="tikiorg_organicgrp_managers_`$result.object_id`"}
		{assign var=pgrpname value="tikiorg_organicgrp_pending_`$result.object_id`"}
		{if $result.tracker_status == 'o' || $grpname|in_group}
			<div class="row">
				<form name="commSearch" method="post" action="./tikiorg_organicgrp_groupsearchpage?organicgroup={$result.object_id}&cat={$result.tracker_field_og_categoryID}&forgetlastsearch=y">
					<div class="col-md-12">
						<input type="text" name="default[content]" placeholder="Search within this {$prefs.ta_tikiorg_organicgrp_sterm}..." class="searchField">
						<input type="submit" value="Search" name="submit" class="btn btn-primary">
					</div>
				</form>
			</div>
			<div class="row">
				<div class="col-md-12">
					<h3>Latest Forum Posts</h3>
					{wikiplugin _name="list"}
					{literal}
						{list max="5" offset="0"}
						{filter type="forum post"}
						{filter content="0" field="parent_thread_id"}
						{filter content="{/literal}{$result.tracker_field_og_forum_ID}{literal}" field="parent_object_id"}
						{sort mode="modification_date_desc"}
						{output template="addons/tikiorg_organicgrp/templates/tikiorg-forum_list_title.tpl"}
						{ALTERNATE()}<p class="emptyinfo">No post available.</p>{ALTERNATE}
					{/literal}
					{/wikiplugin}
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<h3>Recent Activity</h3>
					{wikiplugin _name="activitystream" auto="1"}
					{literal}
						{pagination max="5"}
						{filter categories="{/literal}{$result.tracker_field_og_categoryID}{literal}"}
						{group field=aggregate collect=user}
					{/literal}
					{/wikiplugin}
					{jq}
						$('a').each(function(index, value) {
							var url = $(this).attr('href');
							if (typeof url !== 'undefined') {
								var ogid = '{{$result.object_id}}';
								var newurl = url.replace('?itemId', '?organicgroup=' + ogid + '&itemId');
								$(this).attr('href', newurl);
							}
						});
					{/jq}
				</div>
				<div class="col-md-4">
					<h3>New Members</h3>
					{wikiplugin _name="activitystream"}
					{literal}
						{pagination max="5"}
						{filter field="type" exact="user"}
						{filter field="organicgroupid" exact="{/literal}{$result.object_id}{literal}"}
						{group field=aggregate collect=user}
					{/literal}
					{/wikiplugin}
					{jq}
						$('a').each(function(index, value) {
							var url = $(this).attr('href');
							if (typeof url !== 'undefined') {
								var ogid = '{{$result.object_id}}';
								var newurl = url.replace('?itemId', '?organicgroup=' + ogid + '&itemId');
								$(this).attr('href', newurl);
							}
						});
					{/jq}
				</div>
			</div>
		{else}
			<p>{tr}As this is a private group, you have to be a group member to view activity within this group{/tr}</p>
		{/if}
	{/foreach}
	</div>
{/strip}