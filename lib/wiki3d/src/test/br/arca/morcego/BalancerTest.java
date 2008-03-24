/*
 * Created on May 23, 2004
 *
 * To change the template for this generated file go to
 * Window - Preferences - Java - Code Generation - Code and Comments
 */
package br.arca.morcego;

import junit.framework.TestCase;
import br.arca.morcego.run.Balancer;
import br.arca.morcego.structure.Graph;
import br.arca.morcego.structure.GraphElementFactory;
import br.arca.morcego.structure.Node;

/**
 * @author alex
 *
 */
public class BalancerTest extends TestCase {

	Graph universe;
	Balancer balancer;
	Node particle1, particle2;
	float x1,y1,z1,x2,y2,z2;
	
	protected void setUp() throws Exception {
		Config.init();
		universe = new Graph();
		balancer = new Balancer(universe);
		Morcego.setUp();
		
	}

	public void testFreeParticle(){
		
		particle1 = GraphElementFactory.createNode("1", universe);
		x1 = particle1.getBody().getX();
		y1 = particle1.getBody().getY();
		z1 = particle1.getBody().getZ();
		
		balancer.runBalancingEngine();
		
		assertEquals("particle should be were we put it", x1, particle1.getBody().getX(), 0.0);
		assertEquals("particle should be were we put it", y1, particle1.getBody().getY(), 0.0);
		assertEquals("particle should be were we put it", z1, particle1.getBody().getZ(), 0.0);
	}
	
	public void testRepulsion(){
		
		particle1 = GraphElementFactory.createNode("1", universe);
		particle2 = GraphElementFactory.createNode("2", universe);
		
		float distanceBefore = particle1.getBody().getDistanceTo(particle2.getBody());
		
		balancer.runBalancingEngine();
		
		assertTrue("distance from particles is not what we expected", (particle1.getBody().getDistanceTo(particle2.getBody()) > distanceBefore));
	}
	
}
