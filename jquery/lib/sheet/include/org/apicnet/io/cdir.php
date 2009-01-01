<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

//
// $Archive: /iPage/V1.1/include/dir.php $
// $Date: 2005-05-18 11:01:38 $
// $Revision: 1.3 $
//
// $History: dir.php $
// 
// 
// *****************  Version 5  *****************
// User: @PICNet      Date: 18.03.04   Time: 14:12
// User: Hannesd      Date: 28.11.00   Time: 14:12
// Updated in $/iPage/V1.1/include
//

if ( !defined( "INCLUCDED_DIR" ) ) {
	define( "INCLUCDED_DIR", TRUE  );

/**
 * CDir
* Class for reading a directory structure.
* aFiles contains multiple aFile entries
* aFile:   Path        => relative path eg. ../xx/yy/
*          File        => filename eg. filename (without extension)
*          Extension   => ext
*          IsDirectory => true/false
*          FullName    => Path . File . "." . Extension
*          FileName    => File . "." . Extension

* Notes
* Filenames with multiple Extensions: only the last extensions is saved as extensions
* eg: aaa.bbb.ccc results in File=aaa.bbb and Extension=ccc
* Filenames are stored in the same case as the are stored in the filesystem
* sFilter is only applied to files.
 * @package or.apicnet.io
 * @version $Id: cdir.php,v 1.3 2005-05-18 11:01:38 mose Exp $
 * @access public
 **/
Class CDir extends ErrorManager {
    var $aFiles;

    Function CDir(){
        $this->Init();
		parent::ErrorManager();
    }

    Function Init(){
        unset( $this->aFiles );
        $this->aFiles = array();
    }
	
    /**
     * CDir::Read()
     * 
     * @param $sPath path eg. "../xx/yy/" (note the last "/")
     * @param string $sInclude regular expression for filtering path- and filenames
     * @param boolean $fRecursive true/false: go down the whole structure
     * @param integer $levelRecursive number of whole structure down
     * @param boolean $fFiles result set will contain entries which are files
     * @param boolean $fDirectories result set will contain entries which are directories
     * @param string $sRoot Root-Path. Will be appended to the entries.
     * @param string $sExclude  regular expression for filtering path- and filenames
     * @return 
     **/
    Function Read( $sPath, $sInclude = "", $fRecursive = false, $levelRecursive=2, $fFiles = true, $fDirectories = true, $sRoot = "", $sExclude = "" ){
        $oHandle = opendir( $sPath );
        while ( $sFilename = readdir( $oHandle ) ){
            $fInsert = true;

            if ( $sFilename == "." || $sFilename == ".." )  continue;

            $fIsDirectory = is_dir( $sPath . $sFilename );

            if ( !$fFiles && !$fIsDirectory )      $fInsert = false;
            if ( !$fDirectories && $fIsDirectory ) $fInsert = false;

            if ( $fInsert && !$fIsDirectory && ( !empty( $sInclude ) || !empty( $sExclude ) ) ) {
                $sFullname = $sRoot;
                $sFullname .= $sFilename;

                if ( !empty( $sInclude ) )
                    if ( !ereg( $sInclude, $sFullname ) )
                        $fInsert = false;

                if ( !empty( $sExclude ) )
                    if ( ereg( $sExclude, $sFullname ) )
                        $fInsert = false;
            }

            if ( $fInsert ){
                $i = strrpos( $sFilename, "." ) + 1;
                if ( substr( $sFilename, $i - 1, 1 ) == "." ) {
                    $sFile = substr( $sFilename, 0, $i - 1 );
                    $sExtension = substr( $sFilename, $i );
                } else {
                    $sFile = $sFilename;
                    $sExtension = "";
                }

                $aFile = array(
                        "Path" => $sRoot,
                        "File" => $sFile,
                        "Extension" => $sExtension,
                        "IsDirectory" => $fIsDirectory
                    );

                //Insert current file into aFiles array
                $this->aFiles[] = $aFile;
            }

            //Recursion?
            if ( $fRecursive && $fIsDirectory && ($levelRecursive > 0 || $levelRecursive = -1))
                $this->Read( $sPath . $sFilename . "/", $sInclude, $fRecursive, $levelRecursive - 1, $fFiles, $fDirectories, $sRoot . $sFilename . "/", $sExclude );
        }

        closedir( $oHandle );
    }

    /**
     * CDir::Output()
     * 
     * @return 
     **/
    Function Output(){
        reset( $this->aFiles );
        while( list( $sKey, $aFile ) = each( $this->aFiles ) )
            $this->OutputFile( $aFile );
    }

    /**
     * CDir::OutputFile()
     * 
     * @param $aFile
     * @return 
     **/
    Function OutputFile( $aFile ){
        printf( "Path: %s<br>\n", $this->GetPath( $aFile ) );
        printf( "File: %s<br>\n", $this->GetFile( $aFile ) );
        printf( "Extension: %s<br>\n", $this->GetExtension( $aFile ) );
        printf( "IsDirectory: %s<br>\n", $this->GetIsDirectory( $aFile ) ? "true" : "false" );
        printf( "IsFile: %s<br>\n", $this->GetIsFile( $aFile ) ? "true" : "false" );
        printf( "FullName: %s<br>\n", $this->FullName( $aFile ) );
        printf( "FileName: %s<br>\n", $this->FileName( $aFile ) );
        printf( "DirectoryName: %s<br>\n", $this->DirectoryName( $aFile ) );
        echo "<hr>\n";
    }

    /**
     * CDir::GetPath()
     * 
     * @param $aFile
     * @return 
     **/
    Function GetPath( $aFile ){
        return( $aFile[ "Path" ] );
    }

    /**
     * CDir::GetFile()
     * 
     * @param $aFile
     * @return 
     **/
    Function GetFile( $aFile ){
        return( $aFile[ "File" ] );
    }

    /**
     * CDir::GetExtension()
     * 
     * @param $aFile
     * @return 
     **/
    Function GetExtension( $aFile ){
        return( $aFile[ "Extension" ] );
    }

    /**
     * CDir::GetIsDirectory()
     * 
     * @param $aFile
     * @return 
     **/
    Function GetIsDirectory( $aFile ){
        return( $aFile[ "IsDirectory" ] );
    }

    /**
     * CDir::GetIsFile()
     * 
     * @param $aFile
     * @return 
     **/
    Function GetIsFile( $aFile ){
        return( !$this->GetIsDirectory( $aFile ) );
    }

    /**
     * CDir::FullName()
     * 
     * @param $aFile
     * @return 
     **/
    Function FullName( $aFile ){
        return( $this->GetPath( $aFile ) . $this->FileName( $aFile ) );
    }

    /**
     * CDir::FileName()
     * 
     * @param $aFile
     * @return 
     **/
    Function FileName( $aFile ){
        $sBuffer = $this->DirectoryName( $aFile );
        if ( $this->GetIsDirectory( $aFile ) )
            $sBuffer .= "/";

        return( $sBuffer );
    }
	
    /**
     * CDir::DirectoryName() DirectoryName returns the same as FileName, but without a ending "/" for Directories.
     * 
     * @param $aFile
     * @return 
     **/
    Function DirectoryName( $aFile ){
        $sBuffer = $this->GetExtension( $aFile );
        if ( !empty( $sBuffer ) )
            $sBuffer = "." . $sBuffer;
        $sBuffer = $this->GetFile( $aFile ) . $sBuffer;

        return( $sBuffer );
    }
	
	// Based on the other notes given before.
	// Sorts an array (you know the kind) by key
	// and by the comparison operator you prefer.
	
	// Note that instead of most important criteron first, it's
	// least important criterion first.
	
	// The default sort order is ascending, and the default sort
	// type is strnatcmp.
	
	// function multisort($array[, $key, $order, $type]...)
	function multisort($array){
	   for($i = 1; $i < func_num_args(); $i += 3){
	       $key = func_get_arg($i);
	       
	       $order = true;
	       if($i + 1 < func_num_args())
	           $order = func_get_arg($i + 1);
	       
	       $type = 0;
	       if($i + 2 < func_num_args())
	           $type = func_get_arg($i + 2);
	
	       switch($type){
	           case 1: // Case insensitive natural.
	               $t = 'strcasenatcmp($a[' . $key . '], $b[' . $key . '])';
	               break;
	           case 2: // Numeric.
	               $t = '$a[' . $key . '] - $b[' . $key . ']';
	               break;
	           case 3: // Case sensitive string.
	               $t = 'strcmp($a[' . $key . '], $b[' . $key . '])';
	               break;
	           case 4: // Case insensitive string.
	               $t = 'strcasecmp($a[' . $key . '], $b[' . $key . '])';
	               break;
	           default: // Case sensitive natural.
	               $t = 'strnatcmp($a[' . $key . '], $b[' . $key . '])';
	               break;
	       }
	
	       uasort($array, create_function('$a, $b', 'return ' . ($order ? '' : '-') . '(' . $t . ');'));
	   }
	
	   return $array;
	}
	
	//function multisort([$key, $order, $type]...)
	function sort(){
		$error  = FALSE;
		$params = "";
		$plen   = func_num_args();
		$result = NULL;
		
		if (func_num_args() > 2) {
			
			for($i = 0; $i < func_num_args(); $i += 3){
				$Key = func_get_arg($i);
				$order = func_get_arg($i + 1);
				if ($order) $order = 1;
				else $order = 0;
				$type = func_get_arg($i + 2);
				$params .= $Key.", ".$order.", ".$type;
				if ($i + 3 < func_num_args()) $params .= ', ';
			}
		} else {
			$error = TRUE;
		}
		
		if (!$error) {
			//echo("params : multisort(\$this->aFiles, ".$params.");<br>");
			eval("\$result = \$this->multisort(\$this->aFiles, ".$params.");");
		} else {
			$this -> ErrorTracker(4, "Error dans le trie du tableau", 'sort', __FILE__, __LINE__);
		}
		
		return $result;
	}
}

}
// if ( !INCLUCDED_DIR )

?>
