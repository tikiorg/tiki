package wiki3d;
import java.util.*;
public class XmlReader {
	Vector links = new Vector();
	Node parentNode;
	Vector actions = new Vector();
	public XmlReader(String s, Vertexes vertexList) {		
		int i = s.indexOf("graph");
		int j = s.indexOf("\"", i + 4);
		int k = s.indexOf("\"", j + 2);
		String s2 = s.substring(++j, k);
		parentNode = new Node(s2);
		parentNode.center();
		vertexList.add(parentNode);
		System.out.print("graph node=" + s2);
		while ((i = s.indexOf("link", ++j)) > 0) {
			j = s.indexOf("\"", i + 4);
			k = s.indexOf("\"", j + 2);
			String name = s.substring(++j, k);
			System.out.println("link name " + name);
			Node childNode = new Node(name);
			childNode.addLink(parentNode);
			//g.addLink(l);
			vertexList.add(childNode);
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

}
