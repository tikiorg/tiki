{* $Header: /cvsroot/tikiwiki/tiki/templates/fgal_listing_conf.tpl,v 1.2 2007-10-04 22:17:35 nyloth Exp $ *}

<tr class="formcolor">
<td>{tr}ID{/tr}</td>
<td><input type="checkbox" name="fgal_list_id"{if $prefs.fgal_list_id eq 'y'} checked="checked"{/if} /></td>
</tr><tr class="formcolor">

<td>{tr}Name{/tr}</td>
<td><input type="checkbox" name="fgal_list_name"{if $prefs.fgal_list_name eq 'y'} checked="checked"{/if} /></td>
</tr><tr class="formcolor">

<td>{tr}Description{/tr}</td>
<td><input type="checkbox" name="fgal_list_description"{if $prefs.fgal_list_description eq 'y'} checked="checked"{/if} /></td>
 </tr><tr class="formcolor">

<td>{tr}Type{/tr}</td>
<td><input type="checkbox" name="fgal_list_type"{if $prefs.fgal_list_type eq 'y'} checked="checked"{/if} /></td>
 </tr><tr class="formcolor">

<td>{tr}Created{/tr}</td>
<td><input type="checkbox" name="fgal_list_created"{if $prefs.fgal_list_created eq 'y'} checked="checked"{/if} /></td>
</tr><tr class="formcolor">

<td>{tr}Last modified{/tr}</td>
<td><input type="checkbox" name="fgal_list_lastmodif"{if $prefs.fgal_list_lastmodif eq 'y'} checked="checked"{/if} /></td>
</tr><tr class="formcolor">

<td>{tr}User{/tr}</td>
<td><input type="checkbox" name="fgal_list_user"{if $prefs.fgal_list_user eq 'y'} checked="checked"{/if} /></td>
</tr><tr class="formcolor">

<td>{tr}Files{/tr}</td>
<td><input type="checkbox" name="fgal_list_files"{if $prefs.fgal_list_files eq 'y'} checked="checked"{/if} /></td>
</tr><tr class="formcolor">

<td>{tr}Hits{/tr}</td>
<td><input type="checkbox" name="fgal_list_hits"{if $prefs.fgal_list_hits eq 'y'} checked="checked"{/if} /></td>
</tr><tr class="formcolor">

<td>{tr}Parent gallery{/tr}</td>
<td><input type="checkbox" name="fgal_list_parent"{if $prefs.fgal_list_parent eq 'y'} checked="checked"{/if} /></td>
</tr>

