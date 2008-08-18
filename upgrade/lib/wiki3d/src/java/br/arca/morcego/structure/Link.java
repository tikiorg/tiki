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
package br.arca.morcego.structure;

import java.awt.Graphics;
import java.awt.event.MouseEvent;

import br.arca.morcego.Morcego;
import br.arca.morcego.physics.Spring;

/**
 * @author lfagundes
 * 
 * To change the template for this generated type comment go to Window -
 * Preferences - Java - Code Generation - Code and Comments
 */
public abstract class Link extends GraphElement {

	//private Graph graph;
	
	private Spring spring;
	
	protected Node node1, node2;
	
	/**
	 *  
	 */
	public Link(Node n1, Node n2) {
		super();
		
		spring = new Spring(n1.getBody(), n2.getBody());

		node1 = n1;
		node2 = n2;
		
	}

	public boolean hasNode(Node node) {
		return node1 == node || node2 == node;
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see br.arca.morcego.ScreenComponent#getDepth()
	 */
	public float getDepth() {		
		return node1.getDepth() + node2.getDepth() / 2;
	}

	
	/*
	 * (non-Javadoc)
	 * 
	 * @see br.arca.morcego.ScreenComponent#contains(int, int)
	 */
	public boolean contains(MouseEvent e) {
		/*try {
			return Math.abs((node1.y - node2.y) / (e.getY() - node2.y)
			 - (node1.x - node2.x) / (e.getX() - node2.x)) <= 0.3f
			&& e.getX() < Math.max(node1.u, node2.u)
			&& e.getY() < Math.max(node1.v, node2.v)
			&& e.getX() > Math.min(node1.u, node2.u)
			&& e.getY() > Math.min(node1.v, node2.v);			
		} catch (ArithmeticException ex) {
			return (e.getX() == node1.x && e.getY() == node1.y)
				|| (e.getX() == node2.x && e.getY() == node2.y);
		} */
		return false;
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see br.arca.morcego.ScreenComponent#draw(java.awt.Graphics)
	 */
	public void paint(Graphics g) {
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mouseClicked(java.awt.event.MouseEvent)
	 */
	public void mouseClicked(MouseEvent e) {
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mouseEntered(java.awt.event.MouseEvent)
	 */
	public void mouseEntered(MouseEvent e) {
		Morcego.setHandCursor();
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mouseExited(java.awt.event.MouseEvent)
	 */
	public void mouseExited(MouseEvent e) {
		Morcego.setDefaultCursor();
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mousePressed(java.awt.event.MouseEvent)
	 */
	public void mousePressed(MouseEvent e) {
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mouseReleased(java.awt.event.MouseEvent)
	 */
	public void mouseReleased(MouseEvent e) {
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseMotionListener#mouseDragged(java.awt.event.MouseEvent)
	 */
	public void mouseDragged(MouseEvent e) {
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseMotionListener#mouseMoved(java.awt.event.MouseEvent)
	 */
	public void mouseMoved(MouseEvent e) {
	}


	public Spring getSpring() {
		return spring;
	}
	
	public void setProperty(String name, Object value) {
		if (name.equals("springSize")) {
			spring.setSize(((Float)value).floatValue());
		}
		if (name.equals("springElasticConstant")) {
			spring.setElasticConstant(((Float)value).floatValue());
		}
		super.setProperty(name, value);
	}
	public Node getNode1() {
		return node1;
	}
	public Node getNode2() {
		return node2;
	}
	
	public boolean visible() {
		return getNode1().visible() && getNode2().visible();
	}
	
}
