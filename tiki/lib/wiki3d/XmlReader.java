package wiki3d;
import java.util.*;
public class XmlReader {
	Vector links = new Vector();
	CenterNode g;
	Vector actions = new Vector();
	public XmlReader(String s, Vertexes v) {
		int i = s.indexOf("graph");
		int j = s.indexOf("\"", i + 4);
		int k = s.indexOf("\"", j + 2);
		String s2 = s.substring(++j, k);
		g = new CenterNode(s2);
		v.add(g);
		System.out.print("graph node=" + s2);
		while ((i = s.indexOf("link", ++j)) > 0) {
			j = s.indexOf("\"", i + 4);
			k = s.indexOf("\"", j + 2);
			String name = s.substring(++j, k);
			System.out.println("link name " + name);
			Node l = new Node(name, g);
			//g.addLink(l);
			v.add(l);
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
				Action a = new Action(label, url, l);
				l.addAction(a);
				//  v.add(a);

				j = k;
			}

			j = lastlink + 4;

		}

	}
	public static void main(String[] args) {
		String s =
			"<graph node=\"x1\"><link name=\"link1\"><action label=\"a1 1st action\" url=\"a1u1\"><action label=\"a2 2nd action\" url= \"a2u1\"></link><link name=\"l2\"><action label=\"a3 last action\" url=\"u3b4\"></link></graph>";
		XmlReader x = new XmlReader(s, new Vertexes());
		int i = 0;
		while (i++ < 100000) {
		}

	}
}
