{if ($groupnavfrom == 'forum' || $section == 'forums') && $forum_info.name|truncate:19:'':true eq 'tikiorg_organicgrp_'}
	{if !isset($groupTrackerItemId)}{assign var=groupTrackerItemId value=$forum_info.name|addonitemid}{/if}
	{jq}$('.here_groupforum').removeClass('btn-default').addClass('btn-info');{/jq}
{elseif ($groupnavfrom == 'whiteboard')}
	{if !isset($groupTrackerItemId)}{assign var=groupTrackerItemId value=$smarty.request.organicgroup}{/if}
	{jq}$('.here_groupboard').removeClass('btn-default').addClass('btn-info');{/jq}
{elseif ($groupnavfrom == 'files')}
	{if !isset($groupTrackerItemId)}{assign var=groupTrackerItemId value=$smarty.request.organicgroup}{/if}
	{jq}$('.here_groupfiles').removeClass('btn-default').addClass('btn-info');{/jq}
{elseif ($groupnavfrom == 'home')}
	{if !isset($groupTrackerItemId)}{assign var=groupTrackerItemId value=$smarty.request.organicgroup}{/if}
	{jq}$('.here_grouphome').removeClass('btn-default').addClass('btn-info');{/jq}
{elseif ($groupnavfrom == 'members')}
	{if !isset($groupTrackerItemId)}{assign var=groupTrackerItemId value=$smarty.request.organicgroup}{/if}
	{jq}$('.here_groupmembers').removeClass('btn-default').addClass('btn-info');{/jq}
{/if}
{if $groupTrackerItemId}

	{wikiplugin _name="list"}
	{literal}
		{list max="1" offset="0"}
		{filter type="trackeritem"}
		{filter content="{/literal}{addonobjectid profile="004_og_tracker" reference="trk_og"}{literal}" field="tracker_id"}
		{filter content="{/literal}{$groupTrackerItemId}{literal}" field="object_id"}
		{output template="addons/tikiorg_organicgrp/templates/tikiorg-groupnav.tpl"}
		{FORMAT(name="logo_image")}{display name="tracker_field_og_logo_image" format="trackerrender" default=""}{FORMAT}
	{/literal}
	{/wikiplugin}
{/if}
