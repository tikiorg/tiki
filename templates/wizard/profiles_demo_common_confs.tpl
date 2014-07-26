{* $Id: profiles_demo_common_confs.tpl 51152 2014-05-05 08:27:09Z xavidp $ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Configuration Profiles Wizard{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" /></div>
{tr}Check out some commonly used configurations in Tiki sites{/tr}. </br></br>
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Profiles:{/tr}</legend>
	<table style="width:100%">
	<tr>
        <td style="width:48%">
            <b>{tr}User & Registration Tracker{/tr}</b> (<a href="tiki-admin.php?profile=User_Trackers&show_details_for=User_Trackers&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br>
            {tr}This profile allows you to request more details in the registration process or in the User Wizard, as well as to provide more custom information to your users{/tr}.
            <br/><a href="https://doc.tiki.org/User+Tracker"  target="tikihelp" class="tikihelp" title="{tr}User & Registration Tracker{/tr}:
            {tr}It includes:{/tr}
            <ul>
                <li>{tr}A long list of predefined usual fields, to choose from{/tr}</li>
                <li>{tr}Some fields already prepared to display custom information from your specific site{/tr}</li>
                <li>{tr}The chance to easily customize it with the power of Trackers{/tr}</li>
            </ul>
        	{tr}Click to read more{/tr}">
                <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                <a href="http://tiki.org/display542" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                    <img src="img/profiles/profile_thumb_user_and_registration_tracker.png"  width="150" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                </a>
                <div class="mini" style="width:100px;">
                    <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                </div>
            </div>
            <br/>
        </td>
        <td style="width:4%">
            &nbsp;
        </td>
        <td style="width:48%">
            <b>{tr}Custom Contact Form{/tr}</b> (<a href="tiki-admin.php?profile=Custom_Contact_Form_12x&show_details_for=Custom_Contact_Form_12x&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br>
            {tr}This profile eases the task to create a custom contact form adapted to the specific case of that site.{/tr}
            <br/><a href="https://doc.tiki.org/Trackers"  target="tikihelp" class="tikihelp" title="{tr}Custom Contact Form{/tr}:
           	{tr}More details{/tr}:
            <ul>
                <li>{tr}Enables Trackers and sets up a few fields to create a basic 'contact us' form as a starting point{/tr}</li>
                <li>{tr}New fields can be added asking questions specific for the site{/tr}</li>
                <li>{tr}You decide where and when to display the link to the contact us form in your Tiki menus and pages{/tr}</li>
            </ul>
            {tr}Click to read more{/tr}">
                <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                <a href="http://tiki.org/display543" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                    <img src="img/profiles/profile_thumb_custom_contact_form.png"  width="150" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                </a>
                <div class="mini" style="width:100px;">
                    <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                </div>
            </div>
            <br/>
        </td>
	</tr>
	<tr>
        <td style="width:48%">
            <b>{tr}Dynamic Items List{/tr}</b> (<a href="tiki-admin.php?profile=Dynamic_items_list_demo&show_details_for=Dynamic_items_list_demo&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br>
            {tr}This profile creates two linked trackers. One that allow pre-selecting the items in a drop down list based on the items in a previous drop down field. And a second tracker that holds the options displayed in the drop down fields.{/tr}
            <a href="https://doc.tiki.org/Dynamic+items+list" target="tikihelp" class="tikihelp" title="{tr}Dynamic Items List{/tr}:
           	{tr}More details{/tr}:
        	<ul>
		        <li>{tr}Useful for Geographic data (State, Country/Province, ...){/tr}</li>
	            <li>{tr}Useful for Types and Subtypes{/tr}</li>
	            <li>{tr}Useful for Program Names and Versions{/tr}</li>
	            <li>{tr}Easily manage the options in the second tracker without editing the dropdown in the first tracker{/tr} </li>
	        </ul>
        	{tr}Click to read more{/tr}">
                <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                <a href="http://tiki.org/display521" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                    <img src="img/profiles/profile_thumb_dynamic_items_list.png"  width="150" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
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
            <b>{tr}Sortable Tables{/tr}</b> (<a href="tiki-admin.php?profile=Sortable+Tables&show_details_for=Sortable+Tables&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
            <br/>
            {tr}This profile creates a few examples of tables with data that can be sorted and filtered interactively in real time, using the JQuery Sortable Tables feature{/tr}.
            <br/>
            <a href="https://doc.tiki.org/PluginFancyTable" target="tikihelp" class="tikihelp" title="{tr}Sortable Tables{/tr}:
           	{tr}More details{/tr}:
        	<ul>
		        <li>{tr}Useful to sort and filter data in real time{/tr}</li>
	            <li>{tr}Same approach for FancyTable and TrackerList Plugins{/tr}</li>
	            <li>{tr}Sorting can be server-side or client-side based {/tr}</li>
	        </ul>
        	{tr}Click to read more{/tr}">
                <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
            </a>
            <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                <a href="http://tiki.org/display548" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                    <img src="img/profiles/profile_thumb_sortable_tables.png"  width="150" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                </a>
                <div class="mini" style="width:100px;">
                    <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                </div>
            </div>
        </td>
	</tr>
	</table>
</fieldset>
<br>
</div>

