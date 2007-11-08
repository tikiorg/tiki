{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-siteid.tpl,v 1.19.2.1 2007-11-08 21:15:50 ntavares Exp $ *}

<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>
	<div class="rbox-data" name="tip">{tr}Don't forget: to use the feature you will need to enable it on{/tr} <a class="rbox-link" href="tiki-admin.php?page=features">{tr}Admin{/tr}&nbsp;{$prefs.site_crumb_seper}&nbsp;{tr}Features{/tr}</a></div>
</div>

<div class="cbox">
    <div class="cbox-title">
        {tr}{$crumbs[$crumb]->description}{/tr}
        {help crumb=$crumbs[$crumb]}
    </div>
	<div class="cbox-data">
		<form method="post" action="tiki-admin.php?page=siteid">
			<table class="admin">
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Custom Site Header{/tr}</td>
				</tr>	
				<tr>
					<td class="form"><label for="feature_sitemycode">{tr}Activate{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitemycode" id="feature_sitemycode"{if $prefs.feature_sitemycode eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitemycode">{tr}Content{/tr}:</label></td>
					<td><textarea name="sitemycode" rows="6" style="width: 90%" id="sitemycode">{$prefs.sitemycode|escape}</textarea>
					<br />
					<small><em>{tr}Example{/tr}</em>: 
					{literal}{if $user neq ''}{/literal}&lt;div align="right" style="float: right; font-size: 10px"&gt;{literal}{{/literal}tr}{tr}logged as{/tr}{literal}{/tr}{/literal}: {literal}{$user}{/literal}&lt;/div&gt;{literal}{/if}{/literal}</small></td>
				</tr>
				<tr>
					<td class="form"><label for="sitemycode_publish">{tr}Publish{/tr}:</label></td>
					<td><input type="checkbox" name="sitemycode_publish" id="sitemycode_publish"{if $prefs.sitemycode_publish eq 'y'} checked="checked"{/if} /></td>
				</tr>
				
				<tr>
        	<td colspan="2"><hr/></td>
				</tr>
								
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Site Breadcrumbs{/tr}</td>
				</tr>
				
				<tr>
                                        <td class="form"><label for="feature_breadcrumbs">{tr}Activate{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_breadcrumbs" id="feature_breadcrumbs"{if $prefs.feature_breadcrumbs eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
                			<td class="form"><label for="feature_siteloc">{tr}Site location bar{/tr}:</label></td>
					<td><select name="feature_siteloc" id="feature_siteloc">
					<option value="y" {if $prefs.feature_siteloc eq 'y'}selected="selected"{/if}>{tr}at top of page{/tr}</option>
					<option value="page" {if $prefs.feature_siteloc eq 'page'}selected="selected"{/if}>{tr}at top of center column{/tr}</option>
					<option value="n" {if $prefs.feature_siteloc eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
					</select></td>
				</tr>
				<tr>
					<td class="form"><label for="feature_siteloclabel">{tr}Prefix breadcrumbs with 'Location:' label?{/tr}</td>
					<td><input type="checkbox" name="feature_siteloclabel" id="feature_siteloclabel"{if $prefs.feature_siteloclabel eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
                                        <td class="form"><label for="feature_sitetitle">{tr}Larger font for{/tr}:</label></td>
                                        <td><select name="feature_sitetitle" id="feature_sitetitle">
                                        <option value="y" {if $prefs.feature_sitetitle eq 'y'}selected="selected"{/if}>{tr}entire location{/tr}</option>
                                        <option value="title" {if $prefs.feature_sitetitle eq 'title'}selected="selected"{/if}>{tr}page name{/tr}</option>
                                        <option value="n" {if $prefs.feature_sitetitle eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
                                        </select></td>
				</tr>
                                <tr>
                                        <td class="form"><label for="feature_sitedesc">{tr}Use page description:{/tr}</label></td>
					<td><select name="feature_sitedesc" id="feature_sitedesc">
					<option value="y" {if $prefs.feature_sitedesc eq 'y'}selected="selected"{/if}>{tr}at top of page{/tr}</option>
					<option value="page" {if $prefs.feature_sitedesc eq 'page'}selected="selected"{/if}>{tr}at top of center column{/tr}</option>
					<option value="n" {if $prefs.feature_sitedesc eq 'n'}selected="selected"{/if}>{tr}none{/tr}</option>
					</select></td>
                                </tr>
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Site Search{/tr}</td>
				</tr>
				<tr> 
                                        <td class="form"><label for="feature_sitesearch">{tr}Activate{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_sitesearch" id="feature_sitesearch"{if $prefs.feature_sitesearch eq 'y'} checked="checked"{/if} /></td>
                                </tr>
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Site Logo{/tr}</td>
				</tr>
				
				<tr>
					<td class="form"><label for="feature_sitelogo">{tr}Activate{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitelogo" id="feature_sitelogo"{if $prefs.feature_sitelogo eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_src">{tr}Site logo source (image path){/tr}:</label></td>
					<td><input type="text" name="sitelogo_src" id="sitelogo_src" value="{$prefs.sitelogo_src}" size="60" style="width: 90%" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_bgcolor">{tr}Site logo background color{/tr}:</label></td>
					<td><input type="text" name="sitelogo_bgcolor" id="sitelogo_bgcolor" value="{$prefs.sitelogo_bgcolor}" size="15" maxlength="15" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_align">{tr}Site logo alignment{/tr}:</label></td>
					<td><select name="sitelogo_align" id="sitelogo_align">
						<option value="left" {if $prefs.sitelogo_align eq 'left'}selected="selected"{/if}>{tr}on left side{/tr}</option>
						<option value="center" {if $prefs.sitelogo_align eq 'center'}selected="selected"{/if}>{tr}on center{/tr}</option>
						<option value="right" {if $prefs.sitelogo_align eq 'right'}selected="selected"{/if}>{tr}on right side{/tr}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_title">{tr}Site logo title (on mouse over){/tr}:</label></td>
					<td><input type="text" name="sitelogo_title" id="sitelogo_title" value="{$prefs.sitelogo_title}" size="50" maxlength="50" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_alt">{tr}Alt. description (e.g. for text browsers){/tr}:</label></td>
					<td><input type="text" name="sitelogo_alt" id="sitelogo_alt" value="{$prefs.sitelogo_alt}" size="50" maxlength="50" /></td>
				</tr>
				
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Site Ads and Banners{/tr}</td>
				</tr>
				
				<tr>
					<td class="form"><label for="feature_sitead">{tr}Activate{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitead" id="feature_sitead"{if $prefs.feature_sitead eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitead">{tr}Content{/tr}:</label></td>
					<td><textarea name="sitead" rows="6" style="width: 90%" id="sitead">{$prefs.sitead|escape}</textarea>
					<br />
					<small><em>{tr}Example{/tr}</em>: {literal}{banner zone='{/literal}{tr}Test{/tr}{literal}'}{/literal}</small></td>
				</tr>
				<tr>
					<td class="form"><label for="sitead_publish">{tr}Publish{/tr}:</label></td>
					<td><input type="checkbox" name="sitead_publish" id="sitead_publish"{if $prefs.sitead_publish eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="heading" colspan="2" align="center">{tr}Top bar{/tr}</td>
				</tr>
				<tr> 
                                        <td class="form"><label for="feature_sitemenu">{tr}Site menu bar{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_sitemenu" id="feature_sitemenu"{if $prefs.feature_sitemenu eq 'y'} checked="checked"{/if} />{tr}Note: This feature also requires PHPLayers to be turned on in {/tr}{tr}Admin{/tr}&nbsp;{$prefs.site_crumb_seper}&nbsp;{tr}Features{/tr}</td>
                                </tr>
				<tr> 
                                        <td class="form"><label for="feature_topbar_id_menu">{tr}Menu ID{/tr}:</label></td>
                                        <td><input type="text" name="feature_topbar_id_menu" id="feature_topbar_id_menu" value="{$prefs.feature_topbar_id_menu}" size="6" maxlength="6" /></td>
                                </tr>
				<tr> 
                                        <td class="form"><label for="feature_topbar_version">{tr}Current Version{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_topbar_version" id="feature_topbar_version"{if $prefs.feature_topbar_version eq 'y'} checked="checked"{/if} /></td>
                                </tr>
				<tr> 
                                        <td class="form"><label for="feature_topbar_date">{tr}Date{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_topbar_date" id="feature_topbar_date"{if $prefs.feature_topbar_date eq 'y'} checked="checked"{/if} /></td>
                                </tr>
				<tr> 
                                        <td class="form"><label for="feature_topbar_debug">{tr}Debugger Console{/tr}:</label></td>
                                        <td><input type="checkbox" name="feature_topbar_debug" id="feature_topbar_debug"{if $prefs.feature_topbar_debug eq 'y'} checked="checked"{/if} /></td>
                                </tr>
							
				<tr>
					<td class="heading" colspan="2"
                                        align="center">{tr}Custom Site Footer{/tr}</td>
				</tr>
				<tr>
					<td class="form"><label for="feature_bot_logo">{tr}Activate{/tr}:</label></td>
                                          <td><input type="checkbox" name="feature_bot_logo" id="feature_bot_logo"{if $prefs.feature_bot_logo eq 'y'} checked="checked"{/if} /></td>
				</tr>

				<tr>
					<td class="form"><label for="bot_logo_code">{tr}Content{/tr}:</label></td>
					<td><textarea name="bot_logo_code" rows="6" style="width: 90%">{$prefs.bot_logo_code|escape}</textarea>
					<br />
					<small><em>{tr}Example{/tr}</em>:&lt;div style="text-align: center"&gt;&lt;small&gt;Powered by Tikiwiki&lt;/small&gt;&lt;/div&gt;</small></td>
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
