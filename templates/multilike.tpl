Total Likes: {$totalCount}<br/>
{if $uses_values}Total Points: {$totalPoints}<br/>{/if}
{foreach $buttons as $button}
	<a class="{if $multilike_many eq 'y'}multilike_many{else}multilike_group{/if}"
	   data-relation="{$button.relation}"
	   data-relation_prefix="{$relation_prefix}"
	   data-target_type="{$type}"
	   data-target_id="{$object}"
	   {if $uses_values}
	   title="Worth {$button.value} Points"
	   {/if}
	   href="#"}>
		{if $button.selected eq '0'}
			<i class="fa fa-thumbs-o-up"></i>
		{else}
			<i class="fa fa-thumbs-up"></i>
		{/if}
		{$button.label} (Likes {$button.count}{if $uses_values}, Points {$button.points}{/if})
	</a>
	<br/>
{/foreach}


{jq}
	$(".multilike_group").click(function(e) {
		e.preventDefault();
		var element = $(this);
		$.post($.service(
			'relation',
			'toggle_group',
			{
				relation:$(element).data('relation'),
				relation_prefix:$(element).data('relation_prefix'),
				target_type:$(element).data('target_type'),
				target_id:$(element).data('target_id'),
				source_type:"user",
				source_id:"{{$user}}",
			}
			), function(data) {
				location.reload();
			},
		'json');
	});
	$(".multilike_many").click(function(e) {
		e.preventDefault();
		var element = $(this);
		$.post($.service(
			'relation',
			'toggle',
			{
				relation:$(element).data('relation'),
				target_type:$(element).data('target_type'),
				target_id:$(element).data('target_id'),
				source_type:"user",
				source_id:"{{$user}}",
			}
			), function(data) {
				location.reload();
			},
		'json');
	});
{/jq}