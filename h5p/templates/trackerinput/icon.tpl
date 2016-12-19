<div class="icon-selector-container">
	<input type="hidden" name="{$field.ins_id|escape}" value="{$field.value|escape}">
	<img class="icon" src="{$field.value|escape}" alt="{tr}Select Icon{/tr}">
	<div class="selector" style="display: none;">
		<div class="sections" style="float: left; width: 25%;">
			<div class="buttons">
				{foreach from=$data.galleries item=gal}
					<a href="{$gal.url|escape}">{$gal.label|escape}</a>
				{/foreach}
			</div>
		</div>
		<div class="contents" style="float: left; width: 75%; max-height: 600px;">
		</div>
	</div>
</div>

{jq}
	$('.icon-selector-container').removeClass('icon-selector-container').each(function () {
		var icon = $('.icon', this).button();
		var field = $(':input', this);
		var jqxhr;
		var selector = $('.selector', this)
			.dialog({
				title: icon.attr('alt'),
				width: 600,
				autoOpen: false,
				modal: true,
				open: function () { $(document).trigger('iconsloaded'); }
			})
			.each(function () {
				var contents = $('.contents', this);
				$('.buttons', this).buttonset();
				$('.sections a', this).css('display', 'block').click(function () {
					contents.empty().append($('<img/>').attr('src', 'img/spinner.gif'));
					if (jqxhr) {
						jqxhr.abort();
					}
					jqxhr = $.getJSON($(this).attr('href'), function (data) {
						jqxhr = null;
						contents.empty();
						$.each(data.result, function (k, v) {
							var link = $(v.link);
							link.attr('title', tr(v.title));
							link.empty().append($('<img/>').attr('src', link.attr('href')));
							link.click(function () {
								field.val($(this).attr('href'));
								icon.attr('src', $(this).attr('href'));
								selector.dialog('close');
								return false;
							});

							link.appendTo(contents);
						});
					});
					return false;
				});
				$('.sections a:first', this).click();
			});
		icon.click(function () {
			selector.dialog('open');
		});
	});
{/jq}
