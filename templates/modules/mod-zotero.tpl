{tikimodule error=$module_params.error title=$tpl_module_title name="zotero" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="zoterosearch" method="post" action="tiki-ajax_services.php">
		<input type="text" name="zotero_tags"/>
		<input type="submit" name="zotero" value="{tr}Search{/tr}"/>
		<ul class="results">
		</ul>
		<div class="error">
			{remarksbox type="errors" title="{tr}Oops!{/tr}"}
				{if $zotero_authorized}
					<p>{tr}No results were found. While we have an authorization from Zotero, it may not grant access to the group. If this search should have results, try re-authenticating with Zotero.{/tr}</p>
				{else}
					<p>{tr}We are not autorized to access the group at this time. If you have access to the Zotero group, you can grant this site read access.{/tr}</p>
				{/if}
				<p><a href="tiki-ajax_services.php?oauth_request=zotero">{tr}Authenticate with Zotero{/tr}</a></p>
			{/remarksbox}
		</div>
	</form>
	{jq}
	$('.zoterosearch:not(.done)').addClass('done')
		.find('.results, .error').hide().end()
		.submit(function (e) {
			var form = this;

			e.preventDefault();
			$.post(this.action, $(this).serialize(), function (data) {
				var isError = data.type == 'unauthorized';
				$('.error', form).toggle(isError || data.results.length === 0);
				$('.results', form).toggle(! isError).empty();

				$.each(data.results, function (k, i) {
					var entry = $('<li/>').hide(), link = $('<strong/>');
					entry
						.append($(i.content))
						.append($('<a/>').text('Zotero').attr('href', i.url))
						.append($('<input type="text"/>').val('{zotero key=' + i.key + '}'));

					link.text(i.title);
					link.css('cursor', 'pointer');
					link.click(function () {
						entry.toggle();
						$(':text', entry).select().focus();
					});
					$('.results', form).append(link).append(entry);
				});
			}, 'jsonp');

			return false;
		});
	{/jq}
{/tikimodule}
