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
import java.awt.*;

public class Graph extends Vector {

	private final class RenderingStrategy implements Comparator {
		public int compare(Object o1, Object o2) {
			if (((Node) o1).z < ((Node) o2).z) {
				return -1;
			} else if (((Node) o1).z == ((Node) o2).z) {
				return 0;
			} else {
				return 1;
			}
		}
	}

	private Node focus;

	private Node centerNode;

	private Matrix3D rotation;

	private Hashtable nodesFromName = new Hashtable();
	private Vertex origin;
	private boolean rotating;

	private Spinner spinner;
	private Balancer balancer;

	public Graph() {
		rotation = new Matrix3D();
		rotation.setIdentity();

		origin =
			new Vertex(
				((Integer) Config.getValue("originX")).intValue(),
				((Integer) Config.getValue("originY")).intValue(),
				((Integer) Config.getValue("originZ")).intValue());
	}

	public void add(Node node) {
		if (node.getGraph() != this) {
			node.setGraph(this);
		}

		if (nodeFromName(node.getName()) == null) {
			super.add(node);
			nodesFromName.put(node.getName(), node);
		}
		
		if (balancer != null) {
			synchronized (balancer) {
				balancer.awake();
			}
		}
	}

	public void navigateTo(Node node) {

		if (centerNode != null) {
			centerNode.unCenter();
		}

		centerNode = node;
		node.center();
	}

	public boolean contains(int x, int y) {
		int i = size();

		while (i > 0) {
			i--;
			if (((Node) elementAt(i)).contains(x, y)) {
				focus = ((Node) elementAt(i));
				return true;
			}
		}

		return false;

	}

	synchronized public void paint(Graphics g) {
		Collections.sort(this, getStrategy());
		for (int i = 0; i < size(); i++) {
			try {
				((Node) elementAt(i)).paint(g);
			} catch (Exception ex) {
			}
		}

	}

	private Comparator getStrategy() {
		return new RenderingStrategy();
	}

	public void rotateNodes() {
		if (!rotation.isIdentity()) {
			Enumeration e = elements();
			while (e.hasMoreElements()) {
				Node node = (Node) e.nextElement();
				rotation.transform(node);
				node.proj();
			}

			if (!rotating) {
				rotation.setIdentity();
			}
		}

	}

	public void removeNode(Node node) {
		for (int i = 0; i < size(); i++) {
			if (this.elementAt(i) == node) {
				this.remove(i);
			}
		}
		if (focus == node) {
			focus = null;
		}
		nodesFromName.remove(node.getName());
	}

	public void rotate(float xtheta, float ytheta) {
		if (xtheta > ((Float) Config.getValue("maxTheta")).floatValue())
			xtheta = ((Float) Config.getValue("maxTheta")).floatValue();
		else if (xtheta < - ((Float) Config.getValue("maxTheta")).floatValue())
			xtheta = - ((Float) Config.getValue("maxTheta")).floatValue();
		else if (
			xtheta > 0
				&& xtheta < ((Float) Config.getValue("minTheta")).floatValue())
			xtheta = ((Float) Config.getValue("minTheta")).floatValue();
		else if (
			xtheta < 0
				&& xtheta > - ((Float) Config.getValue("minTheta")).floatValue())
			xtheta = - ((Float) Config.getValue("minTheta")).floatValue();

		if (ytheta > ((Float) Config.getValue("maxTheta")).floatValue())
			ytheta = ((Float) Config.getValue("maxTheta")).floatValue();
		else if (ytheta < - ((Float) Config.getValue("maxTheta")).floatValue())
			ytheta = - ((Float) Config.getValue("maxTheta")).floatValue();
		else if (
			ytheta > 0
				&& ytheta < ((Float) Config.getValue("minTheta")).floatValue())
			ytheta = ((Float) Config.getValue("minTheta")).floatValue();
		else if (
			ytheta < 0
				&& ytheta > ((Float) Config.getValue("minTheta")).floatValue())
			ytheta = - ((Float) Config.getValue("minTheta")).floatValue();

		rotation.setIdentity();
		rotation.xrot(-xtheta);
		rotation.yrot(-ytheta);

	}

	public Node nodeFromName(String nodeName) {
		Node node = (Node) nodesFromName.get(nodeName);
		return node;
	}

	public Node getFocus() {
		return focus;
	}

	/**
	 * @return Returns the centerNode.
	 */
	public Node getCenterNode() {
		return centerNode;
	}

	/**
	 * @return Returns the origin.
	 */
	public Vertex getOrigin() {
		return origin;
	}

	/**
	 * @return Returns the rotating.
	 */
	public boolean isRotating() {
		return rotating;
	}

	/**
	 * @param rotating
	 *                  The rotating to set.
	 */
	public void setRotating(boolean rotating) {
		this.rotating = rotating;
		if (rotating && spinner != null) {
			synchronized (spinner) {
				spinner.notify();
			}
		}
	}

	/**
	 * @return Returns the rotation.
	 */
	public Matrix3D getRotation() {
		return rotation;
	}

	/**
	 * @param spinner
	 *                  The spinner to set.
	 */
	public void setSpinner(Spinner spinner) {
		this.spinner = spinner;
	}

	/**
	 * @param balancer The balancer to set.
	 */
	public void setBalancer(Balancer balancer) {
		this.balancer = balancer;
	}

	/**
	 * @param node
	 * @return
	 */
	public boolean connected(Node node) {
		for (Enumeration e = node.getLinkList(); e.hasMoreElements();) {
			if (nodeFromName((String) e.nextElement()) != null) {
				return true;
			}
		}
		
		return false;
	}

}