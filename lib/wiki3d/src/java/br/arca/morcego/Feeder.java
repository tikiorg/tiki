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

import java.util.Enumeration;
import java.util.Vector;

/**
 * @author lfagundes
 * 
 * Feeder runs in a thread and will retrieve graph data whenever needed, then
 * pass new and old nodes to animator to put them in graph in a fancy way.
 */
public class Feeder implements Runnable {

	private Graph graph;
	private GraphDataRetriever retriever;
	private Animator animator;

	public Feeder(Graph g, GraphDataRetriever gdr) {
		graph = g;
		retriever = gdr;

		animator = new Animator(graph);
	}

	/*
	 * Returns a Vector with all nodes contained in g1 but not in g2
	 */
	private Vector diffGraphs(Graph g1, Graph g2) {
		Vector diff = new Vector();
		for (Enumeration e = g1.elements(); e.hasMoreElements();) {
			Node node = (Node) e.nextElement();
			if (g2.nodeFromName(node.getName()) == null) {
				diff.add(node);
			}
		}
		return diff;
	}

	/*
	 * Gets the new graph and feeds it's node to current graph
	 */
	public void feed(Graph newGraph) {
		Vector removed = diffGraphs(graph, newGraph);
		Vector added = diffGraphs(newGraph, graph);
		animator.animate(added, removed);
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
					this.wait();
				} catch (InterruptedException e1) {
					// TODO Auto-generated catch block
					e1.printStackTrace();
				}
			}

			Graph newGraph;
			try {
				newGraph =
					retriever.retrieveData(graph.getCenterNode().getName());
			} catch (Exception e) {
				e.printStackTrace();
				break;
			}
			feed(newGraph);
		}
	}
}
