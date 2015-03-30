{activityframe activity=$activity heading="{tr _0=$activity.user|userlink}%0 created a tracker{/tr}"}
	<p>{object_link type=tracker id=$activity.trackerId} has been created.</p>
	{if is_array($activity.aggregate)}
	<small>{$activity.aggregate.user|userlink}</small>
	{/if}
{/activityframe}
