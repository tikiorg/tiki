/*
 * Morcego - 3D network browser Copyright (C) 2004 Luis Fagundes - Arca
 * <lfagundes@arca.ime.usp.br>
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; either version 2.1 of the License, or (at your option)
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

package br.arca.morcego.structure;

import java.awt.Component;
import java.awt.Graphics;
import java.awt.event.MouseEvent;
import java.util.Collections;
import java.util.Comparator;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import javax.swing.event.MouseInputListener;

import br.arca.morcego.Config;
import br.arca.morcego.Morcego;
import br.arca.morcego.physics.Matrix3x3;
import br.arca.morcego.physics.PositionedObject;
import br.arca.morcego.physics.Vector3D;
import br.arca.morcego.run.Balancer;
import br.arca.morcego.run.Feeder;
import br.arca.morcego.run.Rotator;
import br.arca.morcego.transport.Transport;

public class Graph extends Component implements MouseInputListener,
		PositionedObject, Runnable {

	private Vector nodes;

	private Vector links;

	private Node centerNode;

	private Hashtable nodesFromId = new Hashtable();

	private Rotator rotator;

	private Matrix3x3 rotation;

	private Vector3D orientation;

	private Balancer balancer;

	private Feeder feeder;

	private int dragSpeedX;

	private int dragSpeedY;

	private boolean focusFixed = false;

	private Vector elements;

	private GraphElement oldFocus;

	private float depth;

	private GraphElement focus;

	private int previousX, previousY;

	private final class RenderingStrategy implements Comparator {
		public int compare(Object o1, Object o2) {
			if (((GraphElement) o1).getDepth() > ((GraphElement) o2).getDepth()) {
				return -1;
			} else if (((GraphElement) o1).getDepth() == ((GraphElement) o2)
					.getDepth()) {
				return 0;
			} else {
				return 1;
			}
		}
	}

	public Graph() {
		elements = new Vector();

		nodes = new Vector();
		links = new Vector();
		rotation = new Matrix3x3();

		orientation = new Vector3D(1, 0, 0);

	}

	public void addElement(GraphElement e) {
		e.setGraph(this);
		elements.add(e);
	}

	public void addNode(Node node) {

		if (getNodeById(node.getId()) == null) {
			addElement(node);
			nodes.add(node);
			nodesFromId.put(node.getId(), node);

			for (Enumeration e = node.getLinkList(); e.hasMoreElements();) {
				String neighbourName = (String) e.nextElement();
				Node neighbour = getNodeById(neighbourName);
				if (neighbour != null) {
					Link link = GraphElementFactory.createLink(node, neighbour);
					addElement(link);
					links.add(link);
				}
			}

			if (balancer != null) {
				synchronized (balancer) {
					balancer.awake();
				}
			}
		}
	}

	/**
	 *  
	 */
	synchronized private void order() {
		Collections.sort(elements, new RenderingStrategy());
	}

	public void navigateTo(Node node) {
		center(node);

		//notifyBalancer();
		notifyFeeder();
	}

	/**
	 * @param newNode
	 */
	public void center(Node newCenter) {
		if (centerNode != null) {
			centerNode.unCenter();
		}

		centerNode = newCenter;
		newCenter.center();	
	}

	public void removeNode(Node node) {

		elements.remove(node);
		nodes.remove(node);

		if (focus == node) {
			focus = null;
		}

		nodesFromId.remove(node.getId());

		Vector removedLinks = new Vector();
		for (Enumeration e = links.elements(); e.hasMoreElements();) {
			Link link = (Link) e.nextElement();
			if (link.hasNode(node)) {
				removedLinks.add(link);
			}
		}

		for (Enumeration e = removedLinks.elements(); e.hasMoreElements();) {
			Link link = (Link) e.nextElement();
			elements.remove(link);
			links.remove(link);
		}

	}

	public Node getNodeById(String nodeId) {
		Node node = (Node) nodesFromId.get(nodeId);
		return node;
	}

	/**
	 * @return Returns the centerNode.
	 */
	public Node getCenterNode() {
		return centerNode;
	}

	/**
	 * @param node
	 * @return
	 */
	public boolean connected(Node node) {
		for (Enumeration e = node.getLinkList(); e.hasMoreElements();) {
			if (getNodeById((String) e.nextElement()) != null) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param e
	 */
	private void savePosition(MouseEvent e) {
		previousX = e.getX();
		previousY = e.getY();
	}

	public boolean contains(MouseEvent e) {
		if (focusFixed) {
			return true;
		}
		// get focus
		order();
		boolean contains = false;
		for (int i = elements.size() - 1; i >= 0 && !contains; i--) {
			GraphElement component = (GraphElement) elements.elementAt(i);
			if (component.visible() && component.contains(e)) {
				oldFocus = focus;
				focus = component;
				contains = true;
			}
		}

		if (!contains) {
			oldFocus = focus;
			focus = null;
		}

		if (focus != oldFocus) {
			if (focus != null) {
				focus.mouseEntered(e);
			}
			if (oldFocus != null) {
				oldFocus.mouseExited(e);
			}
		}

		// graph claims to contain anything by now
		// TODO strange...
		return true;
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see br.arca.morcego.ScreenComponent#paint(java.awt.Graphics)
	 */
	public void paint(Graphics g) {
		synchronized (elements) //Sync on elements so that they don't change
		// after order and during painting.
		{
			order();
			for (Enumeration e = elements.elements(); e.hasMoreElements();) {
				GraphElement element = (GraphElement) e.nextElement();
				if (element.visible()) {
					element.paint(g);
				}
			}
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mouseClicked(java.awt.event.MouseEvent)
	 */
	public void mouseClicked(MouseEvent e) {
		rotator.stop();
		if (contains(e)) {
			if (focus != null) {
				focus.mouseClicked(e);
			}
		}

		savePosition(e);
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mouseEntered(java.awt.event.MouseEvent)
	 */
	public void mouseEntered(MouseEvent e) {
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mouseExited(java.awt.event.MouseEvent)
	 */
	public void mouseExited(MouseEvent e) {
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mousePressed(java.awt.event.MouseEvent)
	 */
	public void mousePressed(MouseEvent e) {

		rotator.stop();

		if (focus != null) {
			focus.mousePressed(e);
		}

		savePosition(e);
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseListener#mouseReleased(java.awt.event.MouseEvent)
	 */
	public void mouseReleased(MouseEvent e) {

		if (focus != null) {
			focus.mouseReleased(e);
		} else {
			if (Math.abs(dragSpeedX) + Math.abs(dragSpeedY) > 4) {
				// TODO putaquepariuquegambiarradaporra
				rotator.spin(dragSpeedY / 5, -dragSpeedX / 5);
			}
		}
		savePosition(e);
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseMotionListener#mouseDragged(java.awt.event.MouseEvent)
	 */
	public void mouseDragged(MouseEvent e) {

		if (focus != null) {
			focus.mouseDragged(e);
		} else {
			dragSpeedX = e.getX() - previousX;
			dragSpeedY = e.getY() - previousY;
			rotate(dragSpeedY, -dragSpeedX);
		}

		Morcego.notifyRenderer();

		savePosition(e);

	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.awt.event.MouseMotionListener#mouseMoved(java.awt.event.MouseEvent)
	 */
	public void mouseMoved(MouseEvent e) {
		if (contains(e)) {
			if (focus != null) {
				focus.mouseMoved(e);
			}
		}

		savePosition(e);
	}

	/**
	 * @return
	 */
	public Vector getNodes() {
		return nodes;
	}

	public Vector getLinks() {
		return links;
	}

	public void notifyFeeder() {
		synchronized (feeder) {
			feeder.notifyFeeder();
		}
	}

	public void notifyBalancer() {
		synchronized (balancer) {
			balancer.awake();
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.lang.Runnable#run()
	 */
	public void run() {

		Graph firstGraph = null;

		Class transportClass = null;
		Transport transport = null;
		try {
			transportClass = Config.getClass(Config.transportClass);
			transport = (Transport) transportClass.newInstance();
			transport.setup();
		} catch (Exception e) {
			e.printStackTrace();
			return;
		}

		feeder = new Feeder(this, transport);
		balancer = new Balancer(this);
		rotator = new Rotator(this);

		Thread feedingThread = new Thread(feeder);
		Thread balanceThread = new Thread(balancer);
		Thread spinningThread = new Thread(rotator);

		feedingThread.start();
		spinningThread.start();

		Node firstNode = Config.getNode(Config.startNode);

		addNode(firstNode);
		navigateTo(firstNode);

		balanceThread.start();
	}

	/**
	 * @param focusFixed
	 *            The focusFixed to set.
	 */
	public void fixFocus() {
		focusFixed = true;
	}

	public void releaseFocus() {
		focusFixed = false;
		focus = null;
	}

	/**
	 * @return Returns the orientation.
	 */
	public Vector3D getOrientation() {
		return orientation;
	}

	synchronized public void rotate(float xTheta, float yTheta) {
		Enumeration e = getNodes().elements();
		while (e.hasMoreElements()) {
			Node node = (Node) e.nextElement();
			node.rotate(xTheta, yTheta);
		}

		getOrientation().rotate(xTheta, yTheta);
		Morcego.getCamera().adjustPosition(this);
	}

	/**
	 * @return
	 */
	public Node getFocus() {
		// TODO should return GraphElement (link should be focusable)
		return (Node) focus;
	}

	public Vector getElements() {
		return elements;
	}
}