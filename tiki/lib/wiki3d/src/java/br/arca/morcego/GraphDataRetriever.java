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

import java.awt.Color;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import org.apache.xmlrpc.XmlRpcAppletClient;
import org.apache.xmlrpc.XmlRpcException;

/**
 * @author lfagundes
 * 
 * To change the template for this generated type comment go to Window -
 * Preferences - Java - Code Generation - Code and Comments
 */
public class GraphDataRetriever {

	private XmlRpcAppletClient client;
	private String url;

	public GraphDataRetriever(String server_url) {
		url = server_url;
		try {
			client = new XmlRpcAppletClient(server_url);
		} catch (MalformedURLException e) {
			System.out.println("Bad URL " + url);
			e.printStackTrace();
		}
	}

	public Graph retrieveData(String centerNodeName) {

		Graph graph = new Graph();

		Vector params = new Vector();
		params.add(new String(centerNodeName));
		params.add(Config.getValue("navigationDepth"));

		Hashtable result = new Hashtable();
		try {
			result = (Hashtable) client.execute("getSubGraph", params);
		} catch (XmlRpcException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		Hashtable nodes = (Hashtable) result.get("graph");

		for (Enumeration eN = nodes.keys(); eN.hasMoreElements();) {
			String nodeName = (String) eN.nextElement();
			Node node = new Node(nodeName, graph);
			Hashtable nodeData = (Hashtable) nodes.get(nodeName);
			
			Vector neighbours = (Vector) nodeData.get("neighbours");
			for (Enumeration eL = neighbours.elements();
				eL.hasMoreElements();
				) {
				node.addLink((String) eL.nextElement());
			}
			
			String nodeColor = (String) nodeData.get("color");
			if (nodeColor != null) {
				node.setColor(Color.decode(nodeColor));
			}
			
			String nodeActionUrl = (String) nodeData.get("actionUrl");
			if (nodeActionUrl != null) {
				try {
					node.setActionUrl(new URL(nodeActionUrl));
				} catch (MalformedURLException e1) {
					// Malformed url will be ignored
				}
			}
		}
		
		return graph;
	}

}
