{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Configuration Profiles Wizard{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" /></div>
{tr}Check out some commonly used configurations in Tiki sites{/tr}. </br></br>
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Profiles:{/tr}</legend>
	<table style="width:100%">
	<tr>
        <td style="width:48%">
            <div class="adminWizardIconright"><img src="img/icons/large/user_tracker48x48.png" alt="{tr}User & Registration Tracker{/tr}" /></div>
            <b>{tr}User & Registration Tracker{/tr}</b> (<a href="tiki-admin.php?profile=User_Trackers&show_details_for=User_Trackers&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br>
            {tr}This profile allows to set up a User Tracker, and you can decide which fields are shown in the registration form of your site, and/or which ones in the User Wizard, when you enable it to display and collect more information to your existing users.{/tr}
            {tr}It includes:{/tr}
            <ul>
                <li>{tr}A long list of predefined usual fields, to choose from{/tr}</li>
                <li>{tr}Some fields already prepared to display custom information from your specific site{/tr}</li>
                <li>{tr}The chance to easily customize it with the power of Trackers{/tr}</li>
                <br/><em>{tr}See also{/tr} <a href="https://doc.tiki.org/User+Tracker" target="_blank">{tr}User Tracker Wiki in doc.tiki.org{/tr}</a></em>
            </ul>
        </td>
        <td style="width:4%">
            &nbsp;
        </td>
        <td style="width:48%">
            <div class="adminWizardIconright"><img src="img/icons/large/profile_custom_contact_form48x48.png" alt="{tr}Custom Contact Form{/tr}" /></div>
            <b>{tr}Custom Contact Form{/tr}</b> (<a href="tiki-admin.php?profile=Custom_Contact_Form&show_details_for=Custom_Contact_Form&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br>
            {tr}This profile eases the task to create a custom contact form adapted to the specific case of that site.{/tr}
            <ul>
                <li>{tr}Enables Trackers and sets up a few fields to create a basic "contact us" form as a starting point{/tr}</li>
                <li>{tr}New fields can be added asking questions specific for the site{/tr}</li>
                <li>{tr}You decide where and when to display the link to the constact us form in your Tiki menus and pages{/tr}</li>
                <br/><em>{tr}See also{/tr} <a href="https://doc.tiki.org/Trackers" target="_blank">{tr}Trackers in doc.tiki.org{/tr}</a></em>
            </ul>
        </td>
	</tr>
	<tr>
        <td style="width:48%">
            <div class="adminWizardIconright"><img src="img/icons/large/profile_dynamic_items_list48x48.png" alt="{tr}Dynamic Items List{/tr}" /></div>
            <b>{tr}Dynamic Items List{/tr}</b> (<a href="tiki-admin.php?profile=Dynamic_items_list_demo&show_details_for=Dynamic_items_list_demo&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br>
            {tr}This profile creates two linked trackers. One that allow pre-selecting the items in a drop down list based on the items in a previous drop down field. And a second tracker that holds the options displayed in the drop down fields.{/tr}
            <a href="https://doc.tiki.org/Dynamic+items+list" target="tikihelp" class="tikihelp" title="{tr}Dynamic Items List{/tr}:
	<ul>
		<li>{tr}Useful for Geographic data (State, Country/Province, ...){/tr}</li>
	    <li>{tr}Useful for Types and Subtypes{/tr}</li>
	    <li>{tr}Useful for Program Names and Versions{/tr}</li>
	    <li>{tr}Easily manage the options in the second tracker without editing the dropdown in the first tracker{/tr} </li>
	</ul>">
                <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                <a href="http://tiki.org/display521" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                    <img src="http://tiki.org/display521"  width="100" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                </a>
                <div class="mini" style="width:100px;">
                    <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                </div>
            </div>
        </td>
        <td style="width:4%">
            &nbsp;
        </td>
        <td style="width:48%">
            <!--	<div class="adminWizardIconright"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Profile X{/tr}" /></div>
            <b>{tr}Profile X{/tr}</b> (<a href="tiki-admin.php?profile=Profile_X&show_details_for=Profile_X&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)<br/>
            <br>
            {tr}This profile allows to {/tr}
            <ul>
            <li>{tr}...{/tr}</li>
            <li>{tr}...{/tr}</li>
            <li>{tr}...{/tr}</li>
            <br/><em>{tr}See also{/tr} <a href="https://doc.tiki.org/Feature_X" target="_blank">{tr}Feature_X in doc.tiki.org{/tr}</a></em>
            </ul>
        -->
        </td>
	</tr>
	</table>
</fieldset>
<br>
</div>

