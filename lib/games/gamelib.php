<?php
class GameLib extends TikiLib {

  function GameLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
  function add_game_hit($game)
  {
    $cant = $this->getOne("select count(*) from tiki_games where gameName='$game'");
    if($cant) {
      $query = "update tiki_games set hits = hits+1 where gameName='$game'";
    } else {
      $query = "insert into tiki_games(gameName,hits,points,votes) values('$game',1,0,0)";
    }
    $result = $this->query($query);
  }
  
  function get_game_hits($game)
  {
    $cant = $this->getOne("select count(*) from tiki_games where gameName='$game'");
    if($cant) {
      $hits = $this->getOne("select hits from tiki_games where gameName='$game'");
    } else {
      $hits =0;
    }
    return $hits;
  }

  
}

$gamelib= new GameLib($dbTiki);
?>