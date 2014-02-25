{* $Id$ *}

{title help="Adding+fields+to+a+tracker" url="tiki-admin_tracker_fields.php?trackerId=$trackerId"}{tr}Admin Tracker:{/tr} {$tracker_info.name}{/title}
{assign var='title' value="{tr}Admin Tracker:{/tr} "|cat:$tracker_info.name|escape}
<div class="navbar">
	{include file="tracker_actions.tpl"}
</div>

{tabset}
	<a name="list"></a>
	{tab name="{tr}Tracker fields{/tr}"}
        <h2>{tr}Tracker fields{/tr}		</h2>
		<form class="form add-field" method="post" action="{service controller=tracker action=add_field}" role="form">
			<input type="hidden" name="trackerId" value="{$trackerId|escape}">
			<button type="submit" class="btn btn-default">{glyph name="plus"} {tr}Add Field{/tr}</button>
		</form>
		<form class="form save-fields" method="post" action="{service controller=tracker action=save_fields}" role="form">
			<table id="fields" class="table table-condensed table-hover">
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
			<div class="form-group">
				<div class="input-group col-sm-6">
					<select name="action" class="form-control">
						<option value="save_fields">{tr}Save All{/tr}</option>
						<option value="remove_fields">{tr}Remove Selected{/tr}</option>
						<option value="export_fields">{tr}Export Selected{/tr}</option>
					</select>
					<span class="input-group-btn">
						<input type="hidden" name="trackerId" value="{$trackerId|escape}">
						<input type="hidden" name="confirm" value="0">
						<button type="submit" class="btn btn-primary" name="submit">{tr}Go{/tr}</button>
					</span>
				</div>
			</div>
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
					$(form).serviceDialog({
						controller: 'tracker',
						action: 'export_fields',
						title: tr('Export'),
						data: $(form).serialize(),
						load: function () {
							$('textarea', this).select();
						}
					});
				} else {
					$.ajax($(form).attr('action'), {
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
						tikitabs(1);
					}
				});

				return false;
			});
		{/jq}
	{/tab}
	
	{tab name="{tr}Import Tracker Fields{/tr}"}
        <h2>{tr}Import Tracker Fields{/tr}</h2>
		<form class="form simple import-fields" action="{service controller=tracker action=import_fields}" method="post" role="form">
			<div class="form-group">
				<label class="control-label">
					{tr}Raw Fields{/tr}
				</label>
				<textarea class="form-control" name="raw" rows="30"></textarea>
			</div>
			<div class="form-group">
				<label>
					<input type="checkbox" name="preserve_ids" value="1">
					{tr}Preserve Field IDs{/tr}
				</label>
			</div>
			<div class="form-group submit">
				<input type="hidden" name="trackerId" value="{$trackerId|escape}">
				<input type="submit" class="btn btn-primary" value="{tr}Import{/tr}">
			</div>
		</form>
	{/tab}
{/tabset}
