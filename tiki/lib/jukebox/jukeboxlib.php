<?php

/* First commit on CVS by Damian aka damosoft */
/* tiki jukebox */

class JukeboxLib extends TikiLib {
	
	function replace_album($title, $description, $user, $public, $albumId, $maxTracks, $genreId) {

		$now = date("U");

		if ($albumId) {
			$query = "update `tiki_jukebox-albums` set `genreName`=?, `genreDescription`=?, `user`=?";
		}
	}

/* Create or Update a Genre */
	function replace_genres($title, $description, $genreId) {

		if (!$genreId) {
			$query = "insert into `tiki_jukebox_genres` (`genreName`, `genreDescription`) values (?, ?)";
			$result = $this->query($query, array($title, $description));
		} else {
			$query = "update `tiki_jukebox_genres` set `genreName`=?, `genreDescription`=? where `genreId`=?";
			$result = $this->query($query, array($title, $description, $genreId));
		}
		return true;
	}

/* Get a single Genre */
	function get_genre($id) {
		
		$query = "select * from `tiki_jukebox_genres` where `genreId`=?";

                $result = $this->query($query,array($id));
                $res = $result->fetchRow();

                return $res;
	}

/* List genres */
        function list_genres($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '') {

                if ($find) {
                        $findesc = '%' . $find . '%';

                        $mid = " where ((`genreName` like ?) Or (`genreDescription` like ?)) ";
                        $bindvars = array($findesc, $findesc);
		} else {
			$mid = "";
			$bindvars = array();
		}

                $query = "select * from `tiki_jukebox_genres` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_jukebox_genres` $mid";
                $result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;

		return $retval;
	}

/* Remove genre */
/* this will set the album and tracks who have this genreId to NULL as well */
	function remove_genre($id) {

		// Search for tracks
		$query = "update `tiki_jukebox_tracks` set `genreId` = ? where `genreId` = ?";
		$result = $this->query($query, array((int) $id, null));

		// Search for albums
                $query = "update `tiki_jukebox_albums` set `genreId` = ? where `genreId` = ?";
                $result = $this->query($query, array((int) $id, null));

		// Now zap the genre
                $query = "delete from `tiki_jukebox_genres` where `genreId`=?";
                $result = $this->query($query,array((int) $id));

                return true;
        }

}

$jukeboxlib = new JukeboxLib($dbTiki);

?>
