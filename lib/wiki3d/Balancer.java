package wiki3d;
import java.util.*;
class Balancer implements Runnable {
	boolean animating = false;

	Vector v = new Vector();
	Morcego c; 
	Balancer(Morcego c) {

		this.c = c;

	}

	public void add(Node vr) {
		v.addElement(vr);		
	}
	
	public void remove(Node vr) {

		v.removeElement(vr);

	}

	public void run() {

		Thread.currentThread().setPriority(Thread.MIN_PRIORITY);

		int i = 0;

		try {

			while (true) {

				Enumeration e;
				if (i++ % 3 == 0) {

					e = this.c.graph.elements();
					while (e.hasMoreElements()) {
						Node node = (Node) e.nextElement();
						node.clearSpeed();
					}

					for (int j = 0; j < this.c.graph.size(); j++) {
						Node node1 = (Node) this.c.graph.elementAt(j);
						for (int k = j + 1; k < this.c.graph.size(); k++) {
							Node node2 = (Node) this.c.graph.elementAt(k);
							SpeedVector sp = node1.getForceFromNode(node2);
							node1.addSpeed(sp);
							node2.addSpeed(sp.reverse());
						}
					}
				}
				Enumeration en = this.c.graph.elements();
				while (en.hasMoreElements()) {
					Node node = (Node) en.nextElement();
					node.balance();
				}
				
				this.c.graph.transform();
				this.c.repaint();

				Thread.sleep(40);
			}
		} catch (InterruptedException e) {
			System.out.println("Balancing interrupted");
		}

	}
}
