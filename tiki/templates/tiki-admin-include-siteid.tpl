{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-siteid.tpl,v 1.2 2005-01-22 22:56:19 mose Exp $ *}
{if $feature_help eq 'y'}
<div class="simplebox">{tr}Tip{/tr}: {tr}Dont forget to use feature you will need to enable it on the <a href="tiki-admin.php?page=features">Features</a> icon.{/tr} </div><br />
{/if}
<div class="cbox">
	<div class="cbox-title">
		{tr}Site Identity Settings{/tr}
	</div>
	<div class="cbox-data">
		<form method="post" action="tiki-admin.php?page=siteid">
			<table class="admin">
			
				<tr>
					<td class="form"><label for="feature_sitemycode">{tr}Custom code{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitemycode" id="feature_sitemycode"{if $feature_sitemycode eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitemycode">{tr}Content{/tr}:</label></td>
					<td><textarea name="sitemycode" rows="6" style="width: 90%" id="sitemycode">{$sitemycode}</textarea>
					<br />
					{tr}Example{/tr}: 
					{literal}{if $user neq ''}{/literal}&lt;div align="right" style="float: right; font-size: 10px"&gt;{literal}{{/literal}tr}logged as{literal}{/tr}{/literal}: {literal}{$user}{/literal}&lt;/div&gt;{literal}{/if}{/literal}</td>
				</tr>
				<tr>
					<td class="form"><label for="sitemycode_publish">{tr}Publish{/tr}:</label></td>
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
        	<td colspan="2"><hr/></td>
				</tr>
				
				<tr>
					<td class="form"><label for="feature_siteloc">{tr}Site location bar{/tr}:</label></td>
					<td><input type="checkbox" name="feature_siteloc" id="feature_siteloc"{if $feature_siteloc eq 'y'} checked="checked"{/if} /></td>
				</tr>
				
				<tr>
        	<td colspan="2"><hr/></td>
				</tr>
				
				<tr>
					<td class="form"><label for="feature_sitelogo">{tr}Site logo{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitelogo" id="feature_sitelogo"{if $feature_sitelogo eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_src">{tr}Site logo source{/tr}:</label></td>
					<td><input type="text" name="sitelogo_src" id="sitelogo_src" value="{$sitelogo_src}" size="60" style="width: 90%" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_bgcolor">{tr}Site logo background color{/tr}:</label></td>
					<td><input type="text" name="sitelogo_bgcolor" id="sitelogo_bgcolor" value="{$sitelogo_bgcolor}" size="15" maxlength="15" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_title">{tr}Site logo title (on mouse over){/tr}:</label></td>
					<td><input type="text" name="sitelogo_title" id="sitelogo_title" value="{$sitelogo_title}" size="50" maxlength="50" /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitelogo_alt">{tr}Alt. description (e.g. for text browsers){/tr}:</label></td>
					<td><input type="text" name="sitelogo_alt" id="sitelogo_alt" value="{$sitelogo_alt}" size="50" maxlength="50" /></td>
				</tr>
				
				<tr>
        	<td colspan="2"><hr/></td>
				</tr>
				
				<tr>
					<td class="form"><label for="feature_sitead">{tr}Site ads and banners{/tr}:</label></td>
					<td><input type="checkbox" name="feature_sitead" id="feature_sitead"{if $feature_sitead eq 'y'} checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td class="form"><label for="sitead">{tr}Content{/tr}:</label></td>
					<td><textarea name="sitead" rows="6" style="width: 40%" id="sitead">{$sitead}</textarea>
					<br />
					{tr}Example{/tr}: {literal}{banner zone='Test'}{/literal}</td>
				</tr>
				<tr>
					<td class="form"><label for="sitead_publish">{tr}Publish{/tr}:</label></td>
					<td><input type="checkbox" name="sitead_publish" id="sitead_publish"{if $sitead_publish eq 'y'} checked="checked"{/if} /></td>
				</tr>
								
				<tr>
					<td colspan="2" class="button">
						<input type="submit" name="siteidentityset" value="{tr}Change preferences{/tr}" />
					</td>
				</tr>
				
			</table>
		</form>
	</div>
</div>
