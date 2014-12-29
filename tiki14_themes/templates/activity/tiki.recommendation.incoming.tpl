{activityframe activity=$activity heading="{tr}Recommended Reading{/tr}" comment=disabled like=disabled summary=content}
	<p>{tr}Based on your previous activity, we believe you might find this useful.{/tr}</p>
	<p>{object_link type=$activity.item_type id=$activity.item_id}</p>
	<p class="small">{tr}Was this useful?{/tr} <a href="#">{tr}Help us improve{/tr}</a></p>
{/activityframe}
