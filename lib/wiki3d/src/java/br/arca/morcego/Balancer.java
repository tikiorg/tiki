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
package br.arca.morcego;

import java.util.*;

/**
 * @author lfagundes
 * 
 * Balancer is responsible for calculating the forces over each node and
 * setting node speed to get the graph balanced.
 */
class Balancer implements Runnable {

	private boolean balancing = true;
	private boolean balancingLock = false;
	private Graph graph;
	private Renderer renderer;
	private int balancedCount;

	public Balancer(Graph g, Renderer r) {
		graph = g;
		renderer = r;
		balancedCount = 0;
		graph.setBalancer(this);
	}

	public void stop() {
		if (!balancingLock) {
			balancing = false;
		}
	}
	
	public void lockBalance() {
		balancingLock = true;
	}
	
	public void unlockBalance() {
		balancingLock = false;
	}

	public void awake() {
		balancing = true;
		balancedCount = 0;
		for (Enumeration e = graph.elements(); e.hasMoreElements();) {
			Node node = (Node) e.nextElement();
			node.awake();
		}
		synchronized (this) {
			this.notify();
		}
	}

	public void run() {

		Thread.currentThread().setPriority(Thread.MIN_PRIORITY);

		try {

			while (true) {

				while (!balancing) {
					synchronized (this) {
						this.wait();
					}
				}

				Enumeration e;

				e = graph.elements();
				while (e.hasMoreElements()) {
					Node node = (Node) e.nextElement();
					node.clearSpeed();
				}

				for (int j = 0; j < graph.size(); j++) {
					Node node1 = (Node) graph.elementAt(j);
					for (int k = j + 1; k < graph.size(); k++) {
						Node node2 = (Node) graph.elementAt(k);
						SpeedVector sp = node1.getForceFromNode(node2);
						node1.addSpeed(sp);
						node2.addSpeed(sp.reverse());
					}
				}

				Enumeration en = graph.elements();
				while (en.hasMoreElements()) {
					Node node = (Node) en.nextElement();
					node.balance(this);
				}

				// balances may have stopped animation
				if (balancing) {
					balancedCount = 0;
				}

				synchronized (renderer) {
					renderer.notify();
				}

				Thread.sleep(
					((Integer) Config.getValue("balancingStepInterval"))
						.intValue());

			}
		} catch (InterruptedException e) {
			System.out.println("Balancing interrupted");
		}

	}

	/**
	 * @param node
	 */
	public void notifyBalanced(Node node) {
		balancedCount++;
		if (balancedCount == graph.size()) {
			stop();
		}
	}
}
