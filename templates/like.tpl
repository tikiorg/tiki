<span class="like_block">
	{if not $count_only}
		<a class="like_button" data-type="{$type}" data-object="{$object}" href="#">
			{if $has_relation}
				<i class="fa fa-thumbs-up fa-lg"></i>
			{else}
				<i class="fa fa-thumbs-o-up fa-lg"></i>
			{/if}
		</a><!--close anchor-->
	{/if}
	<span class="numlikes">{$count}</span> {tr}{$count_label}{/tr}
</span>

{jq}
	$(".like_button").click(function(e) {
		e.preventDefault();
		var element = $(this);
		$.post($.service(
			'relation',
			'toggle',
			{
				relation:"tiki.user.like",
				target_type:$(this).data('type'),
				target_id:$(this).data('object'),
				source_type:"user",
				source_id:"{{$user}}",
			}
			), function(data) {
				if (data && data['relation_id']){ //if relation_id,
					$(element).find("i").removeClass('fa-thumbs-o-up');
					$(element).find("i").addClass('fa-thumbs-up');
					$(element).parent().find('.numlikes').html(parseInt($('.numlikes').html(), 10)+1);
				} else {
					$(element).find("i").removeClass('fa-thumbs-up');
					$(element).find("i").addClass('fa-thumbs-o-up');
					$(element).parent().find('.numlikes').html(parseInt($('.numlikes').html(), 10)-1);
				}
			},
		'json');
	});
{/jq}