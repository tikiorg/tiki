{activityframe activity=$activity heading="{tr _0=$activity.user|userlink}%0 requested an action from you.{/tr}" comment=disabled like=disabled summary=content}
	<p><a href="{service controller=mustread action=list id=$activity.target}">{object_title type=trackeritem id=$activity.target}</a></p>
{/activityframe}
