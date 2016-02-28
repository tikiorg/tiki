{if $action eq 'add'}
    <a class="add-friend btn btn-default"
       href="{bootstrap_modal controller=social action=add_friend username=$userwatch}">
        <i class="fa fa-user-plus"></i>
        {$add_button_text}
    </a>
{elseif $action eq 'remove'}
    <a class="add-friend btn btn-default"
       href="{bootstrap_modal controller=social action=remove_friend friend=$userwatch}">
        <i class="fa fa-user-times"></i>
        {$remove_button_text}
    </a>
{/if}