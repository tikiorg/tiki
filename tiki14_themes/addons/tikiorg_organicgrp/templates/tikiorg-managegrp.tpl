{assign var=tikiaddon_package value="tikiorg_organicgrp"}
{assign var=grpname value="tikiorg_organicgrp_`$f_itemId`"}
{assign var=mgrpname value="tikiorg_organicgrp_managers_`$f_itemId`"}
{assign var=pgrpname value="tikiorg_organicgrp_pending_`$f_itemId`"}
{assign var=admgrpname value="Admins"}
{if $mgrpname|in_group || $admgrpname|in_group}
	<h2>Manage {$prefs.ta_tikiorg_organicgrp_sterm} - {$f_og_title}</h2>
	<a href="tikiorg_organicgrp_grouphomepage?itemId={$smarty.request.itemId|escape}"><button class="btn btn-default">Return to {$prefs.ta_tikiorg_organicgrp_sterm} Home Page</button></a>
	<h3>Manage Members</h3>
	{if $f_status == 'p'}
		<div class="row">
		{wikiplugin _name="memberlist" groups="tikiorg_organicgrp_pending_{$f_itemId}" addon_groups_approval_buttons="y"}{/wikiplugin}
		</div>
	{/if}
	<div class="row">
		{wikiplugin _name="memberlist" groups="tikiorg_organicgrp_{$f_itemId}" showDescriptions="y" email_to_added_user="y" email_to_removed_user="y"}{/wikiplugin}
	</div>
	<div class="row">
		{wikiplugin _name="memberlist" groups="tikiorg_organicgrp_managers_{$f_itemId}"}{/wikiplugin}
		<p>Only leaders can approve new requests to join, remove or add other members. As a leader, you can make other members leaders as well by adding them here.</p>
	</div>
	<h3>{$prefs.ta_tikiorg_organicgrp_sterm} Name and Description</h3>
	<p>Providing an informative description will enable visitors to quickly determine if they would like to join your group or investigate further.</p>
	{wikiplugin _name="tracker" trackerId="{addonobjectid profile="004_og_tracker" ref="trk_og"}" fields="{addonobjectid profile="004_og_tracker" ref="trk_og_title"}:{addonobjectid profile="004_og_tracker" ref="trk_og_description"}:{addonobjectid profile="004_og_tracker" ref="trk_og_logo_image"}" action="Change" url="tikiorg_organicgrp_managegrp?itemId={$f_itemId}"}{/wikiplugin}
	<h3>Removing {$prefs.ta_tikiorg_organicgrp_sterm}</h3>
	<p>Script to remove {$prefs.ta_tikiorg_organicgrp_sterm} is under development. For now, please contact us if you wish to remove the {$prefs.ta_tikiorg_organicgrp_sterm}.<p></p>
{else}
	{remarksbox type="error" title="{$prefs.ta_tikiorg_organicgrp_sterm} Management Pages" close="n"}
	You are not allowed to view this page.
	{/remarksbox}
{/if}
