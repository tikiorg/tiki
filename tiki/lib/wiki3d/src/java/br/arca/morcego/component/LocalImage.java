/*
 * Morcego - 3D network browser Copyright (C) 2005 Luis Fagundes - Arca
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
package br.arca.morcego.component;

import java.awt.Component;
import java.awt.Graphics;
import java.awt.Image;
import java.awt.Rectangle;
import java.awt.Toolkit;
import java.awt.event.MouseEvent;
import java.awt.image.ImageObserver;
import java.net.URL;

import javax.swing.event.MouseInputListener;

import br.arca.morcego.Config;
import br.arca.morcego.Morcego;

/**
 * @author lfagundes
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class LocalImage extends Component implements MouseInputListener {

	Image image;
	int logoWidth, logoHeight;
	private int x;
	private int y;
	private Rectangle boundRectangle;
	private boolean mouseOver;
	
	/**
	 * 
	 */
	public LocalImage(String name, int x, int y) {
		super();
				
		this.x = x;
		this.y = y;
		
		String imgLocation = Config.getString(Config._imageLocation);
		
		URL location = getClass().getClassLoader().getResource(imgLocation+name);
		
		image = Toolkit.getDefaultToolkit().getImage( location );
	
	
		// Invert is not working. The idea is to have negative x and y for
		// positioning from right bottom corner.
		// invert();
	}
	
	private void invert() {
		if (x < 0) {
			x += Config.getInteger(Config.viewWidth) - image.getWidth(Morcego.getApplication());
		}
		if (y < 0) {
			y += Config.getInteger(Config.viewHeight) - image.getHeight(Morcego.getApplication());
		}
	}
	
	public void paint(Graphics g) {
		g.drawImage(image, x, y, this);
		ImageObserver o = Morcego.getApplication();
		boundRectangle = new Rectangle(x, y, image.getWidth(o), image.getHeight(o));
	}

	/* (non-Javadoc)
	 * @see java.awt.event.MouseListener#mouseClicked(java.awt.event.MouseEvent)
	 */
	public void mouseClicked(MouseEvent e) {
		if (boundRectangle.contains(e.getX(),e.getY())) {
			System.out.println(e.toString());
		}
		// TODO open Arca box
	}

	/* (non-Javadoc)
	 * @see java.awt.event.MouseListener#mouseEntered(java.awt.event.MouseEvent)
	 */
	public void mouseEntered(MouseEvent e) {
		if (boundRectangle != null &&
				boundRectangle.contains(e.getX(), e.getY())) {
			Morcego.setHandCursor();
		}
	}

	/* (non-Javadoc)
	 * @see java.awt.event.MouseListener#mouseExited(java.awt.event.MouseEvent)
	 */
	public void mouseExited(MouseEvent arg0) {
		Morcego.setDefaultCursor();
	}

	/* (non-Javadoc)
	 * @see java.awt.event.MouseListener#mousePressed(java.awt.event.MouseEvent)
	 */
	public void mousePressed(MouseEvent e) {
		if (boundRectangle.contains(e.getX(),e.getY())) {
			System.out.println(e.toString());
		}
	}

	/* (non-Javadoc)
	 * @see java.awt.event.MouseListener#mouseReleased(java.awt.event.MouseEvent)
	 */
	public void mouseReleased(MouseEvent arg0) {
	}

	/* (non-Javadoc)
	 * @see java.awt.event.MouseMotionListener#mouseDragged(java.awt.event.MouseEvent)
	 */
	public void mouseDragged(MouseEvent arg0) {
	}

	/* (non-Javadoc)
	 * @see java.awt.event.MouseMotionListener#mouseMoved(java.awt.event.MouseEvent)
	 */
	public void mouseMoved(MouseEvent e) {
		if (mouseOver) {
			if (!boundRectangle.contains(e.getX(), e.getY())) {
				mouseExited(e);
				mouseOver=false;
			}
		} else {
			if (boundRectangle.contains(e.getX(), e.getY())) {
				mouseEntered(e);
				mouseOver=true;
			}
		}
	}
}
