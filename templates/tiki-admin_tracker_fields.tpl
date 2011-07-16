{* $Id$ *}

{title help="Adding+fields+to+a+tracker" url="tiki-admin_tracker_fields.php?trackerId=$trackerId"}{tr}Admin Tracker:{/tr} {$tracker_info.name}{/title}
{assign var='title' value="{tr}Admin Tracker:{/tr} "|cat:$tracker_info.name|escape}
<div class="navbar">
	{include file="tracker_actions.tpl"}
</div>

{tabset}
	<a name="list"></a>
	{tab name="{tr}Tracker fields{/tr}"}
		<form class="save-fields" method="post" action="tiki-ajax_services.php">
			<table id="fields" class="normal">
				<thead>
					<tr>
						<th>{select_all checkbox_names="fields[]"}</th>
						<th>{tr}ID{/tr}</th>
						<th>{tr}Name{/tr}</th>
						<th>{tr}Type{/tr}</th>
						<th>{tr}List{/tr}</th>
						<th>{tr}Title{/tr}</th>
						<th>{tr}Search{/tr}</th>
						<th>{tr}Public{/tr}</th>
						<th>{tr}Mandatory{/tr}</th>
						<th>{tr}Actions{/tr}</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<div>
				<select name="action">
					<option value="save_fields">{tr}Save All{/tr}</option>
					<option value="remove_fields">{tr}Remove Selected{/tr}</option>
					<option value="export_fields">{tr}Export Selected{/tr}</option>
				</select>
				<input type="submit" name="submit" value="{tr}Go{/tr}"/>
				<input type="hidden" name="controller" value="tracker"/>
				<input type="hidden" name="trackerId" value="{$trackerId|escape}"/>
				<input type="hidden" name="confirm" value="0"/>
			</div>
		</form>

		<form class="add-field" method="post" action="tiki-ajax_services.php?controller=tracker&amp;action=addfield">
			<input type="hidden" name="trackerId" value="{$trackerId|escape}"/>
			<input type="submit" value="{tr}Add Field{/tr}"/>
		</form>
		{jq}
			var trackerId = {{$trackerId|escape}};
			$('.save-fields').submit(function () {
				var form = this, confirmed = false

				if ($(form.action).val() === 'remove_fields') {
					confirmed = confirm(tr('Do you really want to delete the selected fields?'));
					$(form.confirm).val(confirmed ? '1' : '0');

					if (! confirmed) {
						return false;
					}
				}

				if ($(form.action).val() === 'export_fields') {
					$(form).tracker_service_dialog({
						title: tr('Export'),
						data: $(form).serialize(),
						load: function () {
							$('textarea', this).select();
						}
					});
				} else {
					$.ajax('tiki-ajax_services.php', {
						type: 'POST',
						data: $(form).serialize(),
						dataType: 'json',
						success: function () {
							$container.tracker_load_fields(trackerId);
						}
					});
				}
				return false;
			});
			var $container = $('.save-fields tbody')
				.sortable({
					update: function () {
						$('td.id :hidden', this).each(function (k) {
							$(this).val(k * 10);
						});
					}
				})
				.disableSelection()
				.css('cursor', 'move');

			$container.tracker_load_fields(trackerId);

			$('.add-field').submit(function () {
				var form = this;
				$(form).tracker_add_field({
					trackerId: trackerId,
					success: function (data) {
						$container.tracker_load_fields(trackerId);
					}
				});

				return false;
			});

			$('.import-fields').submit(function () {
				var form = this;
				$.ajax({
					url: $(form).attr('action'),
					type: 'POST',
					data: $(form).serialize(),
					success: function () {
						$container.tracker_load_fields(trackerId);
						$('textarea', form).val('');
					}
				});

				return false;
			});
		{/jq}
	{/tab}
	
	{tab name="{tr}Import Tracker Fields{/tr}"}
		<form class="simple import-fields" action="tiki-ajax_services.php" method="post">
			<label>
				{tr}Raw Fields{/tr}
				<textarea name="raw"></textarea>
			</label>
			
			<label>
				<input type="checkbox" name="preserve_ids" value="1"/>
				{tr}Preserve Field IDs{/tr}
			</label>

			<div class="submit">
				<input type="hidden" name="controller" value="tracker"/>
				<input type="hidden" name="action" value="import_fields"/>
				<input type="hidden" name="trackerId" value="{$trackerId|escape}"/>
				<input type="submit" value="{tr}Import{/tr}"/>
			</div>
		</form>
	{/tab}
{/tabset}
