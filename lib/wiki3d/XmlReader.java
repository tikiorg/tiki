/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki3d/XmlReader.java,v 1.11 2006-10-22 03:21:41 mose Exp $
 *
 * Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

package wiki3d;

import java.io.DataInputStream;
import java.net.URL;

public class XmlReader {

	public String url;
	public Graph graph;

	public XmlReader(String url, Graph graph) {
		this.url = url;
		this.graph = graph;
	}

	public Node getNode(String nodeName) {
		String s = getUrlData(nodeName);
		int i = s.indexOf("graph");
		int j = s.indexOf("\"", i + 4);
		int k = s.indexOf("\"", j + 2);
		nodeName = s.substring(++j, k);
		//parentNode = new Node(nodeName);
		//graph.add(parentNode);
		Node node = new Node(nodeName, graph);
		
		
		while ((i = s.indexOf("link", ++j)) > 0) {
			j = s.indexOf("\"", i + 4);
			k = s.indexOf("\"", j + 2);
			String name = s.substring(++j, k);
			
			node.addLink(name);
			int lastlink = s.indexOf("</link>", j);			

			j = lastlink + 4;

		}
		
		return node;
	}

	private String getUrlData(String nodeName) {
		StringBuffer buffer = new StringBuffer();
		
		try {
			URL u = new URL(url + "?page=" + nodeName);
			DataInputStream b1 = new DataInputStream(u.openStream());
			int j;
			while (true) {

				j = b1.read();
				if (j == -1)
					break;
				
				buffer.append((char) j);
			}
		} catch (Exception e) {
			System.out.println("Can't get URL");
		}

		return buffer.toString();
	}

}
