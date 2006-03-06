{include file="tiki-ajax_header.tpl"}

<center><b>
  This page ilustrates ajax funcionality in tiki.
  It was made for developers to understand how tiki ajax framework works,
  and will be removed somewhen.
</b></center>

<br><br>

<form name="f">

<div>
  Ajax framework uses three main datatypes.
  These are the common result of functions in tiki:<br>
  <ul>
    <li>scalar: non-array values, like integers or strings</li>
    <li>item: an associative array, tipically a row in a database table</li>
    <li>list: an array of associative arrays, tipically many rows from a db table
        <br>
	<small>
	   note: list type can be returned either as a (data => $res, cant => $cant)
	   array or just the data, the client-side result will be the same
	 </small>
    </li>
  </ul>

  This page shows how client-side framework works.
  Check ajax/test_*.php files to see backend.

</div><br><hr><br>

<div>
  This first example shows how the "scalar" datatype is handled.
  Type a phrase and choose a language, and you'll see the tiki
  translation for that phrase (copy it from somewhere around this page).
  <br>

  <script language="JavaScript">
  {literal}

    function handle_test_scalar(result) {
				document.getElementById('scalarbox').innerHTML=': '+result;
    }

  {/literal}
  </script>

  <input name="phrase" size="30" onblur="javascript:load('test_scalar', document.f.phrase.value, document.f.lang.value)">
  <input name="lang" size="5" value="pt-br" onblur="javascript:load('test_scalar', document.f.phrase.value, document.f.lang.value)">
  <a href="javascript:load('test_scalar', document.f.phrase.value, document.f.lang.value)">
    translate
  </a>

<span id="scalarbox">: </span></div><br><hr><br>

<div>
  The second example ilustrates the "item" type.
  By clicking below, this script will fetch your 
  user information and display to you.

  <script language="JavaScript">
  {literal}

    function handle_test_item(result) {
        alert('userId: ' + result['userId'] + 
	      ', login: ' + result['login']);
    }

  {/literal}
  </script>

  <br><br>
  <center>
    <a href="javascript:load('test_item')">
      who am i?
    </a>
  </center>

</div><br><hr><br>

<div>
  Click below to see the "list" type.

  <script language="JavaScript">
  {literal}

    function handle_test_list(result) {
        alert("fetched " + result.length + 
	      " system permissions, first one is " + result[0]['permName'] + 
	      " of type " + result[0]['type'] + 
	      " and last is " + result[result.length-1]['permName']
	      );
    }

  {/literal}
  </script>


  <br><br>
  <center>
    <a href="javascript:load('test_list',0,10)">
      list system permissions
    </a>
  </center>

</div><br><hr><br>

<div>
  Tiki ajax framework also supports client side translation. For that, you can use the
  js function tra(str). To do so, the function first checks a local cache, and if there's
  no translation, it returns the untranslated string inside a <span> tag, while it asks
  the server for a translation. When it gets the server answer, the untranslated string is
  substituted. If you use a non-english language, next time you load this page note below
  that the string changes after a small time.
  <br><br>

  <center>
    <script>document.write(tra('Home'));</script>
  </center>
</div><br><hr><br>

<div>
  And now, the big magic. The earlier examples show how to transfer data structures
  that needs a javascript callback function for each one. The following example loads
  html code in a very simple way:<br><br>

  Note: there's a bug that limits content size :-(

  <div id="test-content-div"
       style="border: 1px solid black; background-color: #DDDDDD; margin: 20px;">
    <center>

      I'm a div with id "test-content-div".<br>

      <a href="javascript:loadContent('test-content-div', 'test_content')">
        Click here to see the sourcecode of this template
      </a>

    </center>

  </div>

</div><br><hr><br>

</form>
