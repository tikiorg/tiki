{if $preview}
{include file="tiki-preview_article.tpl"}
{/if}
<a class="pagetitle" href="tiki-edit_submission.php">{tr}Edit{/tr}: {$title}</a><br/><br/>
[<a class="link" href="tiki-list_submissions.php">list submissions</a>]
<br/><br/>
<form enctype="multipart/form-data" method="post" action="tiki-edit_submission.php" id='tikieditsubmission'>
<input type="hidden" name="subId" value="{$subId}" />
<input type="hidden" name="image_data" value="{$image_data}" />
<input type="hidden" name="useImage" value="{$useImage}" />
<input type="hidden" name="image_type" value="{$image_type}" />
<input type="hidden" name="image_name" value="{$image_name}" />
<input type="hidden" name="image_size" value="{$image_size}" />
<table class="normal">
<tr><td class="formcolor">{tr}Title{/tr}</td><td class="formcolor"><input type="text" name="title" value="{$title}" /></td></tr>
<tr><td class="formcolor">{tr}Author Name{/tr}</td><td class="formcolor"><input type="text" name="authorName" value="{$authorName}" /></td></tr>
<tr><td class="formcolor">{tr}Topic{/tr}</td><td class="formcolor">
<select name="topicId">
{section name=t loop=$topics}
<option value="{$topics[t].topicId}" {if $topicId eq $topics[t].topicId}selected="selected"{/if}>{$topics[t].name}</option>
{/section}
</select></td></tr>

<tr><td class="formcolor">{tr}Type{/tr}</td><td class="formcolor">
<select id='articletype' name='type' onChange='javascript:chgArtType();'>
<option value='Article' {if $type eq 'Article'}sselected="selected"{/if}>{tr}Article{/tr}</option>
<option value='Review' {if $type eq 'Review'}selected="selected"{/if}>{tr}Review{/tr}</option>
</select>
</select></td></tr>
<tr id='isreview' {if $type eq 'Article'}style="display:none;"{else}style="display:block;"{/if}><td class="formcolor">{tr}Rating{/tr}</td><td class="formcolor">
<select name='rating'>
<option value="10" {if $rating eq 10}selected="selected"{/if}>10</option>
<option value="9.5" {if $rating eq 9.5}selected="selected"{/if}>9.5</option>
<option value="9" {if $rating eq 9}selected="selected"{/if}>9</option>
<option value="8.5" {if $rating eq 8.5}selected="selected"{/if}>8.5</option>
<option value="8" {if $rating eq 8}selected="selected"{/if}>8</option>
<option value="7.5" {if $rating eq 7.5}selected="selected"{/if}>7.5</option>
<option value="7" {if $rating eq 7}selected="selected"{/if}>7</option>
<option value="6.5" {if $rating eq 6.5}selected="selected"{/if}>6.5</option>
<option value="6" {if $rating eq 6}selected="selected"{/if}>6</option>
<option value="5.5" {if $rating eq 5.5}selected="selected"{/if}>5.5</option>
<option value="5" {if $rating eq 5}selected="selected"{/if}>5</option>
<option value="4.5" {if $rating eq 4.5}selected="selected"{/if}>4.5</option>
<option value="4" {if $rating eq 4}selected="selected"{/if}>4</option>
<option value="3.5" {if $rating eq 3.5}selected="selected"{/if}>3.5</option>
<option value="3" {if $rating eq 3}selected="selected"{/if}>3</option>
<option value="2.5" {if $rating eq 2.5}selected="selected"{/if}>2.5</option>
<option value="2" {if $rating eq 2}selected="selected"{/if}>2</option>
<option value="1.5" {if $rating eq 1.5}selected="selected"{/if}>1.5</option>
<option value="1" {if $rating eq 1}selected="selected"{/if}>1</option>
<option value="0.5" {if $rating eq 0.5}selected="selected"{/if}>0.5</option>
</select>
</td></tr>


<tr><td class="formcolor">{tr}Own Image{/tr}</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="1000000">
<input name="userfile1" type="file"></td></tr>
{if $hasImage eq 'y'}
<tr><td class="formcolor">Own Image: </td><td class="formcolor">{$image_name} [{$image_type}] ({$image_size} bytes)</td></tr>
{if $tempimg ne 'n'}
<tr><td class="formcolor">Own Image:</td><td class="formcolor">
<img alt="theimage" border="0" src="{$tempimg}" {if $image_x > 0}width="{$image_x}"{/if}{if $image_y > 0 }height="{$image_y}"{/if}/>
</td></tr>
{/if}
{/if}
<tr><td class="formcolor">{tr}Use own image{/tr}</td><td class="formcolor">
<input type="checkbox" name="useImage" {if $useImage eq 'y'}checked='checked'{/if}/>
</td></tr>
<tr><td class="formcolor">{tr}Own image size x{/tr}</td><td class="formcolor"><input type="text" name="image_x" value="{$image_x}" /></td></tr>
<tr><td class="formcolor">{tr}Own image size y{/tr}</td><td class="formcolor"><input type="text" name="image_y" value="{$image_y}" /></td></tr>

{if $feature_cms_templates eq 'y' and $tiki_p_use_content_templates eq 'y'}
<tr><td class="formcolor">{tr}Apply template{/tr}</td><td class="formcolor">
<select name="templateId" onChange="javascript:document.getElementById('tikieditsubmission').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].templateId}">{tr}{$templates[ix].name}{/tr}</option>
{/section}
</select>
</td></tr>
{/if}


<tr><td class="formcolor">{tr}Heading{/tr}</td><td class="formcolor"><textarea class="wikiedit" id='subheading' name="heading" rows="5" cols="80" wrap="virtual">{$heading}</textarea></td></tr>
<tr><td class="formcolor">{tr}Quicklinks{/tr}</td><td class="formcolor">
{assign var=area_name value="subbody"}
{include file=tiki-edit_help_tool.tpl}
</td>
<tr><td class="formcolor">{tr}Body{/tr}</td><td class="formcolor"><textarea class="wikiedit" id='subbody' name="body" rows="25" cols="80" wrap="virtual">{$body}</textarea></td></tr>
{if $cms_spellcheck eq 'y'}
<tr><td class="formcolor">{tr}Spellcheck{/tr}: </td><td class="formcolor"><input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if}/></td>
{/if}
<tr><td class="formcolor">{tr}Publish Date{/tr}</td><td class="formcolor">
{html_select_date time=$publishDate end_year="+1"} at {html_select_time time=$publishDate display_seconds=false}
</td></tr>
</table>
{if $tiki_p_use_HTML eq 'y'}
<div align="center">{tr}Allow HTML{/tr}: <input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/></div>
{/if}
<div align="center">
<input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" />
</div>
</form>
<br/>
{include file=tiki-edit_help.tpl}