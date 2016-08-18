{* $Id$ *}
{extends "layout_view.tpl"}

{block name="title"}
	{title help="Adding fields to a tracker" url="tiki-admin_tracker_fields.php?trackerId=$trackerId"}{tr}Tracker Fields{/tr}: {$tracker_info.name}{/title}
{/block}

{block name="navigation"}
	{assign var='title' value="{tr}Admin Tracker:{/tr} "|cat:$tracker_info.name|escape}
	<div class="t_navbar margin-bottom-md">
		<div class="btn-group">
			<a href="{service controller=tracker action=add_field trackerId=$trackerId}" class="btn btn-default add-field">{icon name="create"} {tr}Add Field{/tr}</a>
			<a href="{bootstrap_modal controller=tracker action=import_fields trackerId=$trackerId}" class="btn btn-default">{icon name="import"} {tr}Import Fields{/tr}</a>
		</div>
		{include file="tracker_actions.tpl"}
	</div>
{/block}

{block name="content"}
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
				var url = $.serviceUrl({ controller: 'tracker', action: 'export_fields' });
				var target = $('.modal.fade:not(.in)').first();
				$.post(url, $(form).serialize() + '&modal=1', function (data) {
					$(".modal-content", target).html(data);
					target.modal();
				});
				return false;

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
		var $container = $('.save-fields tbody');
		{{if $prefs.feature_jquery_ui eq 'y'}}
			$container.sortable({
					update: function () {
						$('td.id :hidden', this).each(function (k) {
							$(this).val(k * 10);
						});
					}
				})
				.disableSelection()
				.css('cursor', 'move');
		{{/if}}

		$container.tracker_load_fields(trackerId);

		$('.add-field').clickModal({
			open: function () {
				$(this).tracker_add_field({
					trackerId: trackerId
				});
			},
			success: function (data) {
				$container.tracker_load_fields(trackerId);

				$.closeModal({
					done: function () {
						if (! data.FORWARD) {
							return false;
						}

						setTimeout(function () {
							$.openModal({
								remote: $.service(data.FORWARD.controller, data.FORWARD.action, data.FORWARD)
							});
						}, 0);
					}
				});
			}
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
{/block}
