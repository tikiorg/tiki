{* $Id$ *}
{tikimodule error=$module_params.error title=$tpl_module_title name="zotero" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="zoterosearch" method="post" action="tiki-ajax_services.php">
		<input type="text" name="zotero_tags"/>
		<input type="submit" class="btn btn-default btn-sm" name="zotero" value="{tr}Search{/tr}"/>
		<ul class="results">
		</ul>
		<div class="alert alert-warning">
			{remarksbox type="errors" title="{tr}No results!{/tr}"}
				<p>{tr}No results were found. Are you sure you searched for a tag?{/tr}</p>

				{if ! $zotero_authorized}
					<p>{tr}We are not autorized to access the group at this time. If you have access to the Zotero group, you can grant this site read access.{/tr}</p>
					<p><a href="{service controller=oauth action=request provider=zotero}">{tr}Authenticate with Zotero{/tr}</a></p>
				{/if}
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
