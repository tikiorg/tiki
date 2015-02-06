<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 * 
 */

class Multilingual_Aligner_ShortestPathFinderTest extends TikiTestCase
{

	////////////////////////////////////////////////////////////////
	// Documentation tests
	//    These tests illustrate how to use this class.
	////////////////////////////////////////////////////////////////

	protected function setUp()
	{
		$distances_matrix[0][1] = 11;
		$distances_matrix[0][2] = 23;
		$distances_matrix[1][2] = 5;
		$INFINITY = 9999999;
		$this->pfinder = new Multilingual_Aligner_ShortestPathFinder($distances_matrix, $INFINITY);   
	}

	//
	// Note: In remaining tests, assume that $this->pfinder has already
	//       been constructed in a similar way.
	//
	/**
	 * @group multilingual
	 */ 
	public function test_This_is_how_you_create_a_ShortestPathFinder()
	{

		// First, generate a distance matrix.
		$distances_matrix[0][1] = 11;
		$distances_matrix[0][2] = 23;
		$distances_matrix[1][2] = 5;
		$distances_matrix[0][0] = 0;
		$distances_matrix[1][1] = 0;
		$distances_matrix[2][2] = 0;      

		// Need  to choose  a very large number to act as infinity.
		$INFINITY = 9999999;

		$path_finder = new Multilingual_Aligner_ShortestPathFinder($distances_matrix, $INFINITY);
	}

	/**
	 * @group multilingual
	 */ 
	public function test_This_is_how_you_find_shortest_path_from_the_origin_to_another_node()
	{
		$origin_node_num = 0;
		$destination_node_num = 2;
		$this->pfinder->computeShortestPathes($origin_node_num);

		$nodes_in_path = $this->pfinder->shortestPathTo($destination_node_num);
		$distance = $this->pfinder->shortestDistanceTo($destination_node_num);
	}    

	/**
	 * @group multilingual
	 */ 
	public function test_node_names_do_not_have_to_be_numbers()
	{
		$distances_matrix['paris']['lyon'] = 11;
		$distances_matrix['paris']['marseilles'] = 23;
		$distances_matrix['lyon']['marseilles'] = 5;
		$INFINITY = 9999999;

		$this->pfinder = new Multilingual_Aligner_ShortestPathFinder($distances_matrix, $INFINITY);   
		$this->pfinder->computeShortestPathes('paris');      
		$nodes_in_path = $this->pfinder->shortestPathTo('marseilles');
		$distance = $this->pfinder->shortestDistanceTo('marseilles');

	}

	////////////////////////////////////////////////////////////////
	// Internal tests
	//    These tests check the internal workings of the class.
	////////////////////////////////////////////////////////////////

	/**
	 * @group multilingual
	 */ 
	public function test__nodesInMatrix()
	{

		$distances_matrix = array();
		$distances_matrix['paris']['london'] = 300;
		$distances_matrix['paris']['rome'] = 600;
		$distances_matrix['london']['ottawa'] = 2000;      
		$exp_nodes = array('london', 'ottawa', 'paris', 'rome');

		$nodes_list = $this->pfinder->_nodesInMmatrix($distances_matrix);

		$this->assertEquals($exp_nodes, $nodes_list, "Bad list of nodes.");
	}

	/**
	 * @group multilingual
	 */ 
	public function test_path_finder_SMALL_example()
	{
		$start_node_num = 0;
		$this->pfinder->computeShortestPathes($start_node_num); 

		$this->assertShortestPathIs(1, array(0, 1), 11, "");     
		$this->assertShortestPathIs(2, array(0, 1, 2), 16, "");
	}

	/**
	 * @group multilingual
	 */ 
	public function test_path_finder_LARGER_example()
	{
		$start_node_num = 'ottawa';
		$cost_matrix['ottawa']['montreal'] = 50;
		$cost_matrix['ottawa']['chicago'] = 100;
		$cost_matrix['ottawa']['detroit'] = 150;
		$cost_matrix['montreal']['detroit'] = 90;
		$cost_matrix['montreal']['vancouver'] = 300;

		$cost_matrix['detroit']['vancouver'] = 110;
		$cost_matrix['chicago']['vancouver'] = 170;
		$cost_matrix['ottawa']['toronto'] = 90;
		$cost_matrix['toronto']['vancouver'] = 280;
		$INFINITY = 9999999;

		$this->pfinder = new Multilingual_Aligner_ShortestPathFinder($cost_matrix, $INFINITY);      
		$this->pfinder->computeShortestPathes('ottawa'); 

		$this->assertShortestPathIs('montreal', array("ottawa", "montreal"), 50, "Bad path to montreal");     
		$this->assertShortestPathIs('toronto', array("ottawa", "toronto"), 90, "Bad path to toronto");
		$this->assertShortestPathIs('chicago', array("ottawa", "chicago"), 100, "Bad path to chicago");   
		$this->assertShortestPathIs('detroit', array("ottawa", "montreal", "detroit"), 140, "Bad path to detroit");
		$this->assertShortestPathIs('vancouver', array("ottawa", "montreal", "detroit", "vancouver"), 250, "Bad path to vancouver");
	}


	////////////////////////////////////////////////////////////////
	// Helper methods
	////////////////////////////////////////////////////////////////

	function assertShortestPathIs($destination, $exp_path, $exp_dist, $message)
	{
		$got_dist = $distance = $this->pfinder->shortestDistanceTo($destination);
		$this->assertEquals($exp_dist, $got_dist, "Shortest distance to node $destination was wrong.");
		$got_path = $this->pfinder->shortestPathTo($destination);
		$this->assertEquals($exp_path, $got_path, "$message\nShortest path to node $destination was wrong.");
	}

}
