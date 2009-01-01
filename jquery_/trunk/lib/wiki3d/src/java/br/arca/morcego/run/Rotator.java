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
package br.arca.morcego.run;

import br.arca.morcego.Morcego;
import br.arca.morcego.structure.Graph;

/**
 * @author lfagundes
 * 
 * To change the template for this generated type comment go to Window -
 * Preferences - Java - Code Generation - Code and Comments
 */
public class Rotator implements Runnable {

	private Graph graph;

	private float xTheta, yTheta;
	
	private boolean spinning;

	/**
	 *  
	 */
	public Rotator(Graph g) {
		super();
		graph = g;

		xTheta = yTheta = 0;
		
		spinning = false;
	}

	/**
	 * @return Returns the rotating.
	 */
	public boolean isSpinning() {
		return spinning;
	}

	/**
	 * @param rotating
	 *            The rotating to set.
	 */

	public void spin(float xTheta, float yTheta) {
		spinning = true;
		
		this.xTheta = xTheta;
		this.yTheta = yTheta;

//		spin = Matrix3x3.getXRotation(xTheta).multiplyByMatrix(
//				Matrix3x3.getYRotation(yTheta));

		synchronized (this) {
			notify();
		}
	}

	public void stop() {
		spinning = false;
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.lang.Runnable#run()
	 */
	public void run() {

		Thread.currentThread().setPriority(Thread.MIN_PRIORITY);

		while (true) {
			while (!isSpinning()) {
				synchronized (this) {
					try {
						this.wait();
					} catch (InterruptedException e) {
						// Keep waiting
					}
				}
			}

			graph.rotate(xTheta, yTheta);

			Morcego.notifyRenderer();

			try {
				Thread.sleep(50);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}

}