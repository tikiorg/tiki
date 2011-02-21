{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Text area (that apply throughout many features){/tr}
{/remarksbox}

<form action="tiki-admin.php?page=textarea" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="textareasetup" value="{tr}Change preferences{/tr}" />
	</div>
	
	{tabset name="admin_textarea"}
		{tab name="{tr}General Settings{/tr}"}
			<fieldset>
				<legend>{tr}Features{/tr}{help url="Text+Area"}</legend>
				{preference name=feature_filegals_manager}
				{preference name=feature_dynamic_content}
				{preference name=feature_wiki_replace}
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

			</fieldset>

			<fieldset>
				<legend>{tr}Miscellaneous{/tr}</legend>
				{preference name=feature_purifier}
				{preference name=feature_autolinks}
				{preference name=feature_hotwords}
				<div class="adminoptionboxchild" id="feature_hotwords_childcontainer">
					{preference name=feature_hotwords_nw}
				</div>
				{preference name=feature_use_quoteplugin}
				{preference name=feature_use_three_colon_centertag}
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
					<em>{tr}External links will be identified with:{/tr} </em><img class="externallink" src="img/icons/external_link.gif" alt=" (external link)" />.
				{/remarksbox}
			</fieldset>
		{/tab}

		{tab name="{tr}Plugins{/tr}"}
			{remarksbox type="note" title="{tr}About plugins{/tr}"}{tr}Tiki plugins add functionality to wiki pages, articles, blogs, and so on. You can enable and disable them below.{/tr}
			{tr}You can approve plugin use at <a href="tiki-plugins.php">tiki-plugins.php</a>.{/tr}		
			{tr}The edit-plugin icon is an easy way for users to edit the parameters of each plugin in wiki pages. It can be disabled for individual plugins below.{/tr}
			{/remarksbox}
			{if !isset($disabled)}
				{button href="?page=textarea&disabled=y" _text="{tr}Check disabled plugins used in wiki pages{/tr}"}
				<br /><br />
			{else}
				{remarksbox type=errors title="{tr}Disabled used plugins{/tr}"}
					{if empty($disabled)}
						{tr}None{/tr}
					{else}
						<ul>
						{foreach from=$disabled item=plugin}
							<li>{$plugin|lower|escape}</li>
						{/foreach} 
						</ul>
					{/if}
				{/remarksbox}
			{/if}

			<fieldset class="admin">
				<legend>{tr}Plugin preferences{/tr}</legend>
				{preference name=wikipluginprefs_pending_notification}
			</fieldset>

			<fieldset class="admin">
				<legend>{tr}Edit plugin icons{/tr}</legend>
				{preference name=wiki_edit_plugin}
				{preference name=wiki_edit_icons_toggle}
			</fieldset>
			
			<fieldset class="admin">
				<legend>{tr}Plugins{/tr}</legend>
				<fieldset class="admin donthide">
					{listfilter selectors='#content2 .admin fieldset' exclude=".donthide"}
				</fieldset>
				{foreach from=$plugins key=plugin item=info}
					<fieldset class="admin">
						<legend>{$info.name|escape}</legend>
						<div class="adminoptionbox">
							<strong>{$plugin|escape}</strong>: {$info.description|escape}{assign var=pref value=wikiplugin_$plugin}{help url="Plugin$plugin"}
						</div>
						{if in_array( $pref, $info.prefs)}
							{assign var=pref value=wikiplugin_$plugin}
							{assign var=pref_inline value=wikiplugininline_$plugin}	
							{preference name=$pref label="{tr}Enable{/tr}"}
							{preference name=$pref_inline label="{tr}Disable edit plugin icon (make plugin inline){/tr}"}
						{/if}
					</fieldset>
				{/foreach}
			</fieldset>
		{/tab}

		{tab name="{tr}Plugin Aliases{/tr}"}
			{remarksbox type="note" title="{tr}About plugin aliases{/tr}"}
				{tr}Tiki plugin aliases allow you to define your own custom configurations of existing plugins.<br />Find out more here: {help url="Plugin+Alias"}{/tr}
			{/remarksbox}
			{if $prefs.feature_jquery neq 'y'}
				{remarksbox type="tip" title="{tr}Tip{/tr}"}
					{tr}This page is designed to work with JQuery {icon _id="arrow_right" href="tiki-admin.php?page=features"}{/tr}
				{/remarksbox}
			{/if}

		{* JQuery JS to set up page *}{jq}
$('#content3 legend').click(function(event, hidefirst) {
	var im = $(this).contents("img");
	if (im.length > 0) { im = im[0]; }
	if (!typeof this.showing == 'undefined' || !this.showing) {
		if ($(im).length > 0) { $(im).attr("src", $(im).attr("src").replace("/omodule.", "/module.")); }
		this.showing = true;
		if (hidefirst) {
			$(this).nextAll(":not(.hidefirst)").show('fast')
		} else {
			$(this).nextAll().show('fast')
		}
	} else {
		if ($(im).length > 0) { $(im).attr("src", $(im).attr("src").replace("/module.", "/omodule.")); }
		this.showing = false;
		$(this).nextAll(":not(.stayopen)").hide('fast')
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
				<legend><strong>{tr}Available Alias{/tr}</strong>{icon _id="omodule"} {icon _id="add" id="pluginalias_add"}</legend>
				<div class="input_submit_container">
					{foreach from=$plugins_alias item=name}
						{assign var=full value='wikiplugin_'|cat:$name}
						<input type="checkbox" name="enabled[]" value="{$name|escape}" {if $prefs[$full] eq 'y'}checked="checked"{/if}/>
						<a href="tiki-admin.php?page=textarea&amp;plugin_alias={$name|escape}">{$name|escape}</a>
					{/foreach}
					<div align="center">
						<input type="submit" name="enable" value="{tr}Enable Plugins{/tr}"/>
						<input type="submit" name="delete" value="{tr}Delete Plugins{/tr}"/>
					</div>
					{remarksbox type="tip" title="{tr}Tip{/tr}"}
						{tr}Click on the plugin name to edit it.{/tr} {tr}Click on the + icon to add a new one.{/tr}
					{/remarksbox}
				</div>
			</fieldset>
			{jq}$('#pluginalias_available legend').trigger('click');{/jq}
		{/if}
		<fieldset id="pluginalias_general">
			<legend>{tr}General Information{/tr}{icon _id="omodule"}</legend>
		
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="plugin_alias">{tr}Plugin Name:{/tr}</label>
					{if $plugin_admin}
						<input type="hidden" name="plugin_alias" id="plugin_alias" value="{$plugin_admin.plugin_name|escape}"/>
						<strong>{$plugin_admin.plugin_name|escape}</strong>
					{else}
						<input type="text" name="plugin_alias" id="plugin_alias" />
					{/if}
				</div>
			</div>
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="implementation">{tr}Base Plugin:{/tr}</label>
					<select name="implementation" id="implementation">
						{foreach from=$plugins_real item=base}
							<option value="{$base|escape}" {if isset($plugin_admin.implementation) and $plugin_admin.implementation eq $base}selected="selected"{/if}>{$base|escape}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="plugin_name">{tr}Name:{/tr}</label> <input type="text" name="name" id="plugin_name" value="{$plugin_admin.description.name|escape}"/>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="plugin_description">{tr}Description:{/tr}</label> <input type="text" name="description" id="plugin_description" value="{$plugin_admin.description.description|escape}" class="width_40em"/>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="plugin_body">{tr}Body Label:{/tr}</label> <input type="text" name="body" id="plugin_body" value="{$plugin_admin.description.body|escape}"/>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="plugin_deps">{tr}Dependencies:{/tr}</label> <input type="text" name="prefs" id="plugin_deps" value="{if !empty($plugin_admin.description.prefs)}{','|implode:$plugin_admin.description.prefs}{/if}"/>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="filter">{tr}Filter:{/tr}</label> <input type="text" id="filter" name="filter" value="{$plugin_admin.description.filter|default:'xss'|escape}"/>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="validate">{tr}Validation:{/tr}</label>
					<select name="validate" id="validate">
						{foreach from=','|explode:'none,all,body,arguments' item=val}
							<option value="{$val|escape}" {if $plugin_admin.description.validate eq $val}selected="selected"{/if}>{$val|escape}</option>
						{/foreach}
					</select>
			</div></div>
			<div class="adminoptionbox"><div class="adminoptionlabel">
					<label for="inline">{tr}Inline (No Plugin Edit UI):{/tr}</label> <input type="checkbox" id="inline" name="inline" value="1" {if $plugin_admin.description.inline}checked="checked"{/if}/>
			</div></div>
		</fieldset>
		<fieldset id="pluginalias_simple_args">
			<legend>{tr}Simple Plugin Arguments{/tr}{icon _id="omodule"} {icon _id="add" id="pluginalias_simple_add"}</legend>
			{jq}
$('#pluginalias_simple_add').click(function() { $('#pluginalias_simple_new').toggle("fast"); return false; });
{{if $plugin_admin.params}}
$('#pluginalias_doc legend').trigger('click'{{if isset($plugin_admin.description.params)}, true{/if}});
$('#pluginalias_simple_new').hide();
{{/if}}
			{/jq}
			{foreach from=$plugin_admin.params key=token item=value}
				{if ! $value|is_array}
					<div class="admingroup adminoptionbox">
						<div class="adminoptionlabel">
							<label for="sparams_{$token|escape}_token">{tr}Argument:{/tr}</label> <input type="text" name="sparams[{$token|escape}][token]" id="sparams_{$token|escape}_token" value="{$token|escape}"/>
							<label for="sparams_{$token|escape}_default" style="float:none;display:inline">{tr}Default:{/tr}</label> <input type="text" name="sparams[{$token|escape}][default]" id="sparams_{$token|escape}_default" value="{$value|escape}"/>
						</div>
					</div>
				{/if}
			{/foreach}
			<div class="admingroup adminoptionbox hidefirst" id="pluginalias_simple_new">
				<div class="adminoptionlabel">
					<label for="sparams__NEW__token">{tr}New Argument:{/tr}</label>
					<input type="text" name="sparams[__NEW__][token]" id="sparams__NEW__token" value=""/>
					<label for="sparams__NEW__default" style="float:none;display:inline">{tr}Default:{/tr}</label>
					<input type="text" name="sparams[__NEW__][default]" id="sparams__NEW__default" value=""/>
				</div>
			</div>
		</fieldset>
		<fieldset id="pluginalias_doc">
			<legend>{tr}Plugin Parameter Documentation{/tr}{icon _id="omodule"} {icon _id="add" id="pluginalias_doc_add"}</legend>
			{jq}$('#pluginalias_doc_add').click(function() { $('#pluginalias_doc_new').toggle("fast"); return false; });{/jq}
			
			{foreach from=$plugin_admin.description.params key=token item=detail}
				<div class="clearfix admingroup adminoptionbox{if $token eq '__NEW__'} hidefirst" id="pluginalias_doc_new{/if}">
					<div class="adminoptionlabel q1">
						<input type="text" name="input[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}"/>
					</div>
					<div class="adminnestedbox q234">
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][name]">{tr}Name:{/tr}</label> <input type="text" name="input[{$token|escape}][name]" value="{$detail.name|escape}"/>
						</div>
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][description]">{tr}Description:{/tr}</label> <input type="text" name="input[{$token|escape}][description]" value="{$detail.description|escape}" class="width_30em"/>
						</div>
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][required]">{tr}Required:{/tr}</label> <input type="checkbox" name="input[{$token|escape}][required]" value="y"{if $detail.required} checked="checked"{/if}/>
						</div>
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][safe]">{tr}Safe:{/tr}</label> <input type="checkbox" name="input[{$token|escape}][safe]" value="y"{if $detail.safe} checked="checked"{/if}/>
						</div>
						<div class="adminoptionlabel">
							<label for="input[{$token|escape}][filter]">{tr}Filter:{/tr}</label> <input type="text" name="input[{$token|escape}][filter]" value="{$detail.filter|default:xss|escape}"/>
						</div>
					</div>
				</div>
			{/foreach}
		</fieldset>
		<fieldset id="pluginalias_body">
			<legend>{tr}Plugin Body{/tr}{icon _id="omodule"}</legend>

			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="ignorebody">{tr}Ignore User Input:{/tr}</label> <input type="checkbox" name="ignorebody" id="ignorebody" value="y" {if $plugin_admin.body.input eq 'ignore'}checked="checked"{/if}/>
				</div>
			</div>
			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="defaultbody">{tr}Default Content:{/tr}</label>
					<textarea cols="60" rows="12" id="defaultbody" name="defaultbody">{$plugin_admin.body.default|escape}</textarea>
				</div>
				<div class="q1">&nbsp;</div>
				<div class="q234">
					<fieldset class="stayopen">
						<legend>{tr}Parameters{/tr}{icon _id="omodule"}{icon _id="add" id="pluginalias_body_add"}</legend>
						{jq}$('#pluginalias_body_add').click(function() { $('#pluginalias_body_new').toggle("fast"); return false; });{/jq}
						
						{foreach from=$plugin_admin.body.params key=token item=detail}
							<div class="clearfix admingroup adminoptionbox{if $token eq '__NEW__'} hidefirst" id="pluginalias_body_new{/if}">
								<div class="q1">
									<input type="text" name="bodyparam[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}"/>
								</div>
								<div class="q234">
									<div class="adminoptionlabel">
										<label for="bodyparam[{$token|escape}][encoding]">{tr}Encoding:{/tr}</label>
										<select name="bodyparam[{$token|escape}][encoding]">
											{foreach from=','|explode:'none,html,url' item=val}
												<option value="{$val|escape}" {if $detail.encoding eq $val}selected="selected"{/if}>{$val|escape}</option>
											{/foreach}
										</select>
									</div>
									<div class="adminoptionlabel">
										<label for="bodyparam[{$token|escape}][input]">{tr}Argument Source (if different):{/tr}</label> <input type="text" name="bodyparam[{$token|escape}][input]" value="{$detail.input|escape}"/>
									</div>
									<div class="adminoptionlabel">
										<label for="bodyparam[{$token|escape}][default]">{tr}Default Value:{/tr}</label> <input type="text" name="bodyparam[{$token|escape}][default]" value="{$detail.default|escape}"/>
									</div>
								</div>
							</div>
						{/foreach}
					</fieldset>
				</div>
			</div>
		</fieldset>
		<fieldset id="pluginalias_composed_args">
			<legend>{tr}Composed Plugin Arguments{/tr}{icon _id="omodule"} {icon _id="add" id="pluginalias_composed_add"}</legend>
			{jq}$('#pluginalias_composed_add').click(function() { $('#pluginalias_composed_new').toggle("fast"); return false; });{/jq}

			{foreach from=$plugin_admin.params key=token item=detail}
				{if $detail|is_array}
					{if !isset($composed_args)}{assign var=composed_args value=true}{/if}
					<div class="clearfix admingroup adminoptionbox{if $token eq '__NEW__'} hidefirst" id="pluginalias_composed_new{/if}">
						<div class="adminoptionlabel q1">
							<input type="text" name="cparams[{$token|escape}][token]" value="{if $token neq '__NEW__'}{$token|escape}{/if}"/>
						</div>
						<div class="q234">
							<div class="adminoptionlabel">
								<label for="cparams[{$token|escape}][pattern]">{tr}Pattern:{/tr}</label> <input type="text" name="cparams[{$token|escape}][pattern]" value="{$detail.pattern|escape}"/>
							</div>
							<fieldset class="stayopen">
								<legend>{tr}Parameters{/tr}{icon _id="omodule"} {icon _id="add" id="pluginalias_composed_addparam"}</legend>
								{jq}$('#pluginalias_composed_addparam').click(function() { $('#pluginalias_composed_newparam').toggle("fast"); return false; });{/jq}
								{foreach from=$detail.params key=t item=d}
									<div class="clearfix admingroup adminoptionbox{if $t eq '__NEW__'} hidefirst" id="pluginalias_composed_newparam{/if}">
										<div class="q1">
											<input type="text" name="cparams[{$token|escape}][params][{$t|escape}][token]" value="{if $t neq '__NEW__'}{$t|escape}{/if}"/>
										</div>
										<div class="q234">
											<div class="adminoptionlabel">
												<label for="cparams[{$token|escape}][pattern]">{tr}Encoding:{/tr}</label>
												<select name="cparams[{$token|escape}][params][{$t|escape}][encoding]">
													{foreach from=','|explode:'none,html,url' item=val}
														<option value="{$val|escape}" {if $d.encoding eq $val}selected="selected"{/if}>{$val|escape}</option>
													{/foreach}
												</select>
											</div>
											<div class="adminoptionlabel">
												<label for="cparams[{$token|escape}][params][{$t|escape}][input]">{tr}Argument Source (if different):{/tr}</label> <input type="text" name="cparams[{$token|escape}][params][{$t|escape}][input]" value="{$d.input|escape}"/>
											</div>
											<div class="adminoptionlabel">
												<label for="cparams[{$token|escape}][params][{$t|escape}][input]">{tr}Default Value:{/tr}</label> <input type="text" name="cparams[{$token|escape}][params][{$t|escape}][default]" value="{$d.default|escape}"/>
											</div>
										</div>
									</div>
								{/foreach}
							</fieldset>
						</div>
					</div>
				{/if}
			{/foreach}
			{if $plugin_admin}{jq}$('#pluginalias_composed_args legend').trigger('click'{{if isset($composed_args)}, true{/if}});{/jq}{/if}
		</fieldset>
		{/tab}
	{/tabset}
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="textareasetup" value="{tr}Change preferences{/tr}" />
	</div>
</form>


