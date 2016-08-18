{activityframe activity=$activity heading="{tr _0=$activity.user|userlink _1=$activity.event_type|stringfix:'.':' '|stringfix:'tiki ':''}%0 %1{/tr}"}
	<p>{object_link type=$activity.type id=$activity.object}</p>
{/activityframe}
