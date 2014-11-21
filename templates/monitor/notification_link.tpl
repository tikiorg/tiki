<a id="notification-link" href="{bootstrap_modal controller=monitor action=unread quantity=6}" title="{tr}Notifications{/tr}">
	{icon name="notification"}
</a>
{if $prefs.monitor_count_refresh_interval}
	{jq}
	var key = 'notification_count_{{$user|default:anonymous}}';
	$('#notification-link')
	.bind('clear-all.monitor.tiki', function () {
		$.localStorage.store(key, {
			count: 0
		});
		$(this).trigger('reload.monitor.tiki');
	})
	.bind('clear-one.monitor.tiki', function () {
		var link = this;
		$.localStorage.store(key, {
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
			$('.badge', link).remove();
			if (data.count > 0) {
				$('<span class="badge">').text(data.count).prependTo(link);
			}
		}, function (callback) {
			$.getJSON($.service('monitor', 'unread', {nodata: 1}), function (data) {
				callback({
					count: data.count
				});
			});
		}, {{$prefs.monitor_count_refresh_interval}});
	}).trigger('reload.monitor.tiki');
	{/jq}
{/if}
