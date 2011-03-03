{* Debug console tab -- to display result of command *}

{* Display command results if we have smth to show... *}
{if $result_type ne 0} {*NO_RESULT *}

  <pre>&gt;&nbsp;{$command|escape:"html"}</pre>

  {if    $result_type == 1}{*TEXT_RESULT*}

    {* Show text in PRE section *}
    <pre>{strip}
      {$command_result|escape:"html"|wordwrap:90:"\n":true|replace:"\n":"<br />"}
    {/strip}</pre>

  {elseif $result_type == 2}{*HTML_RESULT*}

    {* Type HTML as is *}
    {$command_result}

  {elseif $result_type == 3 && strlen($result_tpl) > 0}{*TPL_RESULT*}

    {* Result have its own template *}
    {include file=$result_tpl}

  {/if}{* Check result type *}

<br />
{/if}{* We have smth to show as result *}
