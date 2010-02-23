<div align="center">
{assign value=1 var='display_login'} {* Hack to display the login module only once if it is also actually used as a module *}
{assign value=1 var='display_module'}
{include file='modules/mod-login_box.tpl'}
</div>
