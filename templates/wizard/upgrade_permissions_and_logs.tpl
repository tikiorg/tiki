{* $Id: upgrade_permissions_and_logs.tpl 51355 2014-05-17 08:07:20Z xavidp $ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_upgrade48x48.png" alt="{tr}Upgrade Wizard{/tr}" title="{tr}Upgrade Wizard{/tr}"/></div>
{tr}New permissions and action log settings{/tr}.
<br/><br/>
<div class="adminWizardContent">

    <fieldset>
        <legend>{tr}Permissions{/tr}</legend>
        <div class="adminWizardIconright"><img src="img/icons/large/permissions48x48.png" alt="{tr}Permissions{/tr}" title="{tr}Permissions{/tr}"/></div>        <b>{tr}Wiki{/tr}</b>:
        <ul>
            <li>{tr}wiki{/tr} > {tr}Can inline edit pages{/tr} <em>(tiki_p_edit_inline)</em>
                <a href="http://doc.tiki.org/Wiki+Inline+Editing" target="tikihelp" class="tikihelp" title="{tr}Wiki Inline Editing:{/tr}
                {tr}Starting in Tiki12, Tiki offers the option to edit inline a wiki page in wysiwyg mode with a simplified editor, which is based on Ckeditor4{/tr}
                <br/><br/>
                {tr}The editor can be quickly turned on/off. All processing is done client side{/tr}
	    	">
                    <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                </a>
            </li>
        </ul>

        <b>{tr}Ratings{/tr}</b>:
        <ul>
            <li>{tr}tiki{/tr} > {tr}Can view results from user ratings{/tr} <em>(tiki_p_ratings_view_results)</em>
                <a href="http://doc.tiki.org/Ratings" target="tikihelp" class="tikihelp" title="{tr}Ratings:{/tr}
                {tr}Starting in Tiki12, Rating results can be selectively shown to just some user groups, as well as a few other new settings were introduced to fine tune the information shown{/tr}.
	    	">
                    <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                </a>
            </li>
        </ul>

        <b>{tr}BigBlueButton{/tr}</b>:
        <ul>
            <li>{tr}bigbluebutton{/tr} > {tr}Can view recordings from past meetings{/tr} <em>(tiki_p_bigbluebutton_view_rec)</em> <a href="http://doc.tiki.org/BigBlueButton" target="tikihelp" class="tikihelp" title="{tr}BigBlueButton:{/tr}
                {tr}New explicit permission tiki_p_bigbluebutton_view_rec needed to view recordings{/tr}
                <br/><br/>
                {tr}tiki_p_bigbluebutton_view_rec is no longer implicit if tiki_p_bigbluebutton_join is granted{/tr}
	    	">
                    <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                </a>
            </li>
        </ul>

        <b>{tr}Tiki{/tr}</b>:
        <ul>
            <li>{tr}tiki{/tr} > {tr}Can switch between wiki and WYSIWYG modes while editing{/tr} <em>(tiki_p_edit_switch_mode)</em>
                <a href="http://doc.tiki.org/Wysiwyg" target="tikihelp" class="tikihelp" title="{tr}Switch editor:{/tr}
                {tr}Starting in Tiki7, Tiki offers the option to allow users to switch the editor from plain text to wysiwyg and viceversa, provided that the user belongs to a group with this required permission granted{/tr}
	    	">
                    <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                </a>
            </li>
        </ul>
    </fieldset>
    <br/>

    <fieldset>
        <legend>{tr}Action log settings{/tr}</legend>
        <div class="adminWizardIconright"><img src="img/icons/large/logs48x48.png" alt="{tr}Logs{/tr}" title="{tr}Logs{/tr}"/></div>
        <b>{tr}BigBlueButton{/tr}</b>:
        <ul>
            <li>{tr}Joined Room{/tr}</li>
            <li>{tr}Left Room{/tr}</li>
        </ul>
    </fieldset>

</div>
