{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-siteid.tpl,v 1.6 2006-05-22 17:09:14 mose Exp $ *}

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}Don't forget: to use feature you will need to enable it on{/tr} <a class="rbox-link" href="tiki-admin.php?page=features">{tr}Admin->Features{/tr}</a></div>
</div>
<br />

<div class="cbox">
    <div class="cbox-title">
        {tr}{$crumbs[$crumb]->description}{/tr}
        {help crumb=$crumbs[$crumb]}
    </div>
	<div class="cbox-data">
		<form method="post" action="tiki-admin.php?page=siteid">
			<table class="admin">
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Custom Code{/tr}</td>
				</tr>	
				<tr>
					<td class="form"><label for="feature_sitemycode">{tr}Custom code{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitemycode" id="feature_sitemycode"{if $feature_sitemycode eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitemycode">&nbsp;&nbsp;{tr}Content{/tr}:</label></td>
					<td><textarea name="sitemycode" rows="6" style="width: 90%" id="sitemycode">{$sitemycode}</textarea>
					<br />
					<small><em>{tr}Example{/tr}</em>: 
					{literal}{if $user neq ''}{/literal}&lt;div align="right" style="float: right; font-size: 10px"&gt;{literal}{{/literal}tr}logged as{literal}{/tr}{/literal}: {literal}{$user}{/literal}&lt;/div&gt;{literal}{/if}{/literal}</small></td>
				</tr>
				<tr>
					<td class="form"><label for="sitemycode_publish">&nbsp;&nbsp;{tr}Publish{/tr}:</label></td>
					<td><input type="checkbox" name="sitemycode_publish" id="sitemycode_publish"{if $sitemycode_publish eq 'y'} checked="checked"{/if} /></td>
				</tr>
				
				<tr>
        	<td colspan="2"><hr/></td>
				</tr>
								
				<tr>
					<td class="form" colspan="2"><label for="alter_tiki_prefs_table"><strong>{tr}Important{/tr}:</strong><br />
					{tr}Tiki preferences value field in db is set to be max. 250 characters long by default until now. That applies for the custom code content too. Check this field if you want to update your preferences database table to support more than 250 chars (although it was tested and works fine with mysql, it's recommended to backup your data manually before any database update){/tr}:</label> <input type="checkbox" name="alter_tiki_prefs_table" id="alter_tiki_prefs_table" /></td>
				</tr>
				
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Site Breadcrumbs{/tr}</td>
				</tr>
				
				<tr>
                                        <td class="form"><label for="feature_breadcrumbs">{tr}Site breadcrumbs{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_breadcrumbs" id="feature_breadcrumbs"{if $feature_breadcrumbs eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
                			<td class="form"><label for="feature_siteloc">&nbsp;&nbsp;{tr}Site location bar{/tr}:</label></td>
					<td><select name="feature_siteloc" id="feature_siteloc">
					<option value="y" {if $feature_siteloc eq 'y'}selected="selected"{/if}>{tr}at top of page{/tr}</option>
					<option value="page" {if $feature_siteloc eq 'page'}selected="selected"{/if}>{tr}at top of center column{/tr}</option>
					<option value="n" {if $feature_siteloc eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
					</select></td>
				</tr>
				<tr>
					<td class="form"><label for="feature_siteloclabel">&nbsp;&nbsp;&nbsp;&nbsp;{tr}Prefix breadcrumbs with 'Location:' label?{/tr}</td>
					<td><input type="checkbox" name="feature_siteloclabel" id="feature_siteloclabel"{if $feature_siteloclabel eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
                                        <td class="form"><label for="feature_sitetitle">&nbsp;&nbsp;&nbsp;&nbsp;{tr}Larger font for{/tr}:</label></td>
                                        <td><select name="feature_sitetitle" id="feature_sitetitle">
                                        <option value="y" {if $feature_sitetitle eq 'y'}selected="selected"{/if}>{tr}entire location{/tr}</option>
                                        <option value="title" {if $feature_sitetitle eq 'title'}selected="selected"{/if}>{tr}page name{/tr}</option>
                                        <option value="n" {if $feature_sitetitle eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
                                        </select></td>
				</tr>
                                <tr>
                                        <td class="form"><label for="feature_sitedesc">&nbsp;&nbsp;{tr}Use page description:{/tr}</label></td>
					<td><select name="feature_sitedesc" id="feature_sitedesc">
					<option value="y" {if $feature_sitedesc eq 'y'}selected="selected"{/if}>{tr}at top of page{/tr}</option>
					<option value="page" {if $feature_sitedesc eq 'page'}selected="selected"{/if}>{tr}at top of center column{/tr}</option>
					<option value="n" {if $feature_sitedesc eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
					</select></td>
                                </tr>
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Site Search{/tr}</td>
				</tr>
				<tr> 
                                        <td class="form"><label for="feature_sitesearch">{tr}Site search bar{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_sitesearch" id="feature_sitesearch"{if $feature_sitesearch eq 'y'} checked="checked"{/if} /></td>
                                </tr>
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Site Logo{/tr}</td>
				</tr>
				
				<tr>
					<td class="form"><label for="feature_sitelogo">{tr}Site logo{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitelogo" id="feature_sitelogo"{if $feature_sitelogo eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_src">&nbsp;&nbsp;{tr}Site logo source{/tr}:</label></td>
					<td><input type="text" name="sitelogo_src" id="sitelogo_src" value="{$sitelogo_src}" size="60" style="width: 90%" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_bgcolor">&nbsp;&nbsp;{tr}Site logo background color{/tr}:</label></td>
					<td><input type="text" name="sitelogo_bgcolor" id="sitelogo_bgcolor" value="{$sitelogo_bgcolor}" size="15" maxlength="15" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_title">&nbsp;&nbsp;{tr}Site logo title (on mouse over){/tr}:</label></td>
					<td><input type="text" name="sitelogo_title" id="sitelogo_title" value="{$sitelogo_title}" size="50" maxlength="50" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_alt">&nbsp;&nbsp;{tr}Alt. description (e.g. for text browsers){/tr}:</label></td>
					<td><input type="text" name="sitelogo_alt" id="sitelogo_alt" value="{$sitelogo_alt}" size="50" maxlength="50" /></td>
				</tr>
				
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Site Ads and Banners{/tr}</td>
				</tr>
				
				<tr>
					<td class="form"><label for="feature_sitead">{tr}Site ads and banners{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitead" id="feature_sitead"{if $feature_sitead eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<mall>
					<td class="form"><label for="sitead">&nbsp;&nbsp;{tr}Content{/tr}:</label></td>
					<td><textarea name="sitead" rows="6" style="width: 40%" id="sitead">{$sitead}</textarea>
					<br />
					<small><em>{tr}Example{/tr}</em>: {literal}{banner zone='Test'}{/literal}</small></td>
				</tr>
				<tr>
					<td class="form"><label for="sitead_publish">&nbsp;&nbsp;{tr}Publish{/tr}:</label></td>
					<td><input type="checkbox" name="sitead_publish" id="sitead_publish"{if $sitead_publish eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Site Menu{/tr}</td>
				</tr>
				<tr> 
                                        <td class="form"><label for="feature_sitemenu">{tr}Site menu bar{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_sitemenu" id="feature_sitemenu"{if $feature_sitemenu eq 'y'} checked="checked"{/if} />{tr}Note: This feature also requires phplayers to be turned on in Admin->Features{/tr}</td>
                                </tr>
								
				<tr>
					<td colspan="2" class="button">
						<input type="submit" name="siteidentityset" value="{tr}Set features{/tr}" />
					</td>
				</tr>
				
			</table>
		</form>
	</div>
</div>
