<?php

error_reporting(E_ALL);

require_once 'PHPUnit/Framework.php';
require_once 'ShortestPathFinder.php';
 
class  ShortestPathFinderTest extends PHPUnit_Framework_TestCase
{

   ////////////////////////////////////////////////////////////////
   // Documentation tests
   //    These tests illustrate how to use this class.
   ////////////////////////////////////////////////////////////////

   protected function setUp()  {
      $distances_matrix[0][1] = 23;
      $distances_matrix[0][2] = 11;
      $distances_matrix[0][2] = 5;
      $INFINITY = 9999999;
      $this->pfinder = new ShortestPathFinder($distances_matrix, $INFINITY);   
   }
 
   //
   // Note: In remaining tests, assume that $this->pfinder has already
   //       been constructed in a similar way.
   //
   public function test_This_is_how_you_create_a_ShortestPathFinder() {
   
      // First, generate a distance matrix.
      $distances_matrix[0][1] = 23;
      $distances_matrix[0][2] = 11;
      $distances_matrix[0][2] = 5;
       
      // Need  to choose  a very large number to act as infinity.
      $INFINITY = 9999999;
      
      $path_finder = new ShortestPathFinder($distances_matrix, $INFINITY);
   }
   
   public function test_This_is_how_you_find_the_shortest_path() {
      //$this->pfinder->
   }
   
  

   ////////////////////////////////////////////////////////////////
   // Internal tests
   //    These tests check the internal workings of the class.
   ////////////////////////////////////////////////////////////////

   public function test_path_finder_example() {
 
      // I is the infinite distance.
      $INF = 1000;
 
      // Size of the matrix
      $matrixWidth = 20;
 
      // $points is an array in the following format: (router1,router2,distance-between-them)
      $points = array(
   	      array(0,1,4),
   	      array(0,2,$INF),
	      array(1,2,5),
 	      array(1,3,5),
	      array(2,3,5),
	      array(3,4,5),
	      array(4,5,5),
	      array(4,5,5),
	      array(2,10,30),
	      array(2,11,40),
	      array(5,19,20),
	      array(10,11,20),
	      array(12,13,20),
      );
 
      $ourMap = array();
 
 
      // Read in the points and push them into the map
 
      for ($i=0,$m=count($points); $i<$m; $i++) {
	      $x = $points[$i][0];
	      $y = $points[$i][1];
	      $c = $points[$i][2];
	      $ourMap[$x][$y] = $c;
	      $ourMap[$y][$x] = $c;
      }
 
      // ensure that the distance from a node to itself is always zero
      // Purists may want to edit this bit out.
 
      for ($i=0; $i < $matrixWidth; $i++) {
          for ($k=0; $k < $matrixWidth; $k++) {
              if ($i == $k) $ourMap[$i][$k] = 0;
          }
      }
 
      // initialize the algorithm class
      $path_finder = new ShortestPathFinder($ourMap, $INF);
 
      // $path_finder->findShortestPath(0,13); to find only path from field 0 to field 13...
      $path_finder->findShortestPath(0); 
 
      // Display the results
 
      echo '<pre>';
      echo "the map looks like:\n\n";
      echo $path_finder -> printMap($ourMap);
      echo "\n\nthe shortest paths from point 0:\n";
      echo $path_finder -> getResults();
      echo '</pre>';
}
   
   ////////////////////////////////////////////////////////////////
   // Helper methods
   ////////////////////////////////////////////////////////////////
   

}

?>