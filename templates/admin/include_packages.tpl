{* $Id$ *}

{if isset($composer_output)}
    {remarksbox type="note" title="{tr}Note{/tr}"}

    {tr}The following list of changes has been applied:{/tr}<br />
        <pre>{$composer_output}</pre>
    {/remarksbox}
{/if}

{tabset name='tabs_admin-packages'}
    {tab name="{tr}Packages Installed{/tr}"}
        <br />
        <h4>{tr}Composer Packages{/tr} <small>{tr}status of the packages registered in the composer.json file{/tr}</small></h4>
        {if ! $composer_available}
            {remarksbox type="warning" title="{tr}Composer not found{/tr}"}
                {tr}Composer could not be executed, so the automated check on the packages can not be performed{/tr}
            {/remarksbox}
        {/if}
        <table class="table">
            <tr>
                <th>{tr}Package Name{/tr}</th>
                <th>{tr}Version Required{/tr}</th>
                <th>{tr}Status{/tr}
                <th>{tr}Version Installed{/tr}</th>
                <th>{tr}Remove{/tr}</th>
            </tr>
            {foreach item=entry from=$composer_packages_installed}
                <tr>
                    <td>{$entry.name}</td>
                    <td>{$entry.required}</td>
                    <td>
                        {if $entry.status == 'installed'}
                            {icon name='success' iclass='tips' ititle="{tr}Status{/tr}:{tr}Installed{/tr}"}
                        {elseif $entry.status == 'missing'}
                            {icon name='warning' iclass='tips' ititle="{tr}Status{/tr}:{tr}Missing{/tr}"}
                        {else}
                            &nbsp;
                        {/if}
                    </td>
                    <td>{$entry.installed|default:'&nbsp;'}</td>
                    <td>{if $entry.installed && $entry.key}
                            <form action="tiki-admin.php?page=packages&cookietab=1" method="post">
                                <input type="hidden" name="redirect" value="0">
                                {ticket}
                                <button name="auto-remove-package" value="{$entry.key}">{tr}Remove{/tr}</button>
                            </form>
                        {else}
                            &nbsp;
                        {/if}
                    </td>
                </tr>
            {/foreach}
            {if $composer_packages_missing}
                <tr>
                    <td colspan="4">
                        <h4>{tr}Looks like there are packages missing{/tr}</h4>
                        {tr}In the list above some of the packages could not be found, they are defined in the composer.json, but do not seem to be installed{/tr}

                        <br />

                        <h4>{tr}Install packages from the administrator interface{/tr}</h4>
                        {if $composer_available}
                            <p>
                            {tr}You can use the administrator interface to install the marked as missing in the list above{/tr},
                            {tr}just click the "Fix Missing Packages" button bellow, and Tiki will try to do the installation for you{/tr}:
                            </p>
                            <form action="tiki-admin.php?page=packages&cookietab=1" method="post">
                                <input type="hidden" name="redirect" value="0">
                                {ticket}
                                <button name="auto-fix-missing-packages" value="auto-fix-missing-packages">{tr}Fix Missing Packages{/tr}</button>
                            </form>
                            <br />
                            The results of the execution of the commands will be displayed back to you after the process finish.
                        {else}
                            {tr}Composer was not detected, you need to follow the manual instructions{/tr}
                        {/if}

                        <br />

                        <h4>{tr}Install packages Manually{/tr}</h4>
                        <p><strong>{tr}Make sure <code>composer</code> is installed{/tr}</strong></p>
                        <p>
                            {tr}You can install composer manually, in the host machine, by following the instructions from{/tr}
                            <a href="https://getcomposer.org/">Composer</a> {tr}website{/tr}
                        </p>
                        <p>
                            {tr}You can also use <code>setup.sh</code> that is included in the Tiki distribution to make sure composer is installed for you, in this case composer will be installed as <code>temp/composer.phar</code>{/tr}.
                            {tr}Bellow is a example how to do this in a linux like operating system{/tr}: <br />
                            <code>bash ./setup.sh composer</code>
                        </p>

                        <p><strong>{tr}Install the missing packages{/tr}</strong></p>
                        <p>
                            {tr}After you have composer installed you can install the missing packages by issuing the command{/tr}
                            <code>composer --no-dev --prefer-dist update nothing</code>.
                            {tr}Bellow is a example how to do this in a linux like operating system{/tr}: <br />
                            <code>php temp/composer.phar --no-dev --prefer-dist update nothing</code>
                        </p>
                    </td>
                </tr>
            {/if}
        </table>
    {/tab}
    {tab name="{tr}Install Other Packages{/tr}"}
        <br />
        <h4>{tr}Composer Packages{/tr} <small>{tr}these packages have been identified as a dependency of one or more features{/tr}</small></h4>
        <table class="table">
            <tr>
                <th>{tr}Package Name{/tr}</th>
                <th>{tr}Version{/tr}</th>
                <th>{tr}Licence{/tr}</th>
                <th>{tr}Required By{/tr}</th>
                <th>{tr}Install{/tr}</th>
            </tr>
            {foreach item=entry from=$composer_packages_available}
                <tr>
                    <td>{$entry.name}</td>
                    <td>{$entry.requiredVersion}</td>
                    <td><a href="{$entry.licenceUrl}">{if empty($entry.licence)}{tr}Not Available{/tr}{else}{$entry.licence}{/if}</a></td>
                    <td>{', '|implode:$entry.requiredBy}</td>
                    <td>
                        <form action="tiki-admin.php?page=packages&cookietab=2" method="post">
                            <input type="hidden" name="redirect" value="0">
                            {ticket}
                            <button name="auto-install-package" value="{$entry.key}">{tr}Install Package{/tr}</button>
                        </form>
                    </td>
                </tr>
            {/foreach}
            {if count($composer_packages_available)}
                <tr>
                    <td colspan="5">
                        <h4>{tr}Looks like there are some optional packages that you can install{/tr}</h4>
                        {tr}In the list above there are some optional packages that you might want to install if you want to use the Tiki Wiki features that require that package{/tr}

                        <br />

                        <h4>{tr}Install packages from the administrator interface{/tr}</h4>
                        {if $composer_available}
                            {tr}You can use the administrator interface to install the optional packages in the list above{/tr},
                            {tr}just click the "Install Package" button, and Tiki will try to do the installation for you{/tr}
                        {else}
                            {tr}Composer was not detected, you need to follow the manual instructions{/tr}
                        {/if}

                        <br />

                        <h4>{tr}Install packages Manually{/tr}</h4>
                        <p><strong>{tr}Make sure <code>composer</code> is installed{/tr}</strong></p>
                        <p>
                            {tr}You can install composer manually, in the host machine, by following the instructions from{/tr}
                            <a href="https://getcomposer.org/">Composer</a> {tr}website{/tr}
                        </p>
                        <p>
                            {tr}You can also use <code>setup.sh</code> that is included in the Tiki distribution to make sure composer is installed for you, in this case composer will be installed as <code>temp/composer.phar</code>{/tr}.
                            {tr}Below is an example of how to do this in a linux like operating system{/tr}: <br />
                            <code>bash ./setup.sh composer</code>
                        </p>

                        <p><strong>{tr}Make sure you have a <code>composer.json</code> file in the root of the website{/tr}</strong></p>
                        <p>
                            {tr}If you do not have already a <code>composer.json</code> file, then create one now.{/tr}
                            {tr}You can optionally use the sample <code>composer.json.dist</code> that comes with Tiki as a starting point.{/tr}
                            {tr}Below is an example of how to do this in a linux like operating system{/tr}: <br />
                            <code>cp composer.json.dist composer.json</code>
                        </p>

                        <p><strong>{tr}Install the package{/tr}</strong></p>
                        <p>
                            {tr}After all the steps above (that only need to be performed once), you can install packages by issuing a command{/tr}
                            <code>composer require package:version</code> {tr}for each package that you would like get installed.{/tr}
                            {tr}Below is an example of how to do this in a linux like operating system{/tr}: <br />
                            <code>php temp/composer.phar require --update-no-dev --prefer-dist psr/log:^1.0</code>
                        </p>
                    </td>
                </tr>
            {/if}
        </table>
    {/tab}
    {tab name="{tr}Packages Bundled{/tr}"}
        <br />
        <h4>{tr}Composer Packages Bundled{/tr} <small>{tr}status of the packages registered in the vendor_bundled/composer.json file{/tr}</small></h4>
        {if ! $composer_available}
            {remarksbox type="warning" title="{tr}Composer not found{/tr}"}
            {tr}Composer could not be executed, so the automated check on the packages can not be performed{/tr}
            {/remarksbox}
        {else}
            {remarksbox type="info" title="{tr}For information only{/tr}"}
            {tr}This list of packages are bundled with Tiki Wiki, and displayed here for informational purpose{/tr}
            {/remarksbox}
        {/if}
        <table class="table">
            <tr>
                <th>{tr}Package Name{/tr}</th>
                <th>{tr}Version Required{/tr}</th>
                <th>{tr}Status{/tr}
                <th>{tr}Version Installed{/tr}</th>
            </tr>
            {foreach item=entry from=$composer_bundled_packages_installed}
                <tr>
                    <td>{$entry.name}</td>
                    <td>{$entry.required}</td>
                    <td>
                        {if $entry.status == 'installed'}
                            {icon name='success' iclass='tips' ititle="{tr}Status{/tr}:{tr}Installed{/tr}"}
                        {elseif $entry.status == 'missing'}
                            {icon name='warning' iclass='tips' ititle="{tr}Status{/tr}:{tr}Missing{/tr}"}
                        {else}
                            &nbsp;
                        {/if}
                    </td>
                    <td>{$entry.installed|default:'&nbsp;'}</td>
                </tr>
            {/foreach}
        </table>
    {/tab}
    {tab name="{tr}Diagnose{/tr}"}
        <br />
        <h4>{tr}Diagnose Composer Installation{/tr} <small>{tr}use the button bellow to test your composer installation{/tr}</small></h4>
        <form action="tiki-admin.php?page=packages&cookietab=4" method="post">
            <input type="hidden" name="redirect" value="0">
            {ticket}
            <button name="auto-run-diagnostics" value="run">{tr}Diagnose Composer{/tr}</button>
        </form>
        {if isset($diagnostic_composer_location) || $diagnostic_composer_output}
            <br />
            <h4>Results</h4>
            {if isset($diagnostic_composer_location) }
                <p><strong>Composer:</strong> {if $diagnostic_composer_location}{tr}{$diagnostic_composer_location}{/tr}{else}{tr}Composer not found{/tr}{/if}</p>
            {/if}
            {if $diagnostic_composer_output}
                <p><strong>Composer diagnose output</strong></p>
                <pre>{$diagnostic_composer_output}</pre>
            {/if}
            <br />
        {/if}
    {/tab}
{/tabset}