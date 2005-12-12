/*
 * Created on May 24, 2004
 *
 * To change the template for this generated file go to
 * Window - Preferences - Java - Code Generation - Code and Comments
 */
package br.arca.morcego.structure;

import java.util.Hashtable;

import br.arca.morcego.structure.link.SolidLink;
import br.arca.morcego.structure.node.RoundNode;

/**
 * @author lfagundes
 *
 * To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Generation - Code and Comments
 */
public class GraphElementFactory {
	
	// Holds type names with hashtable of properties
	private static Hashtable types = new Hashtable();
	
	public static void defineType(String type, Hashtable properties) {
		types.put(type,properties);
	}

	/**
	 * @param node
	 * @param neighbour
	 * @return
	 */
	public static Link createLink(Node node, Node neighbour) {
		return new SolidLink(node,neighbour);		
	}

	/**
	 * @param nodeName
	 * @param graph
	 * @return
	 */
	public static Node createNode(String nodeName, Graph graph) {
		return new RoundNode(nodeName, graph);
	}

}
