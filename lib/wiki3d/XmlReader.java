package wiki3d;

import java.io.DataInputStream;
import java.net.URL;

public class XmlReader {

	public String url;

	public XmlReader(String url) {
		this.url = url;
	}

	public void getNodeData(String nodeName, Graph graph) {
		String s = getUrlData(nodeName);
		int i = s.indexOf("graph");
		int j = s.indexOf("\"", i + 4);
		int k = s.indexOf("\"", j + 2);
		nodeName = s.substring(++j, k);
		//parentNode = new Node(nodeName);
		//graph.add(parentNode);
		Node parentNode = graph.add(nodeName);
		
		System.out.print("graph node=" + nodeName);

		while ((i = s.indexOf("link", ++j)) > 0) {
			j = s.indexOf("\"", i + 4);
			k = s.indexOf("\"", j + 2);
			String name = s.substring(++j, k);
			System.out.println("link name " + name);
			//Node childNode = new Node(name);
			//childNode.addLink(parentNode);
			//graph.add(childNode);
			graph.add(name);
			graph.addLink(name, parentNode.name);
			int lastlink = s.indexOf("</link>", j);
			while (k < lastlink - 1) //process actions
				{
				j = s.indexOf("action", j + 1);
				if (j == -1 || j >= lastlink) {
					j = lastlink + 4;
					break;
				}
				j = s.indexOf("\"", j + 10); //get start of label
				// tab
				k = s.indexOf("\"", j + 2); //end of lable tag
				String label = s.substring(j + 1, k);
				j = s.indexOf("\"", k + 3); //start of url
				k = s.indexOf("\"", j + 1); //end
				String url = s.substring(j + 1, k);

				j = k;
			}

			j = lastlink + 4;

		}

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
				System.out.print((char) j);
				buffer.append((char) j);
			}
		} catch (Exception e) {
			System.out.println("Can't get URL");
		}

		return buffer.toString();
	}

}
