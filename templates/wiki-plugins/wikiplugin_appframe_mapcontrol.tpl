<a id="{$mapcontrol.id|escape}" href="#" title="{$mapcontrol.label|escape}">
	{icon id=$mapcontrol.icon title=$mapcontrol.label}
</a>
{jq}
$('#appframe .map-container').bind('initialized', function () {
	var container = this
		, link = '#{{$mapcontrol.id|escape}}'
		, vlayer
		, mode
		, controls = []
		;
	
	{{if $mapcontrol.mode}}
		mode = {{$mapcontrol.mode|json_encode}};
	{{else}}
		vlayer = container.vectors;
		{{if $mapcontrol.control}}
			controls.push({{$mapcontrol.control}});
		{{/if}}

		{{if $mapcontrol.navigation}}
			controls.push(new OpenLayers.Control.Navigation());
		{{/if}}

		mode = {{$mapcontrol.label|json_encode}};
		container.modeManager.addMode({
			name: {{$mapcontrol.label|json_encode}},
			controls: controls,
			activate: function () {
				$(link).addClass('active');
			},
			deactivate: function () {
				$(link).removeClass('active');
			}
		});
	{{/if}}

	$(link).click(function () {
		container.modeManager.switchTo(mode);
		return false;
	});
});
{/jq}
