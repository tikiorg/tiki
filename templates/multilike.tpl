<div class="multilike">
	{if $show_likes}
	<div class="likes {$orientation}">
		<div class="mini-counts">
			{$totalCount}
		</div>
		<div class="label-text">
			Likes
		</div>
	</div>
	{/if}

	{if $uses_values && $show_points}
		<div class="points {$orientation}">
			<div class="mini-counts">
				{$totalPoints}
			</div>
			<div class="label-text">
				Points
			</div>
		</div>
	{/if}

	<div class="rate {$orientation}">
		<div>
			<div class="title">{$choice_label}</div>
			{foreach $buttons as $button}
				<a class="{if $multilike_many eq 'y'}multilike_many{else}multilike_group{/if}"
				   data-relation="{$button.relation}"
				   data-relation_prefix="{$relation_prefix}"
				   data-target_type="{$type}"
				   data-user="{$user}"
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
					{$button.label} {if $show_option_totals}<span class="count">({$button.count})</span>{/if}
				</a>
				{if $orientation == "vertical"}
					<br>
				{/if}
			{/foreach}
		</div>
	</div>

</div>