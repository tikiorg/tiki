{activityframe activity=$activity heading="{tr _0=$activity.user|userlink}%0 replied to a thread{/tr}" comment=disabled}
	<p>{object_link type=$activity.type id=$activity.object}</p>
	<pre>{$activity.content|truncate:300|escape}</pre>
	<p>{object_link type=$activity.type id=$activity.object title="{tr}Join the discussion!{/tr}" class="btn btn-sm btn-success"}</p>
{/activityframe}
