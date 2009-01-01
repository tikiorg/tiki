/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki3d/Navigator.java,v 1.8 2006-10-22 03:21:39 mose Exp $
 *
 * Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

/*
 * Created on Apr 19, 2004
 * 
 * To change the template for this generated file go to Window - Preferences -
 * Java - Code Generation - Code and Comments
 */
package wiki3d;

import java.util.Enumeration;
import java.util.Iterator;
import java.util.Set;
import java.util.Vector;

/**
 * @author lfagundes
 * 
 * To change the template for this generated type comment go to Window -
 * Preferences - Java - Code Generation - Code and Comments
 */
public class Navigator implements Runnable {
	Graph graph;
	private XmlReader xmlReader;
	private Node centerNode;
	private boolean changed;

	public Navigator(Graph g, XmlReader xr) {
		graph = g;		
		xmlReader = xr;
		changed = false;
	}

	public void navigateFirst(String nodeName) {
		Node node = xmlReader.getNode(nodeName);
		graph.add(node);
		navigateTo(node);
	}

	public void navigateTo(Node node) {

		if (centerNode != null) {
			centerNode.unCenter();
		}

		centerNode = node;
		node.center();
		changed = true;
	}

	/**
	 *  
	 */
	private void clearMappingTrail() {
		for (Enumeration e = graph.elements(); e.hasMoreElements();) {
			Node node = (Node) e.nextElement();
			node.passed = false;
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.lang.Runnable#run()
	 */
	public void run() {

		Thread.currentThread().setPriority(Thread.MIN_PRIORITY);

		while (true) {
			if (changed) {
				changed = false;
				clearMappingTrail();
				getNewNodes();
				removeOldNodes();

			}
			try {
				Thread.sleep(50);
			} catch (InterruptedException e) {
				e.printStackTrace();
			}

		}
	}

	/**
	 *  
	 */
	private void removeOldNodes() {
		Vector oldNodes = new Vector();
		for (Enumeration e = graph.elements(); e.hasMoreElements();) {
			Node node = (Node) e.nextElement();
			if (!node.passed) {				
				oldNodes.add(node);
			} 
		}
		for (Enumeration e = oldNodes.elements(); e.hasMoreElements();) {
			Node node = (Node) e.nextElement();
			graph.removeNode(node);
		}

	}

	/**
	 *  
	 */
	private void getNewNodes() {
		Vector levelNodes = new Vector();
		Vector nextLevelNodes = new Vector();
		Vector passedNodes = new Vector();
		levelNodes.add(centerNode.name);
		for (int level = 0; level <= Config.navigationDepth; level++) {
			for (Enumeration e = levelNodes.elements(); e.hasMoreElements();) {
				String nodeName = (String) e.nextElement();

				if (!passedNodes.contains(nodeName)) {
					passedNodes.add(nodeName);

					Node node = graph.nodeFromName(nodeName);
					if (node == null) {
						node = xmlReader.getNode(nodeName);
						graph.add(node);
					}
					node.passed = true;

					Set linkSet = node.links.keySet();
					for (Iterator it = linkSet.iterator(); it.hasNext();) {
						String neighbourName = (String) it.next();
						if (!passedNodes.contains(neighbourName)
							&& !levelNodes.contains(neighbourName)) {
							nextLevelNodes.add(neighbourName);
						}
					}
				}
			}
			levelNodes = nextLevelNodes;
			nextLevelNodes = new Vector();
		}
	}
}
