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
package br.arca.morcego.run;

import java.awt.Color;

import br.arca.morcego.Config;
import br.arca.morcego.Morcego;

/**
 * @author lfagundes
 * 
 * Thread that keeps rendering graph in screen
 */
public class Renderer implements Runnable {

	private Morcego applet;

	private boolean render = true;

	public Renderer(Morcego applet) {
		this.applet = applet;
	}

	public void render() {
		render = true;
	}

	public static Color fadeColor(Color color, float scale) {
		if (scale > 1) {
			scale = 1;
		}
		if (scale < 0.1f) {
			scale = 0.1f;
		}

		if (color == null) {
			System.out.println("Color is null!!");
			return new Color(0,0,0);
		} else {
			int red = (int) (scale * color.getRed() + (1 - scale)
					*  Config.getColor(Config.backgroundColor).getRed());
			int green = (int) (scale * color.getGreen() + (1 - scale)
					* Config.getColor(Config.backgroundColor).getGreen());
			int blue = (int) (scale * color.getBlue() + (1 - scale)
					* Config.getColor(Config.backgroundColor).getBlue());
			return new Color(red, green, blue);
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see java.lang.Runnable#run()
	 */
	public void run() {

		while (true) {
			if (render) {
				synchronized (this) {
					applet.repaint();
					render = false;
				}
			}
			try {
				Thread.sleep(Config.getInteger(Config.renderingFrameInterval));
			} catch (InterruptedException e) {
				// Keep rendering
			}

		}

	}

}