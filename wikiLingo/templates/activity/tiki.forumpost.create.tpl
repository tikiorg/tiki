{activityframe activity=$activity heading="{tr _0=$activity.user|userlink}%0 started a new discussion!{/tr}"}
	<p>{object_link type=$activity.type id=$activity.object}</p>
	<pre>{$activity.content|truncate:300|escape}</pre>
{/activityframe}
