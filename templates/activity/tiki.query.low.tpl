{activityframe activity=$activity heading="{tr _0=$activity.user|userlink _1=tra($activity.priority)}%0 triggered your %1 priority saved filter{/tr}" comment=disabled like=disabled}
	<p>{object_link type=$activity.type id=$activity.object} <span class="label label-info">{tr}Low Priority{/tr}</span></p>
	<p><a class="btn btn-success" href="{service controller=search_stored action=list queryId=$activity.query}">{tr}See your saved filter{/tr}</a></p>
{/activityframe}
