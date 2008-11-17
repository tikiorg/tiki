/*
 * Created on May 24, 2004
 * 
 * To change the template for this generated file go to Window - Preferences -
 * Java - Code Generation - Code and Comments
 */
package br.arca.morcego.structure.link;

import java.awt.Graphics;

import br.arca.morcego.Config;
import br.arca.morcego.physics.PunctualBody;
import br.arca.morcego.run.Renderer;
import br.arca.morcego.structure.Link;
import br.arca.morcego.structure.Node;

/**
 * @author lfagundes
 * 
 * To change the template for this generated type comment go to Window -
 * Preferences - Java - Code Generation - Code and Comments
 */
public class SolidLink extends Link {

	/**
	 * @param n1
	 * @param n2
	 */
	public SolidLink(Node n1, Node n2) {
		super(n1, n2);
	}

	public void paint(Graphics g) {
		PunctualBody body1 = node1.getBody();
		PunctualBody body2 = node2.getBody();
			
		g.setColor(
			Renderer.fadeColor(
				Config.getColor(Config.linkColor),
				Math.min(body1.getScale(), body2.getScale())));
		g.drawLine(body1.projection.x, body1.projection.y, 
				   body2.projection.x, body2.projection.y);
	}	
}
