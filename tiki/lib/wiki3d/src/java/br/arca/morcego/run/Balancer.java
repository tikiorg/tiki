/*
 * Morcego - 3D network browser Copyright (C) 2004 Luis Fagundes - Arca
 * <lfagundes@arca.ime.usp.br>
 * 
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library; if not, write to the Free Software Foundation,
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
package br.arca.morcego.run;

import java.util.*;

import br.arca.morcego.Config;
import br.arca.morcego.Morcego;
import br.arca.morcego.physics.PunctualBody;
import br.arca.morcego.physics.Vector3D;
import br.arca.morcego.structure.Graph;
import br.arca.morcego.structure.Link;
import br.arca.morcego.structure.Node;

/**
 * @author lfagundes
 * 
 * Balancer is responsible for calculating the forces over each node and
 * setting node speed to get the graph balanced.
 */
public class Balancer implements Runnable {

	private boolean balancing = true;
	private boolean balancingLock = false;
	private Graph graph;
	private int balancedCount;
	private boolean implementsHierarchy;

	public Balancer(Graph g) {
		graph = g;
		balancedCount = 0;
	}

	/*
	 * Stops the balancing when graph is stable
	 */
	public void stop() {
		if (!balancingLock) {
			balancing = false;
		}
	}

	/*
	 * Avoid stoping, for example when user is holding a node
	 */
	public void lockBalance() {
		balancingLock = true;
	}

	/*
	 *  
	 */
	public void unlockBalance() {
		balancingLock = false;
	}

	/*
	 * Restart balancing
	 */
	public void awake() {
		balancing = true;
		synchronized (this) {
			this.notify();
		}
	}

	/*
	 *  
	 */
	public void forceToLevel(Node node) {

		PunctualBody body = node.getBody();
		
		Vector3D orientation = graph.getOrientation();

		float x = body.getSpeed().getX();
		float y = body.getSpeed().getY();
		float z = body.getSpeed().getZ();
		float a = orientation.getX();
		float b = orientation.getY();
		float c = orientation.getZ();
		Vector3D newSpeed =
			new Vector3D(
				b * b * x + c * c * x - a * b * y - a * c * z,
				-b * a * x + a * a * y + c * c * y - b * c * z,
				-c * a * x - c * b * y + a * a * z + b * b * z);
		
		//node.addSpeed(newSpeed);

		Node center = graph.getCenterNode();

		Vector3D speed = new Vector3D(a, b, c);

		int relativeHierarchy = 0;
		try {
		relativeHierarchy =
			((Integer) center.getProperty("hierarchy")).intValue()
				- ((Integer) node.getProperty("hierarchy")).intValue();
		} catch (NullPointerException e) {
			// TODO change _implementsHierarchy
			// in fact, this is a server implementation error, maybe we shoud
			// throw an exception
		}

		speed.resize(100 * relativeHierarchy / speed.module());

		speed.add(new Vector3D(-body.getX(), -body.getY(), -body.getZ()));

		//node.addSpeed(speed);
	}

	/*
	 * (non-Javadoc) Calculates all forces acting over nodes and apply them
	 * until graph is stable
	 * 
	 * @see java.lang.Runnable#run()
	 */
	public void run() {

		Thread.currentThread().setPriority(Thread.MIN_PRIORITY);

		try {

			int cycleCount = 0;
			
			while (true) {

				waitUntilBalancingIsNeeded();

				runBalancingEngine();

				finishedBalancing();
				
				if (cycleCount % 20 == 0) {
					cycleCount = 0;
					
					checkSystemStability();
				}
				
				cycleCount++;

			}
		} catch (InterruptedException e) {
			System.out.println("Balancing interrupted");
		}

	}

	public void runBalancingEngine() {
		checkAllForces();
		balance();
		Morcego.getCamera().adjustPosition(graph);
	}

	private void finishedBalancing() throws InterruptedException {
		// balances may have stopped animation
		if (balancing) {
			balancedCount = 0;
		}

		Morcego.notifyRenderer();

		Thread.sleep(Config.getInteger(Config.balancingStepInterval));
	}

	private void balance() {
		Enumeration en = getNodes().elements();
		while (en.hasMoreElements()) {
			Node node = (Node) en.nextElement();
			node.move();
		}
	}

	/**
	 * Checks if system is stable, if so stops balancing engine
	 */
	private void checkSystemStability() {
		Enumeration en = getNodes().elements();
		boolean stable = true;
		while (stable && en.hasMoreElements()) {
			Node node = (Node) en.nextElement();
			if (node.getBody().getSpeed().module() > 1) {
				stable = false;
			}
		}
		if (stable) {
			stop();
		}
	}

	private void checkAllForces() {
		implementsHierarchy =
			Config.getBoolean(Config._implementsHierarchy);

		for (int j = 0; j < getNodes().size(); j++) {
			Node node1 = (Node) getNodes().elementAt(j);
			
			/*
			if (node1.centered()) {
				forceToCenter(node1);
			}
			*/			
			if (implementsHierarchy) {
				forceToLevel(node1);
			}

			for (int k = j + 1; k < getNodes().size(); k++) {
				Node node2 = (Node) getNodes().elementAt(k);
				Vector3D force = node1.getBody().repel(node2.getBody());
				node1.getBody().getInstantForce().add(force);
				node2.getBody().getInstantForce().add(force.opposite());
			}
			
		}
	
		for (int j = 0; j < getLinks().size(); j++) {
			Link link = (Link) getLinks().elementAt(j);
			Vector3D force = link.getSpring().strech();
			link.getNode1().getBody().getInstantForce().add(force);
			link.getNode2().getBody().getInstantForce().add(force.opposite());
		}
		
			
		for (int j = 0; j < getNodes().size(); j++) {
			((Node)getNodes().elementAt(j)).getBody().applyForce();
		}
	}

	private Vector getNodes() {
		return graph.getNodes();
	}
	
	
	private Vector getLinks() {
		return graph.getLinks();
	}

	private void waitUntilBalancingIsNeeded() throws InterruptedException {
		while (!balancing) {
			synchronized (this) {
				this.wait();
			}
		}
	}

	/**
	 * @param node
	 */
	public void notifyBalanced(Node node) {
		balancedCount++;
		if (balancedCount == graph.getNodes().size()) {
			stop();
		}
	}
}
