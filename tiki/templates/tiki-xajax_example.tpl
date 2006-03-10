{$xajax_js}

<h2>This page ilustrates ajax funcionality in Tiki using the XAJAX Framework</h2>
<br />
The Xajax framework has many advantages such as:
<ul>
<li>You don't need to specify the datatypes;</li>
<li>Easy integration with Smarty, the framework generates the JS source, you just need to register the function;</li>
<li>You don't need to create a file per function;</li>
<li>You can get all data from the forms using the function xajax_getFormValues('form')</li>
</ul>

<br />
It is a short example which translate a string using xajax framework. When you click in translate, the xajax execute the function tra_ajax (it is in .php file) that returns the translated content.<br />
Interesting: in function tra_ajax, using a xajax object, you just need to specify how this content returns. In this case I've returned it applying the content on id "result" (spam id="result"&gt).
<br /><br />
<h5>Translate a string:</h5>
<form id="frmajax" name="frmajax" >
{tr}String{/tr}: &nbsp;<input type="text" name="str" id="str" value="Admin" />
<input type="text" name="lang" id="lang" value="pt-br" size="4"/>
<input type="button" value="{tr}translate{/tr}" onclick="xajax_tra_ajax(xajax.getFormValues('frmajax'));">
<br />{tr}Result{/tr}: <spam id="result" style="color: red;"></spam>
</form>
<br />
<h5>View the template source:</h5>
Template: <input type="text" name="template" id="template" value="tiki-xajax_example.tpl">
<input type="button" value="{tr}View{/tr}"  onclick="xajax_get_template(document.getElementById('template').value);">
</a></p>
<br />
<div id="template-source" style="border: 1px solid black; background-color: #DDDDDD; margin: 20px">
<center>Click View!</center>
</div>


<div>Have a look this php file (tiki-xajax_example.php) and have lot of fun -).</div>