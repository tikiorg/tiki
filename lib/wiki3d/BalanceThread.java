package wiki3d;
import java.util.*;
class BalanceThread implements Runnable {
	boolean animating = false;

	Vector v = new Vector();
	Can3d c;
	BalanceThread(Can3d c) {
		
		this.c = c;

	}
	public void stopanimate() {

		animating = false;
		
	}
	public void animate() {
		animating = true;

	}
	public void add(Node vr) {

		v.addElement(vr);
		Node.setBalancer(this);
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
				if (i++ % 5 == 0) {
					
					e = this.c.vertexes.elements();
					while (e.hasMoreElements()) {
						Node node = (Node) e.nextElement();
						node.clearSpeed();
					}

					for (int j = 0; j < this.c.vertexes.count; j++) {
						Node node1 =
							(Node) this.c.vertexes.elementAt(j);
						for (int k = j + 1; k < this.c.vertexes.count; k++) {
							Node node2 =
								(Node) this.c.vertexes.elementAt(k);
							SpeedVector sp = node1.getForceFromNode(node2);
							node1.addSpeed(sp);
							node2.addSpeed(sp.reverse());
						}
					}
				}
				Enumeration en = this.c.vertexes.elements();
				while (en.hasMoreElements()) {
					Node node = (Node) en.nextElement();
					node.balance();
				}
				
				this.c.repaint();
				Thread.sleep(40);
			}
		} catch (InterruptedException e) {
		}

	}
}
