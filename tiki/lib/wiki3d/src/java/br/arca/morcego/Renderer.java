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
 * Thread that keeps rendering graph in screen
 */
public class Renderer implements Runnable {

	private Morcego applet;
	
	public Renderer(Morcego applet) {
		this.applet = applet;
	}
	
	/* (non-Javadoc)
	 * @see java.lang.Runnable#run()
	 */
	public void run() {
		// TODO avoid concurrent notifies from Balancer and Spinner
		while (true) {
			synchronized (this) {
				try {
					this.wait();
				} catch (InterruptedException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
			
			synchronized(applet.getGraph()) {
				applet.getGraph().rotateNodes();		
				applet.repaint();
			}			
			
		}
		
	}
	
}
