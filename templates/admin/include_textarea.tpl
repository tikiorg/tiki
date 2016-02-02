{* $Id$ *}
{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Text area (that apply throughout many features){/tr}
{/remarksbox}

<form class="form-horizontal" action="tiki-admin.php?page=textarea" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="textareasetup" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	{tabset name="admin_textarea"}
		{tab name="{tr}General Settings{/tr}"}
			<h2>{tr}General Settings{/tr}</h2>
			<fieldset>
				<legend>{tr}Features{/tr}{help url="Text+Area"}</legend>
				{preference name=feature_fullscreen}
				{preference name=feature_filegals_manager}
				{preference name=feature_dynamic_content}
				{preference name=feature_wiki_replace}
				{preference name=feature_syntax_highlighter}
				<div class="adminoptionboxchild" id="feature_syntax_highlighter_childcontainer">
					{preference name=feature_syntax_highlighter_theme}
				</div>
				{preference name=feature_wysiwyg}
				{preference name=ajax_autosave}
			</fieldset>

			<fieldset>
				<legend>{tr}Wiki syntax{/tr}{help url="Wiki+Syntax"}</legend>
				{preference name=feature_smileys}
				{preference name=feature_wiki_paragraph_formatting}
				<div class="adminoptionboxchild" id="feature_wiki_paragraph_formatting_childcontainer">
					{preference name=feature_wiki_paragraph_formatting_add_br}
				</div>
				{preference name=section_comments_parse}
				{preference name=feature_wiki_monosp}
				{preference name=feature_wiki_tables}
				{preference name=feature_wiki_argvariable}
				{preference name=wiki_dynvar_style}
				{preference name=wiki_dynvar_multilingual}
				{preference name=feature_wikilingo}
			</fieldset>

			<fieldset class="table featurelist">
				<legend>{tr}Plugins{/tr}</legend>
				{preference name=wikiplugin_showreference}
				{preference name=wikiplugin_addreference}
				{preference name=wikiplugin_alink}
				{preference name=wikiplugin_aname}
				{preference name=wikiplugin_box}
				{preference name=wikiplugin_button}
				{preference name=wikiplugin_center}
				{preference name=wikiplugin_code}
				{preference name=wikiplugin_countdown}
				{preference name=wikiplugin_div}
				{preference name=wikiplugin_dl}
				{preference name=wikiplugin_fade}
				{preference name=wikiplugin_fancylist}
				{preference name=wikiplugin_fancytable}
				{preference name=wikiplugin_font}
				{preference name=wikiplugin_footnote}
				{preference name=wikiplugin_footnotearea}
				{preference name=wikiplugin_gauge}
				{preference name=wikiplugin_html}
				{preference name=wikiplugin_iframe}
				{preference name=wikiplugin_include}
				{preference name=wikiplugin_mono}
				{preference name=wikiplugin_mouseover}
				{preference name=wikiplugin_mwtable}
				{preference name=wikiplugin_now}
				{preference name=wikiplugin_quote}
				{preference name=wikiplugin_remarksbox}
				{preference name=wikiplugin_scroll}
				{preference name=wikiplugin_slider}
				{preference name=wikiplugin_sort}
				{preference name=wikiplugin_split}
				{preference name=wikiplugin_sup}
				{preference name=wikiplugin_sub}
				{preference name=wikiplugin_tabs}
				{preference name=wikiplugin_tag}
				{preference name=wikiplugin_toc}
				{preference name=wikiplugin_versions}
				{preference name=wikiplugin_showpref}
			</fieldset>

			<fieldset>
				<legend>{tr}Miscellaneous{/tr}</legend>
				{preference name=feature_purifier}
				{preference name=feature_autolinks}
				{preference name=feature_hotwords}
				<div class="adminoptionboxchild" id="feature_hotwords_childcontainer">
					{preference name=feature_hotwords_nw}
					{preference name=feature_hotwords_sep}
				</div>
				{preference name=feature_use_quoteplugin}
				{preference name=feature_use_three_colon_centertag}
				{preference name=feature_simplebox_delim}
				{preference name=mail_template_custom_text}
				{preference name=wiki_plugindiv_approvable}
			</fieldset>

			<fieldset>
				<legend>{tr}Default size{/tr}</legend>
				{preference name=default_rows_textarea_wiki}
				{preference name=default_rows_textarea_comment}
				{preference name=default_rows_textarea_forum}
				{preference name=default_rows_textarea_forumthread}
			</fieldset>

			<fieldset>
				<legend>{tr}External links and images{/tr}</legend>
				{preference name=cachepages}
				{preference name=cacheimages}
				{preference name=feature_wiki_ext_icon}
				{preference name=feature_wiki_ext_rel_nofollow}
				{preference name=popupLinks}
				{remarksbox type='tip' title="{tr}Tip{/tr}"}
					{tr}External links will be identified with:{/tr} {icon name="link-external"}
				{/remarksbox}
			</fieldset>
		{/tab}

		{tab name="{tr}Plugins{/tr}"}
			<h2>{tr}Plugins{/tr}</h2>
			{remarksbox type="note" title="{tr}About plugins{/tr}"}{tr}Tiki plugins add functionality to wiki pages, articles, blogs, and so on. You can enable and disable them below.{/tr}
			{tr}You can approve plugin use at <a href="tiki-plugins.php">tiki-plugins.php</a>.{/tr}
			{tr}The edit-plugin icon is an easy way for users to edit the parameters of each plugin in wiki pages. It can be disabled for individual plugins below.{/tr}
			{/remarksbox}
			{if !isset($disabled)}
				{button href="?page=textarea&disabled=y" _text="{tr}Check disabled plugins used in wiki pages{/tr}"}
				<br><br>
			{else}
				{remarksbox type=errors title="{tr}Disabled used plugins{/tr}"}
					{if empty($disabled)}
						{tr}None{/tr}
					{else}
						<ul>
						{foreach from="{$disabled}" item=plugin}
							<li>{$plugin|lower|escape}</li>
						{/foreach}
						</ul>
					{/if}
				{/remarksbox}
			{/if}

			<fieldset class="table">
				<legend>{tr}Plugin preferences{/tr}</legend>
				{preference name=wikipluginprefs_pending_notification}
			</fieldset>

			<fieldset class="table">
				<legend>{tr}Edit plugin icons{/tr}</legend>
				{preference name=wiki_edit_plugin}
				{preference name=wiki_edit_icons_toggle}
			</fieldset>

			<fieldset class="table" id="plugins">
				<legend>{tr}Plugins{/tr}</legend>
				<fieldset class="table donthide">
					{listfilter selectors='#plugins > fieldset' exclude=".donthide"}
				</fieldset>
				{foreach from=$plugins key=plugin item=info}
					<fieldset class="table">
						<legend>
							{if $info.iconname}{icon name=$info.iconname}{else}{icon name='plugin'}{/if} {$info.name|escape}
						</legend>
						<div class="adminoptionbox">
							<strong>{$plugin|escape}</strong>: {$info.description|default:''|escape}
							{help url="Plugin$plugin"}
						</div>
						{assign var=pref value="wikiplugin_$plugin"}
						{if in_array( $pref, $info.prefs)}
							{assign var=pref value="wikiplugin_$plugin"}
							{assign var=pref_inline value="wikiplugininline_$plugin"}
							{preference name=$pref label="{tr}Enable{/tr}"}
							{preference name=$pref_inline label="{tr}Disable edit plugin icon (make plugin inline){/tr}"}
						{/if}
					</fieldset>
				{/foreach}
			</fieldset>
		{/tab}

		{tab name="{tr}Plugin Aliases{/tr}"}
			<h2>{tr}Plugin Aliases{/tr}</h2>
			{remarksbox type="note" title="{tr}About plugin aliases{/tr}"}
				{tr}Tiki plugin aliases allow you to define your own custom configurations of existing plugins.{/tr}<br>
				{tr}Find out more here:{/tr}{help url="Plugin+Alias"}
			{/remarksbox}
			{if $prefs.feature_jquery neq 'y'}
				{remarksbox type="tip" title="{tr}Tip{/tr}"}
					{tr}This page is designed to work with JQuery{/tr} <a href="tiki-admin.php?page=features">{icon name="next"}</a>
				{/remarksbox}
			{/if}

			{* JQuery JS to set up page *}
			{jq}
				$('#contentadmin_textarea-3 legend > .toggle').click(function(event, hidefirst) {
					var legend = $(this).parent()[0];
					if ($(this).hasClass('collapsed')) {
						$(legend).find('.expanded.toggle').show();
						$(this).hide();
						if (hidefirst) {
							$(legend).nextAll(":not(.hidefirst)").show('fast')
						} else {
							$(legend).nextAll().show('fast')
						}
					} else {
						$(legend).find('.collapsed.toggle').show();
						$(this).hide();
						$(legend).nextAll(":not(.stayopen)").hide('fast')
					}
					return false;
				}).css("cursor", "pointer").nextAll(":not(.stayopen)").hide();
				{{if $plugin_admin}{* show gen info and simple params if plugin_admin selected *}}
				$('#pluginalias_general legend').trigger('click');
				$('#pluginalias_simple_args legend').trigger('click'{{if isset($plugin_admin.params)}, true{/if}});
				$('#pluginalias_body legend').trigger('click'{{if isset($plugin_admin.body.params)}, true{/if}});
				$('#pluginalias_add').click(function() {
					window.location.href = window.location.href.replace(/plugin_alias=[^&]*/, 'plugin_alias_new=true');
				});
				{{elseif $plugins_alias}{* or if no plugin_admin and a nice list *}}
				$('#pluginalias_general').hide();
				$('#pluginalias_simple_args').hide();
				$('#pluginalias_doc').hide();
				$('#pluginalias_body').hide();
				$('#pluginalias_composed_args').hide();
				$('#pluginalias_add').click(function() {
					$('#pluginalias_general legend')[0].showing = false;
					$('#pluginalias_general legend').trigger('click');
					$('#pluginalias_simple_args legend')[0].showing = false;
					$('#pluginalias_simple_args legend').trigger('click');
					$('#pluginalias_body legend')[0].showing = false;
					$('#pluginalias_body legend').trigger('click');

					$('#pluginalias_general').show();
					$('#pluginalias_simple_args').show();
					$('#pluginalias_doc').show();
					$('#pluginalias_body').show();
					$('#pluginalias_composed_args').show();

					$('#pluginalias_available legend')[0].showing = true;
					$('#pluginalias_available legend').trigger('click');

					return false;
				});
				{{else}{* or new view if no plugin_admin and no list *}}
					$('#pluginalias_general legend').trigger('click');
					$('#pluginalias_simple_args legend').trigger('click');
					$('#pluginalias_body legend').trigger('click');
				{{/if}}
				if (window.location.href.indexOf('plugin_alias_new=true') > -1) {
					$('#pluginalias_add').trigger('click');
				}
			{/jq}

			{* from tiki-admin-include-plugins.tpl *}
			{if $plugins_alias|@count}
				<fieldset id="pluginalias_available">
					<legend>
						<strong>{tr}Available Alias{/tr}</strong>{icon name='expanded' iclass='expanded toggle'}{icon name='collapsed' iclass='collapsed toggle' istyle='display:none'}{icon name="add" id="pluginalias_add" iclass='stayopen'}
					</legend>
					<div class="input_submit_container">
						{foreach from=$plugins_alias item=name}
							{assign var=full value='wikiplugin_'|cat:$name}
							<input type="checkbox" name="enabled[]" value="{$name|escape}" {if $prefs[$full] eq 'y'}checked="checked"{/if}>
							<a href="tiki-admin.php?page=textarea&amp;plugin_alias={$name|escape}">{$name|escape}</a>
						{/foreach}
						<div align="center">
							<input type="submit" class="btn btn-default btn-sm" name="enable" value="{tr}Enable Plugins{/tr}">
							<input type="submit" class="btn btn-warning btn-sm" name="delete" value="{tr}Delete Plugins{/tr}">
						</div>
						{remarksbox type="tip" title="{tr}Tip{/tr}"}
							{tr}Click on the plugin name to edit it.{/tr} {tr}Click on the + icon to add a new one.{/tr}
						{/remarksbox}
					</div>
				</fieldset>
				{jq}$('#pluginalias_available legend').trigger('click');{/jq}
			{/if}

			<fieldset id="pluginalias_general">
				<legend>
					{tr}General Information{/tr}{icon name='expanded' iclass='expanded toggle'}{icon name='collapsed' iclass='collapsed toggle' istyle='display:none'}
				</legend>

				<div class="adminoptionbox form-group">
					<div class="adminoptionlabel">
						<label class="control-label col-sm-4" for="plugin_alias">
							{tr}Plugin Name:{/tr}
						</label>
						<div class="col-sm-8">
							{if $plugin_admin}
								<input type="hidden" class="form-control" name="plugin_alias" id="plugin_alias" value="{$plugin_admin.plugin_name|escape}">
								<strong>{$plugin_admin.plugin_name|escape}</strong>
							{else}
								<input type="text" class="form-control" name="plugin_alias" id="plugin_alias">
							{/if}
						</div>
					</div>
				</div><br><br>
				<div class="adminoptionbox form-group">
					<div class="adminoptionlabel">
						<label class="control-label col-sm-4" for="implementation">
							{tr}Base Plugin:{/tr}
						</label>
						<div class="col-sm-8">
							<select class="form-control" name="implementation" id="implementation">
								{foreach from=$plugins_real item=base}
									<option value="{$base|escape}" {if isset($plugin_admin.implementation) and $plugin_admin.implementation eq $base}selected="selected"{/if}>
										{$base|escape}
									</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div><br>
				<div class="adminoptionbox form-group">
					<div class="adminoptionlabel">
						<label class="control-label col-sm-4" for="plugin_name">
							{tr}Name:{/tr}
						</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="name" id="plugin_name" value="{$plugin_admin.description.name|default:''|escape}">
						</div>
					</div>
				</div><br>
				<div class="adminoptionbox form-group">
					<div class="adminoptionlabel">
						<label class="control-label col-sm-4" for="plugin_description">
							{tr}Description:{/tr}
						</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="description" id="plugin_description" value="{$plugin_admin.description.description|default:''|escape}" class="width_40em">
						</div>
					</div>
				</div><br>
				<div class="adminoptionbox form-group">
					<div class="adminoptionlabel">
						<label class="control-label col-sm-4" for="plugin_body">
							{tr}Body Label:{/tr}
						</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="body" id="plugin_body" value="{$plugin_admin.description.body|default:''|escape}">
						</div>
					</div>
				</div><br>
				<div class="adminoptionbox form-group">
					<div class="adminoptionlabel">
						<label class="control-label col-sm-4" for="plugin_deps">
							{tr}Dependencies:{/tr}
						</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="prefs" id="plugin_deps" value="{if !empty($plugin_admin.description.prefs)}{','|implode:$plugin_admin.description.prefs}{/if}">
						</div>
					</div>
				</div><br>
				<div class="adminoptionbox form-group">
					<div class="adminoptionlabel">
						<label class="control-label col-sm-4" for="filter">
							{tr}Filter:{/tr}
						</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" id="filter" name="filter" value="{$plugin_admin.description.filter|default:'xss'|escape}">
						</div>
					</div>
				</div><br>
				<div class="adminoptionbox form-group">
					<div class="adminoptionlabel">
						<label class="control-label col-sm-4" for="validate">
							{tr}Validation:{/tr}
						</label>
						<div class="col-sm-8">
							<select class="form-control" name="validate" id="validate">
								{foreach from=','|explode:'none,all,body,arguments' item=val}
									<option value="{$val|escape}" {if !empty($plugin_admin.description.validate) and $plugin_admin.description.validate eq $val}selected="selected"{/if}>
										{$val|escape}
									</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div><br>
				<div class="adminoptionbox form-group">
					<div class="adminoptionlabel">
						<label class="control-label col-sm-4" for="inline">{tr}Inline (No Plugin Edit UI):{/tr}</label>
						<div class="col-sm-8">
							<input class="form-control" type="checkbox" id="inline" name="inline" value="1" {if !empty($plugin_admin.description.inline)}checked="checked"{/if}>
						</div>
					</div>
				</div><br>
			</fieldset><br>

			<fieldset id="pluginalias_simple_args">
				<legend>
					{tr}Simple Plugin Arguments{/tr}{icon name='expanded' iclass='expanded toggle'}{icon name='collapsed' iclass='collapsed toggle' istyle='display:none'}{icon name="add" iclass='stayopen' id="pluginalias_simple_add"}
				</legend>
				{jq}
					$('#pluginalias_simple_add').click(function() {
							var me = $('#pluginalias_simple_new'), clone = me.clone(), index = me.parent().children().size();
							clone.removeAttr('id');

							clone.find(':input').each(function () {
								$(this).attr('name', $(this).attr('name').replace('__NEW__', index));
								$(this).attr('id', $(this).attr('id').replace('__NEW__', index));
							}).val('');
							clone.find('label').each(function () {
								$(this).attr('for', $(this).attr('for').replace('__NEW__', index));
							});
							clone.show();
							me.parent().append(clone);

						return false;
					});
					{{if !empty($plugin_admin.params)}}
					$('#pluginalias_doc legend').trigger('click'{{if isset($plugin_admin.description.params)}, true{/if}});
					$('#pluginalias_simple_new').hide();
					{{/if}}
				{/jq}
				<div class="adminoptionbox">
					<div class="form-group">
						<label class="control-label col-sm-4">
							{tr}Argument{/tr}
						</label>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-1" ></label>
						<label class="control-label col-sm-4">
							{tr}Default{/tr}
						</label>
					</div>
				</div><br>
				{if !empty($plugin_admin.params)}
					{foreach from=$plugin_admin.params key=token item=value}
							{if ! $value|is_array}
								<div class="adminoptionbox">
									<div class="form-group">
										<div class="col-sm-4">
											<input class="form-control" type="text" name="sparams[{$token|escape}][token]" id="sparams_{$token|escape}_token" value="{$token|escape}">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-1" for="sparams_{$token|escape}_default"></label>
										<div class="col-sm-4">
											<input class="form-control" type="text" name="sparams[{$token|escape}][default]" id="sparams_{$token|escape}_default" value="{$value|escape}">
										</div>
									</div>
								</div><br>
							{/if}
						{/foreach}
				{/if}
				<div class="adminoptionbox hidefirst" id="pluginalias_simple_new">
					<div class="adminoptionlabel">
						<div class="form-group">
							<div class="col-sm-4">
								<input class="form-control" type="text" name="sparams[__NEW__][token]" id="sparams__NEW__token" value="">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-1" for="sparams__NEW__default"></label>
							<div class="col-sm-4">
								<input class="form-control" type="text" name="sparams[__NEW__][default]" id="sparams__NEW__default" value="">
							</div>
						</div>
					</div><br>
				</div>
			</fieldset><br>

			<fieldset id="pluginalias_doc">
				<legend>
					{tr}Plugin Parameter Documentation{/tr}{icon name='expanded' iclass='expanded toggle'}{icon name='collapsed' iclass='collapsed toggle' istyle='display:none'}{icon name="add" id="pluginalias_doc_add" iclass='stayopen'}
				</legend>
				{jq}$('#pluginalias_doc_add').click(function() { $('#pluginalias_doc_new').toggle(); return false; });{/jq}

				{if !empty($plugin_admin.description.params)}
					{foreach from=$plugin_admin.description.params key=token item=detail}
						<div class="clearfix adminoptionbox{if $token eq '__NEW__'} hidefirst" id="pluginalias_doc_new{/if}">
							<div class="adminnestedbox form-group">
								<div class="adminoptionlabel form-group">
									<label class="control-label col-sm-4" for="input[{$token|escape}][token]">
										{tr}Parameter:{/tr}
									</label>
									<div class="col-sm-8">
										<input class="form-control" type="text" name="input[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}">
									</div>
								</div>
								<div class="adminoptionlabel form-group">
									<label class="control-label col-sm-4" for="input[{$token|escape}][name]"
											>{tr}Name:{/tr}
									</label>
									<div class="col-sm-8">
										<input class="form-control" type="text" name="input[{$token|escape}][name]" value="{$detail.name|escape}">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="input[{$token|escape}][description]">
										{tr}Description:{/tr}
									</label>
									<div class="col-sm-8">
										<input class="form-control" type="text" name="input[{$token|escape}][description]" value="{$detail.description|escape}" class="width_30em">
									</div>
								</div>
								<div class="adminoptionlabel form-group">
									<label class="control-label col-sm-4" for="input[{$token|escape}][required]">
										{tr}Required:{/tr}
									</label>
									<div class="col-sm-8">
										<input class="form-control" type="checkbox" name="input[{$token|escape}][required]" value="y"{if $detail.required} checked="checked"{/if}>
									</div>
								</div>
								<div class="adminoptionlabel form-group">
									<label class="control-label col-sm-4" for="input[{$token|escape}][safe]">
										{tr}Safe:{/tr}
									</label>
									<div class="col-sm-8">
										<input class="form-control" type="checkbox" name="input[{$token|escape}][safe]" value="y"{if $detail.safe} checked="checked"{/if}>
									</div>
								</div>
								<div class="adminoptionlabel form-group">
									<label class="control-label col-sm-4" for="input[{$token|escape}][filter]">
										{tr}Filter:{/tr}
									</label>
									<div class="col-sm-8">
										<input class="form-control" type="text" name="input[{$token|escape}][filter]" value="{$detail.filter|default:xss|escape}">
									</div>
								</div>
							</div>
						</div>
						<hr>
					{/foreach}
				{/if}
			</fieldset><br>

			<div id="pluginalias_body">
				<fieldset>
					<legend>
						{tr}Plugin Body{/tr}{icon name='expanded' iclass='expanded toggle'}{icon name='collapsed' iclass='collapsed toggle' istyle='display:none'}
					</legend>

					<div class="adminoptionbox">
						<div class="adminoptionlabel form-group">
							<label class="control-label col-sm-4" for="ignorebody">
								{tr}Ignore User Input:{/tr}
							</label>
							<div class="col-sm-8">
								<input class="form-control" type="checkbox" name="ignorebody" id="ignorebody" value="y" {if !empty($plugin_admin.body.input) and $plugin_admin.body.input eq 'ignore'}checked="checked"{/if}/>
							</div>
						</div>
					</div>
					<div class="adminoptionbox form-group">
						<div class="adminoptionlabel form-group">
							<label class="control-label col-sm-4" for="defaultbody">{tr}Default Content:{/tr}</label>
							<div class="col-sm-8">
								<textarea class="form-control" cols="60" rows="12" id="defaultbody" name="defaultbody">{$plugin_admin.body.default|default:''|escape}</textarea>
							</div>
						</div>
					</div>
					<div style="clear:both; margin-left:60px">
						<fieldset class="stayopen">
							<legend style="font-size:125%">{tr}Parameters{/tr}{icon name='expanded' iclass='expanded toggle'}{icon name='collapsed' iclass='collapsed toggle' istyle='display:none'}{icon name="add" id="pluginalias_body_add" iclass='stayopen'}</legend>
							{jq}$('#pluginalias_body_add').click(function() { $('#pluginalias_body_new').toggle("fast"); return false; });{/jq}

							{if !empty($plugin_admin.body.params)}
								{foreach from=$plugin_admin.body.params key=token item=detail}
									<div class="clearfix adminoptionbox{if $token eq '__NEW__'} hidefirst" id="pluginalias_body_new{/if}">
										<div class="adminoptionlabel form-group">
											<label class="control-label col-sm-6" for="bodyparam[{$token|escape}][token]">
												{tr}Parameter:{/tr}
											</label>
											<div class="col-sm-6">
												<input class="form-control" type="text" name="bodyparam[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}">
											</div>
										</div>
										<div class="adminoptionlabel form-group">
											<label class="control-label col-sm-6" for="bodyparam[{$token|escape}][encoding]">
												{tr}Encoding:{/tr}
											</label>
											<div class="col-sm-6">
												<select class="form-control" name="bodyparam[{$token|escape}][encoding]">
													{foreach from=','|explode:'none,html,url' item=val}
														<option value="{$val|escape}" {if $detail.encoding eq $val}selected="selected"{/if}>
															{$val|escape}
														</option>
													{/foreach}
												</select>
											</div>
										</div>
										<div class="adminoptionlabel form-group">
											<label class="control-label col-sm-6" for="bodyparam[{$token|escape}][input]">
												{tr}Argument Source (if different):{/tr}
											</label>
											<div class="col-sm-6">
												<input class="form-control" type="text" name="bodyparam[{$token|escape}][input]" value="{$detail.input|escape}">
											</div>
										</div>
										<div class="adminoptionlabel form-group">
											<label class="control-label col-sm-6" for="bodyparam[{$token|escape}][default]">
												{tr}Default Value:{/tr}
											</label>
											<div class="col-sm-6">
												<input class="form-control" type="text" name="bodyparam[{$token|escape}][default]" value="{$detail.default|escape}">
											</div>
										</div>
									</div>
								{/foreach}
							{/if}
						</fieldset>
					</div>
				</fieldset>
			</div><br><br>

			<fieldset id="pluginalias_composed_args">
				<legend>
					{tr}Composed Plugin Arguments{/tr}{icon name='expanded' iclass='expanded toggle'}{icon name='collapsed' iclass='collapsed toggle' istyle='display:none'}{icon name="add" id="pluginalias_composed_add" iclass='stayopen'}
				</legend>
				{jq}$('#pluginalias_composed_add').click(function() { $('#pluginalias_composed_new').toggle("fast"); return false; });{/jq}

				{if !empty($plugin_admin.params)}
					{foreach from=$plugin_admin.params key=token item=detail}
						{if $detail|is_array}
							{if !isset($composed_args)}{assign var=composed_args value=true}{/if}
							<div class="clearfix adminoptionbox{if $token eq '__NEW__'} hidefirst" id="pluginalias_composed_new{/if}">
								<div class="adminoptionlabel form-group">
									<label class="control-label col-sm-4" for="cparams[{$token|escape}][token]">
										{tr}Parameter:{/tr}
									</label>
									<div class="col-sm-8">
										<input class="form-control" type="text" name="cparams[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}">
									</div>
								</div>
								<div class="adminoptionlabel form-group">
									<label class="control-label col-sm-4" for="cparams[{$token|escape}][pattern]">
										{tr}Pattern:{/tr}
									</label>
									<div class="col-sm-8">
										<input class="form-control" type="text" name="cparams[{$token|escape}][pattern]" value="{$detail.pattern|escape}">
									</div>
								</div><br>
								<div style="clear:both; margin-left:60px">
									<fieldset class="stayopen">
										<legend style="font-size:125%">
											{tr}Parameters{/tr}{icon name='expanded' iclass='expanded toggle'}{icon name='collapsed' iclass='collapsed toggle'}{icon name="add" id="pluginalias_composed_addparam" iclass='stayopen'}
										</legend>
										{jq}$('#pluginalias_composed_addparam').click(function() { $('#pluginalias_composed_newparam').toggle("fast"); return false; });{/jq}
										{if !empty($detail.params)}
											{foreach from=$detail.params key=t item=d}
												<div class="clearfix adminoptionbox{if $t eq '__NEW__'} hidefirst" id="pluginalias_composed_newparam{/if}">
													<div class="adminoptionlabel form-group">
														<label class="control-label col-sm-6" for="cparams[{$token|escape}][params][{$t|escape}][token]">
															{tr}Parameter:{/tr}
														</label>
														<div class="col-sm-6">
															<input class="form-control" type="text" name="cparams[{$token|escape}][params][{$t|escape}][token]" value="{if $t neq '__NEW__'}{$t|escape}{/if}">
														</div>
													</div>
													<div class="adminoptionlabel form-group">
														<label class="control-label col-sm-6" for="cparams[{$token|escape}][pattern]">
															{tr}Encoding:{/tr}
														</label>
														<div class="col-sm-6">
															<select class="form-control" name="cparams[{$token|escape}][params][{$t|escape}][encoding]">
																{foreach from=','|explode:'none,html,url' item=val}
																	<option value="{$val|escape}" {if $d.encoding eq $val}selected="selected"{/if}>{$val|escape}</option>
																{/foreach}
															</select>
														</div>
													</div>
													<div class="adminoptionlabel form-group">
														<label class="control-label col-sm-6" for="cparams[{$token|escape}][params][{$t|escape}][input]">
															{tr}Argument Source (if different):{/tr}
														</label>
														<div class="col-sm-6">
															<input class="form-control" type="text" name="cparams[{$token|escape}][params][{$t|escape}][input]" value="{$d.input|escape}"/>
														</div>
													</div>
													<div class="adminoptionlabel form-group">
														<label class="control-label col-sm-6" for="cparams[{$token|escape}][params][{$t|escape}][input]">
															{tr}Default Value:{/tr}
														</label>
														<div class="col-sm-6">
															<input class="form-control" type="text" name="cparams[{$token|escape}][params][{$t|escape}][default]" value="{$d.default|escape}"/>
														</div>
													</div>
												</div>
											{/foreach}
										{/if}
									</fieldset>
								</div><hr>
							</div>
						{/if}
					{/foreach}
				{/if}
				{if $plugin_admin}{jq}$('#pluginalias_composed_args legend').trigger('click'{{if isset($composed_args)}, true{/if}});{/jq}{/if}
			</fieldset>
		{/tab}
	{/tabset}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="textareasetup" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>


