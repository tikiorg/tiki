{activityframe activity=$activity heading="
<div class='active user'><hr/></div>
{tr _0=$activity.user|userlink}%0 joined {$prefs.ta_tikiorg_organicgrp_sterm|a_or_an}{/tr}"}
	<div class="active_part2">
		<div class="avt_title1">
			{ifsearchexists type="trackeritem" id="{$activity.organicgroupid|escape}"}
				<a href="tikiorg_organicgrp_grouphomepage?itemId={$activity.organicgroupid|escape}">{$activity.organicgroupname|escape}</a><br/>
				<br/>
			{/ifsearchexists}
			{ifsearchnotexists type="trackeritem" id="{$activity.organicgroupid|escape}"}
			{$activity.organicgroupname|escape} (deleted)
			{/ifsearchnotexists}
		</div>
	</div>
{/activityframe}
