{activityframe activity=$activity heading="{tr _0=$activity.aggregate.user|userlink}%0 modified a tracker item{/tr}"}
	<p>{object_link type=$activity.type id=$activity.object} in {object_link type=tracker id=$activity.trackerId}</p>
{/activityframe}
