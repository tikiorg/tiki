<a id="notification-link" href="{service controller=monitor action=unread quantity=6 modal=true}" data-toggle="modal" data-target="#bootstrap-modal">
	{glyph name=globe}
</a>
{if $prefs.monitor_count_refresh_interval}
	{jq}
	var key = 'notification_count';
	$('#notification-link')
	.bind('clear-all.monitor.tiki', function () {
		$.localStorage.store(key, {
			date: Date.now(),
			count: 0
		});
		$(this).trigger('reload.monitor.tiki');
	})
	.bind('clear-one.monitor.tiki', function () {
		var link = this;
		$.localStorage.store(key, {
			date: Date.now(),
			count: Math.max(0, parseInt($('.badge', link).text(), 10) -1)
		});
		$(this).trigger('reload.monitor.tiki');
	})
	.bind('force-reload.monitor.tiki', function () {
		$.localStorage.store(key, null);
		$(this).trigger('reload.monitor.tiki');
	})
	.bind('reload.monitor.tiki', function () {
		var link = this;
		$.localStorage.load(key, function (data) {
			var now = Date.now();

			$('.badge', link).remove();
			if (data.count > 0) {
				$('<span class="badge">').text(data.count).prependTo(link);
			}

			if ((data.date + {{$prefs.monitor_count_refresh_interval}}*1000) < now) {
				$.localStorage.store(key, null);
				$(link).trigger('force-reload');
			}
		}, function (callback) {
			$.getJSON($.service('monitor', 'unread', {nodata: 1}), function (data) {
				callback({
					count: data.count,
					date: Date.now()
				});
			});
		}); 
	}).trigger('reload.monitor.tiki');
	{/jq}
{/if}
