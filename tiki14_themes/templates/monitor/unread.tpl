{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<div class="notification-container">
		{foreach $result as $activity}
			<div>
				{if $prefs.monitor_individual_clear eq 'y'}
					<a class="clearone pull-right close" href="{service controller=monitor action=clearone activity=$activity.object_id}">&times;</a>
				{/if}
				{activity info=$activity format="summary"}
			</div>
		{foreachelse}
			<div class="alert alert-success">
				{tr}No unread notifications{/tr}
			</div>
		{/foreach}
		<div class="submit">
			{if $result|count > 0}
				<a class="btn btn-default clearall custom-handling" href="{service controller=monitor action=clearall timestamp=$timestamp}">
					{icon name="check"}
					{tr}Mark all as read{/tr}
				</a>
			{/if}
			<a class="btn btn-primary" href="{$more_link|escape}">{tr}Show More{/tr}</a>
		</div>
	</div>

	{jq}
		$('.notification-container .clearall').click(function (e) {
			e.preventDefault();
			$.post($(this).attr('href'));
			var $parent = $(this).closest('.notification-container');

			var last = 0;
			$parent.find('.media').each(function (k, item) {
				setTimeout(function () {
					$(item).slideUp('fast');
				}, k * 100);
				last = k;
			});
			setTimeout(function () {
				$('#bootstrap-modal').modal('hide');
			}, 100 * (last + 2));
			$('#notification-link').trigger('clear-all.monitor.tiki');
		});

		$('.notification-container .close').click(function (e) {
			e.preventDefault();
			$(this).parent().slideUp('fast');
			$.post($(this).attr('href'));
			$('#notification-link').trigger('clear-one.monitor.tiki');
		});
	{/jq}
{/block}
