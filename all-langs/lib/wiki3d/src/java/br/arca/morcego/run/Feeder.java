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

import java.util.Enumeration;
import java.util.Vector;

import br.arca.morcego.Config;
import br.arca.morcego.structure.Graph;
import br.arca.morcego.structure.Node;
import br.arca.morcego.transport.Transport;

/**
 * @author lfagundes
 * 
 * Feeder runs in a thread and will retrieve graph data whenever needed, then
 * pass new and old nodes to animator to put them in graph in a fancy way.
 */
public class Feeder implements Runnable {

	private Graph graph;

	private Transport transport;

	private Animator animator;

	private boolean shouldWait = true;

	public Feeder(Graph g, Transport t) {
		graph = g;
		transport = t;

		animator = new Animator(graph);
	}

	/*
	 * Returns a Vector with all nodes contained in g1 but not in g2
	 */
	private Vector diffGraphs(Graph g1, Graph g2) {
		Vector diff = new Vector();
		for (Enumeration e = g1.getNodes().elements(); e.hasMoreElements();) {
			Node node = (Node) e.nextElement();
			if (g2.getNodeById(node.getId()) == null) {
				diff.add(node);
			}
		}
		return diff;
	}

	/*
	 * Get a Vector with all nodes from g1 that is also in g2
	 *  
	 */
	private Vector intersectGraphs(Graph g1, Graph g2) {
		Vector intersection = new Vector();
		for (Enumeration e = g1.getNodes().elements(); e.hasMoreElements();) {
			Node node = (Node) e.nextElement();
			if (g2.getNodeById(node.getId()) != null) {
				intersection.add(node);
			}
		}
		return intersection;
	}

	/*
	 * Gets the new graph and feeds it's node to current graph
	 */
	public void feed(Graph newGraph) {
		synchronized (newGraph) {
			Vector removed = diffGraphs(graph, newGraph);
			Vector added = diffGraphs(newGraph, graph);
			Vector intersection = intersectGraphs(newGraph, graph);
			
			// Replace all nodes that remain in graph by fresh objects
			for (Enumeration e = intersection.elements(); e.hasMoreElements();) {
				Node newNode = (Node) e.nextElement();
				Node oldNode = graph.getNodeById(newNode.getId());
				newNode.setBody(oldNode.getBody());
				graph.removeNode(oldNode);
				graph.addNode(newNode);
				
				if (oldNode.centered()) {
					graph.center(newNode);
				}
			}

			animator.animate(added, removed);
		}
	}

	public void notifyFeeder() {
		shouldWait = false;
		this.notify();
	}
	/*
	 * (non-Javadoc)
	 * 
	 * @see java.lang.Runnable#run()
	 */
	public void run() {

		Thread.currentThread().setPriority(Thread.MIN_PRIORITY);

		Thread animationThread = new Thread(animator);
		animationThread.start();

		while (true) {
			synchronized (this) {
				try {
					if (shouldWait) {
						this.wait();
					}
					shouldWait = true;
				} catch (InterruptedException e1) {
					// TODO Auto-generated catch block
					e1.printStackTrace();
				}
			}

			Graph newGraph;
			try {
				newGraph = transport.retrieveData(graph.getCenterNode(),
						((Integer) Config.getValue(Config.navigationDepth)));
			} catch (Exception e) {
				e.printStackTrace();
				break;
			}
			feed(newGraph);
		}
	}
}