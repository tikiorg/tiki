<!-- START of {$smarty.template} -->{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Text area (that apply throughout many features){/tr}
{/remarksbox}

<form action="tiki-admin.php?page=textarea" method="post">
<div class="cbox">
<table class="admin"><tr><td>
<div align="center" style="padding:1em"><input type="submit" name="textareasetup" value="{tr}Change Preferences{/tr}" /></div>

{if $prefs.feature_tabs eq 'y'}
			{tabs}{strip}
				{tr}General Settings{/tr}|
				{tr}Plugins{/tr}
			{/strip}{/tabs}
{/if}

      {cycle name=content values="1,2" print=false advance=false reset=true}

    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}General Settings{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}

<fieldset><legend>{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="Text+Area"}{/if}</legend>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_antibot" name="feature_antibot" {if $prefs.feature_antibot eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_antibot">{tr}Anonymous editors must enter anti-bot code (CAPTCHA){/tr}. </label>{if $prefs.feature_help eq 'y'} {help url="Spam+Protection"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_smileys" name="feature_smileys" {if $prefs.feature_smileys eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_smileys">{tr}Smileys{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="Smiley"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="popupLinks" id="general-ext_links" {if $prefs.popupLinks eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="general-ext_links">{tr}Open external links in new window{/tr}.</label>
	<br /><em>{tr}External links will be identified with{/tr}: </em><img border="0" class="externallink" src="img/icons/external_link.gif" alt=" (external link)" />.
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_filegals_manager" id="feature_filegals_manager" {if $prefs.feature_filegals_manager eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_filegals_manager">{tr}Use File Galleries to store pictures {/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_dynamic_content" name="feature_dynamic_content" id="feature_dynamic_content" {if $prefs.feature_dynamic_content eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_dynamic_content">{tr}Dynamic Content System{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="Dynamic+Content"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_comments_post_as_anonymous" id="feature_comments_post_as_anonymous"{if $prefs.feature_comments_post_as_anonymous eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_comments_post_as_anonymous">{tr}Allow to post comments as Anonymous{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="Post+Comments+as+Anonymous"}{/if}</div>
</div>
</fieldset>


<fieldset><legend>{tr}Wiki syntax{/tr} {if $prefs.feature_help eq 'y'} {help url="Wiki+Syntax"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id='feature_autolinks' name="feature_autolinks" {if $prefs.feature_autolinks eq 'y'}checked="checked"{/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_autolinks">{tr}AutoLinks{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="AutoLinks"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="quicktags_over_textarea" id="quicktags_over_textarea" {if $prefs.quicktags_over_textarea eq 'y'}checked="checked"{/if}/> </div>
	<div class="adminoptionlabel"><label for="quicktags_over_textarea">{tr}Show quicktags above textareas{/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="Quicktags"}{/if}
	<br /><em>{tr}If disabled, quicktags will be shown to the left of textareas{/tr}.</em>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input onclick="flip('hotwords_nw');" type="checkbox" name="feature_hotwords" id="feature_hotwords" {if $prefs.feature_hotwords eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_hotwords">{tr}Hotwords{/tr} </label>{if $prefs.feature_help eq 'y'} {help url="Hotwords"}{/if}</div>
	
	<div class="adminoptionboxchild" id="hotwords_nw" style="display:{if $prefs.feature_hotwords eq 'y'}block{else}none{/if};">
		<div class="adminoption"><input type="checkbox" name="feature_hotwords_nw" id="feature_hotwords_nw" {if $prefs.feature_hotwords_nw eq 'y'}checked="checked"{/if}/> </div>
		<div class="adminoptionlabel"><label for="feature_hotwords_nw">{tr}Open Hotwords in new window{/tr}.</label></div>	
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_use_quoteplugin" id="feature_use_quoteplugin"{if $prefs.feature_use_quoteplugin eq 'y'}checked="checked" {/if}/> </div>
	<div class="adminoptionlabel"><label for="feature_use_quoteplugin"> {tr}Use Quote plugin rather than &ldquo;&gt;&rdquo; for quoting{/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="PluginQuote"}{/if}
{if $prefs.wikiplugin_quote ne 'y'}<br />{icon _id=information} {tr}Plugin disabled{/tr}. {/if}
	</div>
</div>
</fieldset>

<fieldset><legend>{tr}Default size{/tr}</legend>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="default_rows_textarea_wiki">{tr}Wiki{/tr}:</label> <input type="text" name="default_rows_textarea_wiki" id="default_rows_textarea_wiki" value="{$prefs.default_rows_textarea_wiki}" size="4" /> {tr}rows{/tr}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="default_rows_textarea_comment">{tr}Comments {/tr}:</label><input type="text" name="default_rows_textarea_comment" id="default_rows_textarea_comment" value="{$prefs.default_rows_textarea_comment}" size="4" />{tr}rows{/tr}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="default_rows_textarea_forum">{tr}Forum{/tr}:</label><input type="text" name="default_rows_textarea_forum" id="default_rows_textarea_forum" value="{$prefs.default_rows_textarea_forum}" size="4" />{tr}rows{/tr}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="default_rows_textarea_forumthread">{tr}Forum reply{/tr}: </label><input type="text" name="default_rows_textarea_forumthread" id="default_rows_textarea_forumthread" value="{$prefs.default_rows_textarea_forumthread}" size="4" />{tr}rows{/tr}</div>
</div>
</fieldset>


      {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>


    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading" id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}Plugins{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}



<div class="adminoptionbox">
	<div class="adminoptionlabel">See the <a href="tiki-admin.php?page=plugins">{tr}Plugin Alias Manager{/tr}</a> for more configuration options.{if $prefs.feature_help eq 'y'} {help url="Plugins"}{/if}</div>
</div>
{foreach from=$plugins key=plugin item=info}
<div class="adminoptionbox">
{assign var=pref value=wikiplugin_$plugin}
{if in_array( $pref, $info.prefs)}
	<div class="adminoption"><input type="checkbox" id="wikiplugin_{$plugin|escape}" name="wikiplugin_{$plugin|escape}" {if $prefs[$pref] eq 'y'}checked="checked" {/if}/>
	</div>
{/if}
	<div class="adminoptionlabel"><label for="wikiplugin_{$plugin|escape}">{$info.name|escape}</label>
	{if $prefs.feature_help eq 'y'} {help url="Plugin$plugin"}{/if}
	<br /><strong>{$plugin|escape}</strong>: {$info.description|escape}
	</div>
</div>
{/foreach}

      {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>


<div align="center" style="padding:1em"><input type="submit" name="textareasetup" value="{tr}Change Preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>


