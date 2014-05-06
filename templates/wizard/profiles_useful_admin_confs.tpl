{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Configuration Profiles Wizard{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" /></div>
{tr}Check out some useful changes in the configuration for site administrators to ease debugging{/tr}. </br></br>
<div class="adminWizardContent">
    <fieldset>
        <legend>{tr}Profiles:{/tr}</legend>
        <table style="width:100%">
            <tr>
                <td style="width:48%">
                    <div class="adminWizardIconright"><img src="img/icons/large/profile_debug_mode48x48.png" alt="{tr}Debug Mode Enabled{/tr}" /></div>
                    <b>{tr}Debug Mode Enabled{/tr}</b> (<a href="tiki-admin.php?profile=Debug_Mode_Enabled&show_details_for=Debug_Mode_Enabled&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)<br/>
                    <b>{tr}Debug Mode Disabled{/tr}</b> (<a href="tiki-admin.php?profile=Debug_Mode_Disabled&show_details_for=Debug_Mode_Disabled&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
                    <br>
                    {tr}Profile <i>Debug_Mode_Enabled</i> will help you detect potential errors and warnings which are hidden otherwise.{/tr}
                    {tr}Once applied, you might like to apply the opposite profile: <i>Debug_Mode_Disabled</i>, if not changing the appropriate settings by hand.{/tr}
                    <br/><a href="https://dev.tiki.org/Recovery" target="tikihelp" class="tikihelp" title="{tr}Debug Mode Enabled{/tr} & {tr}Debug Mode Disabled{/tr}:
           	{tr}More details{/tr}:
        	<ul>
        		<li>{tr}Enables/Disables debugging tools{/tr}</li>
        	    <li>{tr}Enables/Disables logging tools{/tr}</li>
        	    <li>{tr}Disables/Enables redirections to similar pages{/tr}</li>
        	    <li>{tr}Enables/Disables error and warning display to all users, not only admins{/tr} </li>
        	</ul>
            {tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                </td>
                <td style="width:4%">
                    &nbsp;
                </td>
                <td style="width:48%">
                    &nbsp;
                </td>
            </tr>
        </table>
    </fieldset>
    <br>
</div>
