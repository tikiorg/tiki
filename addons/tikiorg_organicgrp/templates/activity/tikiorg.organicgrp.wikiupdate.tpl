{assign var=white value="_"|explode:$activity.namespace}
{assign var="grpname" value="tikiorg_organicgrp_{$white[2]}"}
{activityframe activity=$activity heading="
<div class='active resource'><hr/></div>
{tr _0=$activity.user|userlink}%0 Updated a wiki page in <a href=\"tikiorg_organicgrp_grouphomepage?itemId={$white[1]}\">{$grpname|addongroupname}</a>{/tr}"}
	<div class="active_part2">
		<img src="addons/tikiorg_organicgrp/img/icon-document.png" alt="Document"/>
		<div class="avt_title1">
			<p><a href="{$activity.object}?organicgroup={$white[2]}">{if $activity.object|nonamespace|truncate:11:'':true != "whiteboard_"}{$activity.object|nonamespace}{else}{$activity.object|addongroupname} Whiteboard{/if}</a>
			</p><br/>
			<small>{tr _0=$activity.user|userlink _1=$activity.modification_date|tiki_short_datetime}By %0 (%1){/tr}</small>
		</div>
	</div>
{/activityframe}

