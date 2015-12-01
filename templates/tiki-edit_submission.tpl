{* $Id$ *}
{* Note: if you edit this file, make sure to make corresponding edits on tiki-edit_article.tpl *}

{include file='tiki-articles-js.tpl'}
{if !empty($errors)}
	{remarksbox type='errors' title="{tr}Errors{/tr}"}
		{foreach from=$errors item=m name=errors}
			{$m}
			{if !$smarty.foreach.errors.last}<br>{/if}
		{/foreach}
	{/remarksbox}
{/if}
{if $preview}
	<h2>{tr}Preview{/tr}</h2>

	{include file='article.tpl'}
{/if}

{if $subId}
	{title help="Articles" admpage="articles" url="tiki-edit_submission.php?subId=$subId"}{tr}Edit:{/tr} {$arttitle}{/title}
{else}
	{title help="Articles" admpage="articles"}{tr}Submit article{/tr}{/title}
{/if}

<div class="t_navbar">
	{button href="tiki-list_submissions.php" _icon_name="list" _text="{tr}List Submissions{/tr}"}
</div>

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Use ...page... to separate pages in a multi-page post{/tr}
{/remarksbox}

<form enctype="multipart/form-data" method="post" action=" " id='editpageform' class="form-horizontal">
	<input type="hidden" name="subId" value="{$subId|escape}">
	<input type="hidden" name="previewId" value="{$previewId|escape}">
	<input type="hidden" name="imageIsChanged" value="{$imageIsChanged|escape}">
	<input type="hidden" name="image_data" value="{$image_data|escape}">
	<input type="hidden" name="useImage" value="{$useImage|escape}">
	<input type="hidden" name="image_type" value="{$image_type|escape}">
	<input type="hidden" name="image_name" value="{$image_name|escape}">
	<input type="hidden" name="image_size" value="{$image_size|escape}">
	<div class="panel panel-default"><div class="panel-body">
		{tr}Fields with <b>*</b> are optional{/tr}
		{if $types.$type.show_topline eq 'y'}, {tr}<b>Topline</b>=small line above Title{/tr}{/if}
		{if $types.$type.show_subtitle eq 'y'}, {tr}<b>Subtitle</b>=small line below Title{/tr}{/if}
		{if $types.$type.show_linkto eq 'y'}, {tr}<b>Source</b>=URL to article source{/tr}{/if}
	</div></div>
	<br>
	<div class="form-group" id='show_topline' {if $types.$type.show_topline eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Topline{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="topline" value="{$topline|escape}" size="60" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Title{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="title" value="{$arttitle|escape}" maxlength="255" size="60" class="form-control">
	    </div>
    </div>
    <div class="form-group" id='show_subtitle' {if $types.$type.show_subtitle eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Subtitle{/tr} *</label>
		<div class="col-sm-7">
	      	<input type="text" name="subtitle" value="{$subtitle|escape}" size="60" class="form-contorl">
	    </div>
    </div>
    <div class="form-group" id='show_linkto' {if $types.$type.show_linkto eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Source{/tr} ({tr}URL{/tr}) *</label>
		<div class="col-sm-7">
	      	<input type="text" name="linkto" value="{$linkto|escape}" size="60" class="form-control">{if $linkto neq ''}<a href="{$linkto|escape}" target="_blank">{tr}View{/tr}</a>{/if}
	    </div>
    </div>
	{if $prefs.feature_multilingual eq 'y'}
	<div class="form-group" id='show_lang'>
		<label class="col-sm-3 control-label">{tr}Language{/tr}</label>
		<div class="col-sm-7">
	      	<select name="lang" class="form-control">
				<option value="">{tr}All{/tr}</option>
				{section name=ix loop=$languages}
					<option value="{$languages[ix].value|escape}"{if $lang eq $languages[ix].value} selected="selected"{/if}>{$languages[ix].name}</option>
				{/section}
			</select>
	    </div>
    </div>
    {/if}	
    <div class="form-group" id='show_author' {if $types.$type.show_author eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Author Name{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="authorName" value="{$authorName|escape}" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Topic{/tr}</label>
		<div class="col-sm-7">
	      	<select name="topicId" class="form-control">
				<option value="" {if $topicId eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
				{foreach $topics as $topic}
					<option value="{$topic.topicId|escape}" {if $topicId eq $topic.topicId}selected="selected"{/if}>{$topic.name|escape}</option>
				{/foreach}
			</select>
			{if $tiki_p_admin_cms eq 'y'}
				<a href="tiki-admin_topics.php" class="link">{tr}Admin Topics{/tr}</a>
			{/if}
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Type{/tr}</label>
		<div class="col-sm-7">
	      	<select id='articletype' name='type' onchange='javascript:chgArtType();' class="form-control">
				{foreach from=$types key=typei item=prop}
					<option value="{$typei|escape}" {if $type eq $typei}selected="selected"{/if}>{tr}{$typei|escape}{/tr}</option>
				{/foreach}
			</select>
			{if $tiki_p_admin_cms eq 'y'}
				<a href="tiki-article_types.php" class="link">{tr}Admin Types{/tr}</a>
			{/if}
	    </div>
    </div>
    <div class="form-group" id='use_ratings' {if $types.$type.use_ratings eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Author rating{/tr}</label>
		<div class="col-sm-7">
	      	<select name='rating' class="form-control">
				<option value="10" {if $rating eq 10}selected="selected"{/if}>10</option>
				<option value="9.5" {if $rating eq "9.5"}selected="selected"{/if}>9.5</option>
				<option value="9" {if $rating eq 9}selected="selected"{/if}>9</option>
				<option value="8.5" {if $rating eq "8.5"}selected="selected"{/if}>8.5</option>
				<option value="8" {if $rating eq 8}selected="selected"{/if}>8</option>
				<option value="7.5" {if $rating eq "7.5"}selected="selected"{/if}>7.5</option>
				<option value="7" {if $rating eq 7}selected="selected"{/if}>7</option>
				<option value="6.5" {if $rating eq "6.5"}selected="selected"{/if}>6.5</option>
				<option value="6" {if $rating eq 6}selected="selected"{/if}>6</option>
				<option value="5.5" {if $rating eq "5.5"}selected="selected"{/if}>5.5</option>
				<option value="5" {if $rating eq 5}selected="selected"{/if}>5</option>
				<option value="4.5" {if $rating eq "4.5"}selected="selected"{/if}>4.5</option>
				<option value="4" {if $rating eq 4}selected="selected"{/if}>4</option>
				<option value="3.5" {if $rating eq "3.5"}selected="selected"{/if}>3.5</option>
				<option value="3" {if $rating eq 3}selected="selected"{/if}>3</option>
				<option value="2.5" {if $rating eq "2.5"}selected="selected"{/if}>2.5</option>
				<option value="2" {if $rating eq 2}selected="selected"{/if}>2</option>
				<option value="1.5" {if $rating eq "1.5"}selected="selected"{/if}>1.5</option>
				<option value="1" {if $rating eq 1}selected="selected"{/if}>1</option>
				<option value="0.5" {if $rating eq "0.5"}selected="selected"{/if}>0.5</option>
				<option value="0" {if $rating eq "0"}selected="selected"{/if}>0</option>
			</select>
	    </div>
    </div>
    <div class="form-group" id='show_image_1' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Own Image{/tr}</label>
		<div class="col-sm-7">
	      	<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
			<input name="userfile1" type="file" onchange="document.getElementById('useImage').checked = true;">
	    </div>
    </div>
    {if $hasImage eq 'y'}
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Own Image{/tr}</label>
		<div class="col-sm-7">
	      	{$image_name} [{$image_type}] ({$image_size} {tr}bytes{/tr})
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Own Image{/tr}</label>
		<div class="col-sm-7">
	      	{if $imageIsChanged eq 'y'}
				<img alt="{tr}Article image{/tr}" src="article_image.php?image_type=preview&amp;id={$previewId}">
			{else}
				<img alt="{tr}Article image{/tr}" src="article_image.php?image_type=submission&amp;id={$subId}">
			{/if}
	    </div>
    </div>
    {/if}

    <div class="form-group" id='show_image_2' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Use own image{/tr} *</label>
		<div class="col-sm-7">
	      	<input type="checkbox" name="useImage" id="useImage" {if $useImage eq 'y'}checked='checked'{/if}>
	    </div>
    </div>
    <div class="form-group" id='show_image_3' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Float text around image{/tr} *</label>
		<div class="col-sm-7">
	      	<input type="checkbox" name="isfloat" {if $isfloat eq 'y'}checked='checked'{/if}>
	    </div>
    </div>
    <div class="form-group" d='show_image_4' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Own image size x{/tr} *</label>
		<div class="col-sm-7">
	      	<input type="text" name="image_x" value="{$image_x|escape}" class="form-control">
			<div class="help-block">
				{tr}pixels{/tr}
			</div>
	    </div>
    </div>
    <div class="form-group" id='show_image_5' {if $types.$type.show_image eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Own image size y{/tr} *</label>
		<div class="col-sm-7">
	      	<input type="text" name="image_y" value="{$image_y|escape}" class="form-control">
			<div class="help-block">
				{tr}pixels{/tr}
			</div>
	    </div>
    </div>
    <div class="form-group" id='show_image_caption' {if $types.$type.show_image_caption eq 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Image caption{/tr} *</label>
		<div class="col-sm-7">
	      	<input type="text" name="image_caption" value="{$image_caption|escape}" size="60" class="form-control">
	    </div>
    </div>
    {if $prefs.feature_cms_templates eq 'y' and $tiki_p_use_content_templates eq 'y' and $templates|@count ne 0}
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Apply template{/tr} *</label>
		<div class="col-sm-7">
	      	<select name="templateId" onchange="javascript:document.getElementById('editpageform').submit();" class="form-control">
				<option value="0">{tr}none{/tr}</option>
				{section name=ix loop=$templates}
					<option value="{$templates[ix].templateId|escape}">{tr}{$templates[ix].name}{/tr}</option>
				{/section}
			</select>
	    </div>
    </div>
    {/if}

    {include file='categorize.tpl'}

    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Heading{/tr}</label>
		<div class="col-sm-7">
	      	{if $types.$type.heading_only eq 'y'}
				{textarea name="heading" rows="5" cols="80" Height="200px" id="subheading" class="form-control"}{$heading}{/textarea}
			{else}
				{textarea _simple="y" name="heading" rows="5" cols="95" Height="200px" id="subheading" comments="y"}{$heading}{/textarea}
			{/if}
	    </div>
    </div>
	<div id='heading_only' class="form-group  margin-side-0{if $types.$type.heading_only eq 'y'} hidden{/if}">
		<label class="col-sm-3 control-label">{tr}Body{/tr}</label>
		<div class="col-sm-7">
	      	{textarea name="body" id="body"}{$body}{/textarea}
	    </div>
    </div>
    <div class="form-group" id='show_pubdate' {if $types.$type.show_pubdate eq 'y' || $types.$type.show_pre_publ ne 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Publish Date{/tr}</label>
		<div class="col-sm-7">
	      	{html_select_date prefix="publish_" time=$publishDate start_year="-10" end_year="+10" field_order=$prefs.display_field_order}
			{tr}at{/tr}
			<span dir="ltr">
				{html_select_time prefix="publish_" time=$publishDate display_seconds=false use_24_hours=$use_24hr_clock}
				&nbsp;
				{$siteTimeZone}
			</span>
	    </div>
    </div>
    <div class="form-group" id='show_expdate' {if $types.$type.show_expdate eq 'y' || $types.$type.show_post_expire ne 'y'}style="display:;"{else}style="display:none;"{/if}>
		<label class="col-sm-3 control-label">{tr}Expiration Date{/tr}</label>
		<div class="col-sm-7">
	      	{html_select_date prefix="expire_" time=$expireDate start_year="-10" end_year="+10" field_order=$prefs.display_field_order}
			{tr}at{/tr}
			<span dir="ltr">
				{html_select_time prefix="expire_" time=$expireDate display_seconds=false use_24_hours=$use_24hr_clock}
				&nbsp;
				{$siteTimeZone}
			</span>
	    </div>
    </div>
    {if $tiki_p_use_HTML eq 'y'}
    	{if $smarty.session.wysiwyg neq 'y'}
	    <div class="form-group">
			<label class="col-sm-3 control-label">{tr}Allow full HTML{/tr} <em>({tr}Keep any HTML tag.{/tr})</em></label>
			<div class="col-sm-7">
		      	<input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}>
		      	<div class="help-block">
		      		<em>{tr}If not enabled, Tiki will retain some HTML tags (a, p, pre, img, hr, b, i){/tr}.</em>
		      	</div>
		    </div>
	    </div>
	    {else}
	    <input type="hidden" name="allowhtml" value="{if $allowhtml eq 'y'}on{/if}">
    	{/if}
	{/if}

	{if $prefs.feature_cms_emails eq 'y' and $articleId eq 0}
		<div class="form-group">
			<label class="col-sm-3 control-label">{tr}Emails to be notified (separated with commas){/tr}</label>
			<div class="col-sm-7">
		      	<input type="text" name="emails" value="{$emails|escape}" size="60" class="form-control">
		      	<br>
				{if !empty($userEmail) and $userEmail ne $prefs.sender_email}
					{tr}From:{/tr} {$userEmail|escape}
					<input type="radio" name="from" value="{$userEmail|escape}"{if empty($from) or $from eq $userEmail} checked="checked"{/if}>
					{$prefs.sender_email|escape}
					<input type="radio" name="from" value="{$prefs.sender_email|escape}"{if $from eq $prefs.sender_email} checked="checked"{/if}>
				{/if}
		    </div>
	    </div>
    {/if}

    {include file='freetag.tpl'}

    {if isset($all_attributes)}
		{foreach from=$all_attributes item=att key=attname}
		{assign var='attid' value=$att.itemId|replace:'.':'_'}
		{assign var='attfullname' value=$att.itemId}
		<div class="form-group"  id={$attid} {if $types.$type.$attid eq 'y'}style="display:;"{else}style="display:none;"{/if}>
			<label class="col-sm-3 control-label">{$attname|escape}</label>
			<div class="col-sm-7">
		      	<input type="text" name="{$attfullname}" value="{$article_attributes.$attfullname|escape}" size="60" maxlength="255" class="form-control">
		    </div>
	    </div>
	    {/foreach}
    {/if}


	<div align="center">
		{if $prefs.feature_antibot eq 'y'}<br><div align="center">{include file='antibot.tpl' antibot_table='y'}</div><br>{/if}
		<input type="submit" class="wikiaction btn btn-default" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;">
		<input type="submit" class="wikiaction btn btn-default" name="submitarticle" value="{tr}Submit Article{/tr}" onclick="needToConfirm=false;">
		{if $tiki_p_autoapprove_submission eq 'y'}
			<input type="submit" class="wikiaction btn btn-default" name="save" value="{tr}Auto-Approve Article{/tr}" onclick="needToConfirm=false;">
		{/if}
	</div>
{if $smarty.session.wysiwyg neq 'y'}
	{jq}
$("#editpageform").submit(function(evt) {
	var isHtml = false;
	if (this.saving && !$("input[name=allowhtml]:checked").length) {
		$("textarea", this).each(function(){
			if ($(this).val().match(/<([A-Z][A-Z0-9]*)\b[^>]*>(.*?)<\/\1>/i)) {
				isHtml = true;
			}
		});
		if (isHtml) {
			this.saving = false;
			return confirm(tr('You appear to be using HTML in your article but have not selected "Allow full HTML".\nThis will result in HTML tags being removed.\nDo you want to save your edits anyway?'));
		}
	}
	return true;
}).attr('saving', false);
	{/jq}
{/if}
</form>

<br>
