{if $edit_features.field}
{tikimodule error=$module_params.error title=$tpl_module_title name="map_edit_features" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="map-edit-features" method="post" action="{service controller=tracker action=insert_item}">
		<div class="submit">
			<input type="hidden" name="trackerId" value="{$edit_features.trackerId|escape}"/>
			<input type="hidden" name="controller" value="tracker"/>
			<input type="hidden" name="action" value="insert_item"/>
			<input class="feature-content" id="{$edit_features.field.fieldId|escape}" type="hidden" name="forced~{$edit_features.field.permName|escape}"/>
			{foreach from=$edit_features.hiddenInput key=f item=v}
				<input id="{$f|escape}" type="hidden" name="forced~{$f|escape}" value="{$v|escape}"/>
			{/foreach}
			<input type="submit" class="btn btn-default btn-sm" name="create" value="{tr}Create{/tr}"/>
		</div>
	</form>
	{jq}
	$(function () {
		var map, activeFeature, form = $('.map-edit-features').hide();
		form
			.removeClass('map-edit-features')
			.submit(function () {
				if (! activeFeature) {
					return false;
				}

				var form = this;
				$.post($(form).attr('action'), $(form).serialize(), null, 'json')
					.success(function (data) {
						$(form).trigger('insert', [data]);
					})
					.error(function () {
						$(this).serviceDialog({
							title: $(':submit', form).val(),
							data: $(form).serialize(),
							success: function () {
								$(form).trigger('insert', [{}]);
							},
							close: function () {
								$(form).trigger('cancel');
							}
						});
					})
					;

				return false;
			})
			.each(function () {
				map = $(this).closest('.tab, #appframe, body').find('.map-container:first')[0];

				if (! map) {
					return;
				}

				$(this).show();
			});

		$(map).one('initialized', function () {
			var vlayer = map.vectors, toolbar, modify;

			function saveFeature() {
				var format = new OpenLayers.Format.GeoJSON;
				form.find('.feature-content').val(format.write(activeFeature));
			}
			vlayer.events.on({
				featureadded: function (event) {
					var format = new OpenLayers.Format.GeoJSON;

					if (! event.feature.attributes.itemId && event.feature.attributes.intent !== 'marker') {
						if (activeFeature) {
							vlayer.removeFeatures([activeFeature]);
							activeFeature = null;
						}

						activeFeature = event.feature;
						saveFeature();
						form.submit();
					}
				},
				featuremodified: function (event) {
					if (event.feature === activeFeature) {
						saveFeature();
					}
				}
			});

			{{if $edit_features.standardControls}}
				modify = new OpenLayers.Control.ModifyFeature(vlayer, {
					mode: OpenLayers.Control.ModifyFeature.DRAG | OpenLayers.Control.ModifyFeature.RESHAPE,
				});
				toolbar = new OpenLayers.Control.EditingToolbar(vlayer);
				toolbar.addControls([modify]);

				map.modeManager.addMode({
					name: 'Draw',
					controls: [ toolbar ]
				});
			{{/if}}

			form.bind('insert', function (e, data) {
				var form = this;

				$(map).trigger('changed');
				map.vectors.removeFeatures([activeFeature]);
				activeFeature = null;

				{{if $edit_features.editDetails}}
				if (data.itemId) {
					$('<a>').attr('href', $.service('tracker', 'update_item'))
						.serviceDialog({
							data: {
								trackerId: $(form.trackerId).val(),
								itemId: data.itemId
							},
							success: function () {
								$(map).trigger('changed');
							}
						});
				}
				{{/if}}

				{{if $edit_features.insertMode}}
					map.modeManager.switchTo({{$edit_features.insertMode|json_encode}});
				{{/if}}
			});
		});
	});
	{/jq}
{/tikimodule}
{else}
	{remarksbox type=warning title="Module misconfigured"}
		{tr}No acceptable field to store the feature was found in the specified tracker.{/tr}
	{/remarksbox}
{/if}
