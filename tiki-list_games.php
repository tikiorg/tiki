<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-list_games.php,v 1.17 2004-02-28 22:01:07 techtonik Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
/**
 * TikiWiki Games
 *
 * Play flash games within your tikiwiki
 *
 * @internal
 * This Games system allows users to do following actions:
 * 1. List games
 * 2. Play games and count times game was played
 * 3. Upload games
 * 4. Edit them
 * 5. Delete
 *
 * @package Features
 * @subpackage Games
 * @tikiteam techtonik <techtonik@users.sourceforge.net>
 */

// Initialization
require_once('tiki-setup.php');

include_once('lib/games/gamelib.php');

if ($feature_games != 'y') {
    $smarty->assign('msg', tra("This feature is disabled").": feature_games");

    $smarty->display("error.tpl");
    die;
}

if ($tiki_p_play_games != 'y') {
    $smarty->assign('msg', tra("You dont have permission to use this feature"));

    $smarty->display("error.tpl");
    die;
}



// 1. List Games //
$games = array();
$h = opendir("games/thumbs");

while ($file = readdir($h)) {
    $game = array();

    // LeChuckdaPirate added "is_file" so folders don't be taken as games...
    if (is_file("games/thumbs/$file") && $file != '.' && $file != '..' && !ereg('\.txt$',$file)) {

        if (is_file("games/thumbs/$file" . '.txt')) {
            $data = file_get_contents("games/thumbs/$file" . '.txt');
            $desc = nl2br($data);
        } else {
            $desc = '';
        }

        $game["hits"] = $gamelib->get_game_hits($file);
        $game["desc"] = $desc;
        $game["game"] = $file;
        $games[$file] = $game;
    }
}

closedir($h);

function compare($ar1, $ar2) {
    return $ar2["hits"] - $ar1["hits"];
}

uasort($games, 'compare');

$smarty->assign_by_ref('games', $games);



// 2. Play games and count times game was played //
$smarty->assign('play', 'n');

if(isset($_REQUEST["game"])) {
 $gamelib->add_game_hit($_REQUEST["game"]);
 $game = str_replace( array('/','\\'), '_', $_REQUEST["game"]);
 $parts=explode('.',$game);
 $source='games/flash/'.$parts[0].'.'.$parts[1];
 if (is_file($source))
 {
   $smarty->assign('source',$source);
   $smarty->assign('play','y');
 }
}



// 3. Upload games //
$smarty->assign('uploadform', 'n');

if (isset($_REQUEST["uploadform"]) && $tiki_p_admin_games == 'y') {
    $smarty->assign('uploadform', 'y');
}

if (isset($_REQUEST["upload"])) {
    check_ticket('list-games');
    if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])
        && isset($_FILES['userfile2']) && is_uploaded_file($_FILES['userfile2']['tmp_name'])) {
        $name1 = $_FILES['userfile1']['name'];

        $name2 = $_FILES['userfile1']['name'];
        $parts = explode('.', $name2);
        $namec = implode('.', array(
            $parts[0],
            $parts[1]
        ));

        if ($namec != $name1) {
            $smarty->assign(
                'msg', tra("The thumbnail name must be"). " $name1.gif " . tra("or"). " $name1.jpg " . tra("or"). " $name1.png");

            $smarty->display("error.tpl");
            die;
        }

        @$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
        $name = $_FILES['userfile1']['name'];
        @$fw = fopen("games/flash/$name", "wb");

        if ($fp && $fw) {
            while (!feof($fp)) {
                $data = fread($fp, 8192);

                fwrite($fw, $data);
            }

            fclose($fp);
            fclose($fw);
        }

        @$fp = fopen($_FILES['userfile2']['tmp_name'], "rb");
        $name = $_FILES['userfile2']['name'];
        @$fw = fopen("games/thumbs/$name", "wb");

        if ($fp && $fw) {
            while (!feof($fp)) {
                $data = fread($fp, 8192);

                fwrite($fw, $data);
            }

            fclose($fp);
            fclose($fw);
        }

        @$fw = fopen("games/thumbs/$name" . ".txt", "wb");

        if ($fw) {
            fwrite($fw, $_REQUEST['description']);

            fclose($fw);
        }
    }
}



// 4. Edit them //
$smarty->assign('editgame', 'n');

if (isset($_REQUEST["edit"]) && $tiki_p_admin_games == 'y') {
    $smarty->assign('editgame', 'y');

    $smarty->assign('editable', $_REQUEST["edit"]);
    $file = $_REQUEST["edit"];
    $data = '';
    @$fp = fopen("games/thumbs/$file" . '.txt', "rb");

    if ($fp) {
        $data = fread($fp, filesize("games/thumbs/$file" . '.txt'));

        fclose($fp);
    }

    $smarty->assign('data', $data);
}

if (isset($_REQUEST["save"]) && $tiki_p_admin_games == 'y') {
    check_ticket('list-games');
    $file = $_REQUEST["editable"];

    @$fp = fopen("games/thumbs/$file" . '.txt', "wb");

    if ($fp) {
        fwrite($fp, $_REQUEST["description"]);

        fclose($fp);
    }
}



// 5. Delete //
if (isset($_REQUEST["remove"]) && $tiki_p_admin_games == 'y') {
    // security issue - remove slashes to avoid deleting in parent directory
    $game = str_replace( array('/','\\'), '_',$_REQUEST["remove"]);

    $parts = explode('.', $game);
    $parts = array_slice($parts,0,3);
    $source = 'games/flash/'.$parts[0].'.'.$parts[1];

    $game = 'games/thumbs/'.implode('.',$parts);
    $desc = 'games/thumbs/'.implode('.',$parts).'.txt';

    @unlink($source);
    @unlink($desc);
    @unlink($game);
}





$section = 'games';
include_once('tiki-section_options.php');
ask_ticket('list-games');

// Display the template
$smarty->assign('mid', 'tiki-list_games.tpl');
$smarty->display("tiki.tpl");

?>
