{activityframe activity=$activity heading="{tr _0=$activity.user|userlink}%0 created a tracker item{/tr}"}
	<p>{object_link type=$activity.type id=$activity.object} in {object_link type=tracker id=$activity.trackerId}</p>
	{if $activity.aggregate.user|count > 1}
	<small>{$activity.aggregate.user|userlink}</small>
	{/if}
{/activityframe}
