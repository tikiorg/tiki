/*
 * Morcego - 3D network browser
 * Copyright (C) 2004 Luis Fagundes - Arca <lfagundes@arca.ime.usp.br> 
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
package br.arca.morcego;

/**
 * @author lfagundes
 *
 * To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Generation - Code and Comments
 */
public class Spinner implements Runnable {
	
	private Graph graph;
	private Renderer renderer;

	/**
	 * 
	 */
	public Spinner(Graph g, Renderer r) {
		super();
		graph = g;
		renderer = r;
		
		graph.setSpinner(this);
	}

	/* (non-Javadoc)
	 * @see java.lang.Runnable#run()
	 */
	public void run() {

		Thread.currentThread().setPriority(Thread.MIN_PRIORITY);

		while (true) {
			while (!graph.isRotating()) {
				synchronized(this) {
					try {
						this.wait();
					} catch (InterruptedException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
				}
			}
			
			synchronized (renderer) {
				renderer.notify();
			}
			
			try {
				Thread.sleep(50);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}

}
