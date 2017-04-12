{extends 'layout_view.tpl'}
{block name="navigation"}
	{if $tiki_p_admin eq 'y'}
		<div class="t_navbar margin-bottom-md">
			<div class="btn-group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					{icon name="create"} {tr}Create{/tr} <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li>
						<a href="{bootstrap_modal controller=managestream action=sample}">
							{tr}Sample Rule{/tr}
						</a>
					</li>
					<li>
						<a href="{bootstrap_modal controller=managestream action=record}">
							{tr}Basic Rule{/tr}
						</a>
					</li>
					<li>
						<a href="{bootstrap_modal controller=managestream action=tracker_filter}">
							{tr}Tracker Rule{/tr}
						</a>
					</li>
					<li>
						<a href="{bootstrap_modal controller=managestream action=advanced}">
							{tr}Advanced Rule{/tr}
						</a>
					</li>
				</ul>
			</div>
			{button href="tiki-admin.php?page=community" _icon_name="settings" _text="{tr}Community{/tr}" _class="tips" _title=":{tr}Community Control Panel{/tr}"}
			{* former add_dracula() *}
			{$headerlib->add_jsfile('lib/dracula/raphael-min.js', true)}
			{$headerlib->add_jsfile('lib/dracula/graffle.js', true)}
			{$headerlib->add_jsfile('lib/dracula/graph.js', true)}
			{$headerlib->add_jsfile('lib/jquery_tiki/activity.js', true)}
			<button href="#" id="graph-draw" class="btn btn-default">{icon name="image"}{tr}Event Chain Diagram{/tr}</button>
			<div id="graph-canvas" class="graph-canvas" data-graph-nodes="{$event_graph.nodes|@json_encode|escape}" data-graph-edges="{$event_graph.edges|@json_encode|escape}"></div>
	{jq}
		$('#graph-draw').click(function(e) {
			$('#graph-canvas')
				.empty()
				.css('width', $window.width() - 50)
				.css('height', $window.height() - 130)
				.dialog({
					title: "Events",
					width: $window.width() - 20,
					height: $window.height() - 100
				})
				.drawGraph();
			return false;
		});
	{/jq}
		</div>
	{/if}
{/block}
{block name="title"}
	{title}{tr}Activity Rules{/tr}{/title}
{/block}
{block name="content"}
	{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
	{if $prefs.javascript_enabled !== 'y'}
		{$js = 'n'}
		{$libeg = '<li>'}
		{$liend = '</li>'}
	{else}
		{$js = 'y'}
		{$libeg = ''}
		{$liend = ''}
	{/if}
	<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
		<table class="table table-hover">
			<tr>
				<th>{tr}ID{/tr}</th>
				<th>{tr}Status{/tr}</th>
				<th>{tr}Event Type{/tr}</th>
				<th>{tr}Rule Type{/tr}</th>
				<th>{tr}Description{/tr}</th>
				<th></th>
			</tr>
			{foreach from=$rules item=rule}
				<tr>
					<td class="id">
						{$rule.ruleId|escape}
					</td>
					<td class="text">
						{if $rule.status eq 'enabled'}
							<span class="text-success tips" title=":{tr}Enabled{/tr}">{icon name="toggle-on"}</span>
						{elseif $rule.status eq 'disabled'}
							<span class="tips" title=":{tr}Disabled{/tr}">{icon name="toggle-off"}</span>
						{else}
							<span class="text-warning tips" title=":{tr}Unknown{/tr}">{icon name="warning"}</span>
						{/if}						
					</td>
					<td class="text">
						{$rule.eventType|escape}
					</td>
					<td class="text">
						{$ruleTypes[$rule.ruleType]|escape}
					</td>
					<td class="text">
						{$rule.notes|escape}
					</td>
					<td class="action">
						{capture name=rule_actions}
							{strip}
								{$libeg}
									<a href="{bootstrap_modal controller=managestream action="{if $rule.ruleType eq "sample"}sample{elseif $rule.ruleType eq "record"}record{elseif $rule.ruleType eq "tracker_filter"}tracker_filter{elseif $rule.ruleType eq "advanced"}advanced{/if}" ruleId=$rule.ruleId}" data-rule-id="{$rule.ruleId|escape}">
										{icon name="edit"} {tr}Edit{/tr}
									</a>
								{$liend}
								{if $rule.ruleType eq "record"}
									{$libeg}
										<a href="{bootstrap_modal controller=managestream action=change_rule_status ruleId=$rule.ruleId}">
											{if $rule.status eq "disabled"}
												{icon name="toggle-on"} {tr}Enable{/tr}
											{elseif $rule.status eq "enabled"}
												{icon name="toggle-off"} {tr}Disable{/tr}
											{/if}
										</a>
									{$liend}
								{/if}
								{if $rule.ruleType eq "sample" or $rule.ruleType eq "record"}
									{$libeg}
										<a href="{bootstrap_modal controller=managestream action=change_rule_type ruleId=$rule.ruleId}">
											{icon name="exchange"} {tr}Change Rule Type{/tr}
										</a>
									{$liend}
								{/if}
								{$libeg}
									<a href="{bootstrap_modal controller=managestream action=delete ruleId=$rule.ruleId}">
										{icon name="delete"} {tr}Delete{/tr}
									</a>
								{$liend}
							{/strip}
						{/capture}
						{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a
							class="tips"
							title="{tr}Actions{/tr}"
							href="#"
							{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.rule_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.rule_actions}</ul></li></ul>
						{/if}
					</td>
				</tr>
			{/foreach}
		</table>
	</div>
{/block}