<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'Multilingual/Aligner/ShortestPathFinder.php';

/**
 * This class implements the Dijkstra algorithm for finding the shortest path
 * through a graph.
 *
 * For details on this algorithm, see:
 *   http://en.wikipedia.org/wiki/Dijkstra%27s_algorithm 
 *
 * For details on how to use the class, see the unit test file 
 * ShortestPathFinderTest.php.
 */
 
 
class  Multilingual_Aligner_ShortestPathFinder
{
	var $visited = array();
	var $distance = array();
	var $previousNode = array();
	var $startnode =null;
	var $map = array();
	var $infiniteDistance = 0;
	var $numberOfNodes = 0;
	var $bestPath = 0;
	var $matrixWidth = 0;
	var $shortestPathes = array();
    var $nodes = array();
     
	function __construct(&$ourMap, $infiniteDistance) {
		$this -> infiniteDistance = $infiniteDistance;
		$this -> map = &$ourMap;
		$this -> bestPath = 0;

	    $this->nodes = $this->_nodesInMmatrix($ourMap);
		$this -> numberOfNodes = count($this->nodes);
	}
	
	function _nodesInMmatrix($distanceMatrix) {
	   $nodes = array();
       foreach (array_keys($distanceMatrix) as $originNode) {
          if (!in_array($originNode, $nodes)) {
             array_push($nodes, $originNode);
          }
          $distancesFromThisNode = $distanceMatrix[$originNode];
          foreach (array_keys($distancesFromThisNode) as $destinationNode) {
             if (!in_array($destinationNode, $nodes)) {
                array_push($nodes, $destinationNode);
             }
          }
       }
       sort($nodes);

//       echo "-- _nodesInMmatrix: outputting nodes:\n"; var_dump($nodes);

       return $nodes;
	}	
 
	function computeShortestPathes($start,$to = null) {
		$this -> startnode = $start;
		foreach ($this->nodes as $currNode) {
			if ($currNode == $this -> startnode) {
				$this -> visited[$currNode] = true;
				$this -> distance[$currNode] = 0;
			} else {
				$this -> visited[$currNode] = false;
				$this -> distance[$currNode] = isset($this -> map[$this -> startnode][$currNode]) 
					? $this -> map[$this -> startnode][$currNode] 
					: $this -> infiniteDistance;
			}
			$this -> previousNode[$currNode] = $this -> startnode;
		}
 
		$maxTries = $this -> numberOfNodes;
		
//		echo "-- computeShortestPathes: \$maxTries=$maxTries\n";
		
		$tries = 0;
		while (in_array(false, $this -> visited, true) && $tries <= $maxTries) {
//		    echo "-- computeShortestPathes: \$tries=$tries\n\$this->distance=\n";var_dump($this->distance);echo "\n\$this->previousNode=\n";var_dump($this->previousNode);print "\n";			
			$this -> bestPath = $this->findBestPath($this->distance, array_keys($this -> visited, false, true));
			if($to !== null && $this -> bestPath === $to) {
				break;
			}
			$this -> updateDistanceAndPrevious($this -> bestPath);			
			$this -> visited[$this -> bestPath] = true;
			$tries++;
		}
		$this -> shortestPathes = $this->getShortestPathesInfo();

		return $this -> shortestPathes;
	}
 
	function findBestPath($ourDistance, $ourNodesLeft) {
//	    echo "-- findBestPath: \ourDistance=\n";var_dump($ourDistance);echo "\n\$ourNodesLeft=\n";var_dump($ourNodesLeft);
		$bestPath = $this -> infiniteDistance;
		$bestNode = NULL;
		foreach ($ourNodesLeft as $currNode) {
//		    echo "-- findBestPath: processing node: $currNode, \$ourDistance[\$currNode]=$ourDistance[$currNode], \$bestPath=$bestPath\n";
			if($ourDistance[$currNode] < $bestPath) {
				$bestPath = $ourDistance[$currNode];
				$bestNode = $currNode;
			}
		}
//		echo "-- findBestPath: upon exit, \$bestNode=$bestNode, \ourDistance=\n";var_dump($ourDistance);echo "\n\$ourNodesLeft=\n";var_dump($ourNodesLeft);
		return $bestNode;
	}
 
	function updateDistanceAndPrevious($obp) {	
//	    echo "-- updateDistanceAndPrevious: \$obp=$obp\n";
	    foreach ($this->nodes as $currNode) {
//	        echo "-- updateDistanceAndPrevious: processing \$currNode=$currNode\n"; 	
			if( 	(isset($this->map[$obp][$currNode])) 
			    &&	(!($this->map[$obp][$currNode] == $this->infiniteDistance) || ($this->map[$obp][$currNode] == 0 ))	
				&&	(($this->distance[$obp] + $this->map[$obp][$currNode]) < $this -> distance[$currNode])
			) 	
			{
//			        echo "-- updateDistanceAndPrevious: found shorter rout to \$currNode, through \$obp.\n";
					$this -> distance[$currNode] = $this -> distance[$obp] + $this -> map[$obp][$currNode];
					$this -> previousNode[$currNode] = $obp;
			}
		}

//        echo "-- updateDistanceAndPrevious: upon exit, \$this->distance=\n";var_dump($this->distance);echo "\n\$this->previousNode=\n";var_dump($this->previousNode); echo"\n"; 

	}
	
	function shortestPathTo($destination_node_num) {
	   return $this->shortestPathes[$destination_node_num];
	}
 
    function shortestDistanceTo($destination_node_num) {
       return $this->distance[$destination_node_num];
    }
 
	function printMap(&$map) {
		$placeholder = ' %' . strlen($this -> infiniteDistance) .'d';
		$foo = '';
		for($i=0,$im=count($map);$i<$im;$i++) {
			for ($k=0,$m=$im;$k<$m;$k++) {
				$foo.= sprintf($placeholder, isset($map[$i][$k]) ? $map[$i][$k] : $this -> infiniteDistance);
			}
			$foo.= "\n";
		}
		return $foo;
	}
	
	function getShortestPathesInfo($to = null) {
		$ourShortestPath = array();
		foreach ($this->nodes as $aNode) {
			if($to !== null && $to !== $aNode) {
				continue;
			}
			$ourShortestPath[$aNode] = array();
			$endNode = null;
			$currNode = $aNode;
			$ourShortestPath[$aNode][] = $aNode;
			while ($endNode === null || $endNode != $this -> startnode) {
				$ourShortestPath[$aNode][] = $this -> previousNode[$currNode];
				$endNode = $this -> previousNode[$currNode];
				$currNode = $this -> previousNode[$currNode];
			}
			$ourShortestPath[$aNode] = array_reverse($ourShortestPath[$aNode]);
			if ($to === null || $to === $aNode) {
				if ($to === $aNode) {
					break;
				}
			}
		}
		return $ourShortestPath;
	}
 
	function getResults($to = null) {
		$ourShortestPath = array();
		$foo = '';
		foreach ($this->nodes as $aNode) {
			if($to !== null && $to !== $aNode) {
				continue;
			}
			$ourShortestPath[$aNode] = array();
			$endNode = null;
			$currNode = $aNode;
			$ourShortestPath[$aNode][] = $aNode;
			while ($endNode === null || $endNode != $this -> startnode) {
				$ourShortestPath[$aNode][] = $this -> previousNode[$currNode];
				$endNode = $this -> previousNode[$currNode];
				$currNode = $this -> previousNode[$currNode];
			}
			$ourShortestPath[$aNode] = array_reverse($ourShortestPath[$aNode]);
			if ($to === null || $to === $aNode) {
			if($this -> distance[$aNode] >= $this -> infiniteDistance) {
				$foo .= sprintf("no route from %d to %d. \n",$this -> startnode,$aNode);
			} else {
				$foo .= sprintf('%d => %d = %d [%d]: (%s).'."\n" ,
						$this -> startnode,$aNode,$this -> distance[$aNode],
						count($ourShortestPath[$aNode]),
						implode('-',$ourShortestPath[$aNode]));
			}
			$foo .= str_repeat('-',20) . "\n";
				if ($to === $aNode) {
					break;
				}
			}
		}
		return $foo;
	}
} // end class 
