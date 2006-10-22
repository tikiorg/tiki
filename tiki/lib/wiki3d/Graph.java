/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki3d/Graph.java,v 1.10 2006-10-22 03:21:39 mose Exp $
 *
 * Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

package wiki3d;
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

	public void add(Node node) {
		super.add(node);
		nodesFromName.put(node.name, node);
	}

	Node focus;
	public Matrix3D rotation, // Used to rotate graph
	//accumulatedRotation, // Holds all rotations made to get current
									 // angle
	nodePositioner; // Aplied to all nodes to know position


	public Hashtable nodesFromName = new Hashtable();
	public Vertex origin;
	boolean rotating ;

	
	public Graph() {
		rotation = new Matrix3D();
		//accumulatedRotation = new Matrix3D();
		nodePositioner = new Matrix3D();
		
		rotation.setIdentity();
		//accumulatedRotation.setIdentity();
		nodePositioner.setIdentity();
	}
	
	public void setOrigin(int x, int y, int z) {
		origin = new Vertex(x,y,z);
		//nodePositioner.translate(x, y, z);
	}

	
	public boolean contains(int x, int y) {
		int i = 0;

		while (i < size()) {
			if (((Node) elementAt(i)).contains(x, y)) {
				focus = ((Node) elementAt(i));
				return true;
			}
			i++;
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

	public void transform() {
		
		Enumeration e = elements();
		while (e.hasMoreElements()) {
			Node node = (Node) e.nextElement();
			//accumulatedRotation.transform(node);
			rotation.transformReal(node);
			node.proj();
		}
		
		if (!rotating ) {
			rotation.setIdentity();
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
		node.remove();
	}

	public void rotate(float xtheta, float ytheta) {
		if (xtheta > Config.thetamax)
			xtheta = Config.thetamax;
		else if (xtheta < -Config.thetamax)
			xtheta = -Config.thetamax;
		else if (xtheta > 0 && xtheta < Config.thetamin)
			xtheta = Config.thetamin;
		else if (xtheta < 0 && xtheta > -Config.thetamin)
			xtheta = -Config.thetamin;

		if (ytheta > Config.thetamax)
			ytheta = Config.thetamax;
		else if (ytheta < -Config.thetamax)
			ytheta = -Config.thetamax;
		else if (ytheta > 0 && ytheta < Config.thetamin)
			ytheta = Config.thetamin;
		else if (ytheta < 0 && ytheta > Config.thetamin)
			ytheta = -Config.thetamin;

		rotation.setIdentity();
		rotation.xrot(-xtheta);
		rotation.yrot(-ytheta);
		
		nodePositioner.setIdentity();
		
	}

	public Node nodeFromName(String nodeName) {
		Node node = (Node) nodesFromName.get(nodeName);
		return node;
	}

}
