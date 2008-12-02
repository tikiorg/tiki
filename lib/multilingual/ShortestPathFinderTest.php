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
      $distances_matrix[0][1] = 11;
      $distances_matrix[0][2] = 23;
      $distances_matrix[1][2] = 5;
      $INFINITY = 9999999;
      $this->pfinder = new ShortestPathFinder($distances_matrix, $INFINITY);   
   }
 
   //
   // Note: In remaining tests, assume that $this->pfinder has already
   //       been constructed in a similar way.
   //
   public function test_This_is_how_you_create_a_ShortestPathFinder() {
   
      // First, generate a distance matrix.
      $distances_matrix[0][1] = 11;
      $distances_matrix[0][2] = 23;
      $distances_matrix[1][2] = 5;
      $distances_matrix[0][0] = 0;
      $distances_matrix[1][1] = 0;
      $distances_matrix[2][2] = 0;      
       
      // Need  to choose  a very large number to act as infinity.
      $INFINITY = 9999999;
      
      $path_finder = new ShortestPathFinder($distances_matrix, $INFINITY);
   }
   
   public function test_This_is_how_you_find_shortest_path_from_the_origin_to_another_node() {   
      $origin_node_num = 0;
      $destination_node_num = 2;
      $this->pfinder->computeShortestPathes($origin_node_num);
      
      $nodes_in_path = $this->pfinder->shortestPathTo($destination_node_num);
      $distance = $this->pfinder->shortestDistanceTo($destination_node_num);
   }    

   public function test_node_names_do_not_have_to_be_numbers() {
      echo "\n\n############ test_node_names_do_not_have_to_be_numbers\n\n";
      $distances_matrix['paris']['lyon'] = 11;
      $distances_matrix['paris']['marseilles'] = 23;
     $distances_matrix['lyon']['marseilles'] = 5;
      $INFINITY = 9999999;

      $this->pfinder = new ShortestPathFinder($distances_matrix, $INFINITY);   
      $this->pfinder->computeShortestPathes('paris');      
      $nodes_in_path = $this->pfinder->shortestPathTo('marseilles');
      $distance = $this->pfinder->shortestDistanceTo('marseilles');
      
   }

   ////////////////////////////////////////////////////////////////
   // Internal tests
   //    These tests check the internal workings of the class.
   ////////////////////////////////////////////////////////////////

   public function test__nodesInMatrix() {
   
      $distances_matrix = array();
      $distances_matrix['paris']['london'] = 300;
      $distances_matrix['paris']['rome'] = 600;
      $distances_matrix['london']['ottawa'] = 2000;      
      $exp_nodes = array('london', 'ottawa', 'paris', 'rome');
      
      $nodes_list = $this->pfinder->_nodesInMmatrix($distances_matrix);
      
      $this->assertEquals($exp_nodes, $nodes_list, "Bad list of nodes.");
   }

   public function test_path_finder_SMALL_example() {
      $start_node_num = 0;
      $this->pfinder->computeShortestPathes($start_node_num); 

      $this->assertShortestPathIs(1, array(0, 1), 11);     
      $this->assertShortestPathIs(2, array(0, 1, 2), 16);
   }
   
   public function test_reminder_get_rid_of_maxNodeIndex_method() {
      $this->fail("We want to get rid of node numbbers, and replace them with alphanumeric node ids. Oncce that's done, get rid of method maxNodeIndex()");
   }
      
   ////////////////////////////////////////////////////////////////
   // Helper methods
   ////////////////////////////////////////////////////////////////
   
   function assertShortestPathIs($destination, $exp_path, $exp_dist) {
      $got_dist = $distance = $this->pfinder->shortestDistanceTo($destination);
      $this->assertEquals($exp_dist, $got_dist, "Shortest distance to node $destination was wrong.");
      $got_path = $this->pfinder->shortestPathTo($destination);
      $this->assertEquals($exp_path, $got_path, "Shortest path to node $destination was wrong.");

   }

}

?>