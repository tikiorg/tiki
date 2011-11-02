<input type="hidden" name="{$field.ins_id|escape}" value="{$field.value|escape}"/>
<img id="{$field.ins_id|escape}_icon" src="{$field.value|escape}" alt="{tr}Select Icon{/tr}"/>
<div id="{$field.ins_id|escape}_selector" style="display: none;">
	<div class="sections" style="float: left; width: 25%;">
		<ul>
			{foreach from=$data.galleries item=gal}
				<li><a href="{$gal.url|escape}">{$gal.label|escape}</a></li>
			{/foreach}
		</ul>
	</div>
	<div class="contents" style="float: left; width: 75%;">
	</div>
</div>

{jq}
	var icon = $('#{{$field.ins_id|escape}}_icon').button();
	var field = icon.closest('form')[0].{{$field.ins_id}};
	var selector = $('#{{$field.ins_id|escape}}_selector')
		.dialog({
			title: icon.attr('alt'),
			width: 600,
			autoOpen: false,
			modal: true
		})
		.each(function () {
			var contents = $('.contents', this);
			$('.sections li a', this).click(function () {
				contents.empty();
				$.getJSON($(this).attr('href'), function (data) {
					$.each(data, function (k, v) {
						var link = $(v.link);
						link.empty().append($('<img/>').attr('src', link.attr('href')));
						link.click(function () {
							$(field).val($(this).attr('href'));
							icon.attr('src', $(this).attr('href'));
							selector.dialog('close');
							return false;
						});

						link.appendTo(contents);
					});
				});
				return false;
			});
		});
	icon.click(function () {
		selector.dialog('open');
	});
{/jq}
