<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-list_games.php,v 1.28 2007-10-12 07:55:28 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
/**
 * Tikiwiki Games
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
 * GameID used in all requests is a name of thumbnail file
 *
 * @package Features
 * @subpackage Games
 * @tikiteam techtonik <techtonik@users.sourceforge.net>
 */

// Initialization
require_once('tiki-setup.php');

include_once('lib/games/gamelib.php');

if ($prefs['feature_games'] != 'y') {
    $smarty->assign('msg', tra("This feature is disabled").": feature_games");

    $smarty->display("error.tpl");
    die;
}

if ($tiki_p_play_games != 'y') {
    $smarty->assign('msg', tra("You do not have permission to use this feature"));

    $smarty->display("error.tpl");
    die;
}



// 1. List Games //
$games = array();
$h = opendir("games/thumbs");

while ($file = readdir($h)) {
    $game = array();

    // LeChuckdaPirate added "is_file" so folders don't be taken as games...
    if (is_file("games/thumbs/$file") && $file != '.' && $file != '..' && !ereg('\.txt$',$file)
        && $file != 'index.php' && $file != 'README' ) {

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

// preventive measures: if user refreshes page with game - game hits must stay the same
if(!isset($_REQUEST["game"]) && isset($_SESSION["currentgame"])) unset($_SESSION["currentgame"]);

if(isset($_REQUEST["game"])) {
 $game = basename( $_REQUEST["game"] );
 if (!isset($_SESSION["currentgame"]) || !in_array($game,$_SESSION["currentgame"]) ) {
     $gamelib->add_game_hit($game);
     $_SESSION["currentgame"][] = $game;
 }
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

if (isset($_POST["upload"]) && $tiki_p_admin_games == 'y') {
    check_ticket('list-games');
    if (isset($_FILES['flashfile']) && is_uploaded_file($_FILES['flashfile']['tmp_name'])
        && isset($_FILES['imagefile']) && is_uploaded_file($_FILES['imagefile']['tmp_name'])) {
        $name1 = basename ($_FILES['flashfile']['name']);

        $name2 = basename ($_FILES['imagefile']['name']);
        $parts = explode('.', $name2);
        $namec = $parts[0].'.'.$parts[1];

        if (!ereg("\.(swf|dcr)$", $name1)) {
            $smarty->assign(
                'msg', tra("The game file must have .swf or .dcr extension"));

            $smarty->display("error.tpl");
            die;
        }

        if (!$namec || $namec != $name1 || !isset($parts[2]) || !in_array($parts[2], array('gif','png','jpg'))) {
            $smarty->assign(
                'msg', tra("The thumbnail name must be"). " $name1.gif, $name1.jpg " . tra("or"). " $name1.png");

            $smarty->display("error.tpl");
            die;
        }

        @$fp = fopen($_FILES['flashfile']['tmp_name'], "rb");
        $name = basename($_FILES['flashfile']['name']);
        @$fw = fopen("games/flash/$name", "wb");

        if ($fp && $fw) {
            while (!feof($fp)) {
                $data = fread($fp, 8192);

                fwrite($fw, $data);
            }

            fclose($fp);
            fclose($fw);
            unlink($_FILES['flashfile']['tmp_name']);
        }

        @$fp = fopen($_FILES['imagefile']['tmp_name'], "rb");
        $name = basename($_FILES['imagefile']['name']);
        @$fw = fopen("games/thumbs/$name", "wb");

        if ($fp && $fw) {
            while (!feof($fp)) {
                $data = fread($fp, 8192);

                fwrite($fw, $data);
            }

            fclose($fp);
            fclose($fw);
            unlink($_FILES['imagefile']['tmp_name']);
        }

        @$fw = fopen("games/thumbs/$name" . ".txt", "wb");

        if ($fw) {
            fwrite($fw, nl2br($_POST['description']));
            fclose($fw);
        }

        $game["hits"] = $gamelib->get_game_hits($name2);
        $game["desc"] = $_POST['description'];
        $game["game"] = $name2;
        $games[$name2] = $game;

    // if all needed files are not uploaded
    } else {

        $smarty->assign(
            'msg', tra("Please supply both files"));

        $smarty->display("error.tpl");
        die;
    }

}


// 4. Edit them //
$smarty->assign('editgame', 'n');

if (isset($_REQUEST["edit"]) && $tiki_p_admin_games == 'y') {
    $file = basename( $_REQUEST["edit"] );

    if (array_key_exists($file, $games)) {

        $smarty->assign('editgame', 'y');
        $smarty->assign('editable', $file);

        $descfile = "games/thumbs/$file". '.txt';
        $data = '';

        if (is_file($descfile))
            $data = file_get_contents("games/thumbs/$file" . '.txt');

        $smarty->assign('data', $data);
    }
}

if (isset($_POST["save"]) && $tiki_p_admin_games == 'y') {
    check_ticket('list-games');
    $file = basename( $_POST["editable"] );

    if (array_key_exists($file, $games)) {
    @$fp = fopen("games/thumbs/$file" . '.txt', "wb");

    if ($fp) {
        fwrite($fp, $_POST["description"]);

        fclose($fp);
    }
    $games[$file]['desc'] = nl2br($_POST["description"]);
    }
}

// 5. Delete //
if (isset($_REQUEST["remove"]) && $tiki_p_admin_games == 'y') {
	// security issue - remove slashes to avoid traversing in parent directory
	$game = basename( $_REQUEST["remove"] );
	if (array_key_exists($game, $games)) {
		$area = 'delgame';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$parts = explode('.', $game);
    	$parts = array_slice($parts,0,3);
			$source = 'games/flash/'.$parts[0].'.'.$parts[1];
			$gamefile = 'games/thumbs/'.implode('.',$parts);
			$desc = 'games/thumbs/'.implode('.',$parts).'.txt';
			unlink($source);
			unlink($desc);
			unlink($gamefile);
			unset($games[$game]);
		} else {
			key_get($area);
		}
	}
}

$section = 'games';
include_once('tiki-section_options.php');
ask_ticket('list-games');

// Display the template
$smarty->assign('mid', 'tiki-list_games.tpl');
$smarty->display("tiki.tpl");

?>
