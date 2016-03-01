{if $force_fill_action eq 'new'}
	{jq}
		$.openModal({
			remote: $.service(
				'tracker',
				'insert_item',
				{
					trackerId: {{$force_fill_tracker}},
					editable: {{$force_fill_fields}},
					forced: {"{{$force_fill_user_field_permname}}":"{{$user}}"},
					status:"",
					title:"Please fill in the following information"
				}
			),
		});
	{/jq}
{else}
	{jq}
		$.openModal({
			remote: $.service(
				'tracker',
				'update_item',
				{
					trackerId: {{$force_fill_tracker}},
					itemId: {{$force_fill_item.itemId}},
					editable: {{$force_fill_fields}},
					status:"",
					title:"Please fill in the following information"
				}
			),
		});
	{/jq}
{/if}