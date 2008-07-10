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

/**
 * @author lfagundes
 * 
 * Takes from Feeder nodes to be added and removed from graph and do this in a
 * fancy way
 */
public class Animator implements Runnable {

	private Graph graph;
	private Vector addedStack;
	private Vector removedStack;

	/**
	 *  
	 */
	public Animator(Graph g) {
		graph = g;
		addedStack = new Vector();
		removedStack = new Vector();
	}

	/*
	 * Takes a vector of nodes to be added and one to be removed
	 * and notifies thread that is animating.	 
	 */
	public void animate(Vector add, Vector remove) {
		synchronized (this) {
			addedStack = add;
			removedStack = remove;
			this.notify();
		}
	}

	/*
	 * (non-Javadoc)
	 * When notified takes the added and removed nodes and
	 * animate the insertion and deletion
	 * TODO: When user navigates during animation the removed
	 * nodes remain
	 * @see java.lang.Runnable#run()
	 */
	public void run() {

		Thread.currentThread().setPriority(Thread.MIN_PRIORITY);

		while (true) {
			try {
				if (addedStack.isEmpty() && removedStack.isEmpty()) {
					synchronized (this) {
						this.wait();
					}
				}
			} catch (InterruptedException e2) {				
				e2.printStackTrace();
			}
			for (Enumeration e = removedStack.elements();
				e.hasMoreElements();
				) {
				graph.removeNode((Node) e.nextElement());
			}
			
			while (!addedStack.isEmpty()) {
				Node node = (Node) addedStack.remove(0);
				int i = 0;
				while (i++ < addedStack.size() && !graph.connected(node)) {
					addedStack.add(node);
					node = (Node) addedStack.remove(0);
				}
				
				graph.addNode(node);

				try {
					Thread.sleep(
						Config.getInteger(Config.feedAnimationInterval));
				} catch (InterruptedException e1) {					
					e1.printStackTrace();
				}
			}
			addedStack = new Vector();
			removedStack = new Vector();
		}
	}

}
