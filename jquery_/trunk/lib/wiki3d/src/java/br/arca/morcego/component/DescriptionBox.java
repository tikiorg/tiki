/*
 * Created on May 23, 2004
 * 
 * To change the template for this generated file go to Window - Preferences -
 * Java - Code Generation - Code and Comments
 */
package br.arca.morcego.component;

import java.awt.Component;
import java.awt.Font;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Rectangle;
import java.awt.TextArea;
import java.awt.font.FontRenderContext;
import java.awt.font.TextLayout;
import java.awt.geom.Rectangle2D;

import br.arca.morcego.Config;


/**
 * @author lfagundes
 * 
 * To change the template for this generated type comment go to Window -
 * Preferences - Java - Code Generation - Code and Comments
 */
public class DescriptionBox extends Component {


	private Rectangle rectangle;
	private String text;
	private int originX;
	private int originY;

	public DescriptionBox(String text) {
		this.text = text;
		
		originX = 0;
		originY = 0;
	}

	public void setPosition(int x, int y) {
		originX = x;
		originY = y;
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see br.arca.morcego.Component#paint(java.awt.Graphics)
	 */
	public void paint(Graphics g) {
		// TODO: paint a fancy textbox with description of node
		
		Graphics2D graphic = (Graphics2D) g;
		
		TextArea textArea = new TextArea(text);
		
		textArea.setLocation(0,0);
		textArea.setVisible(true);
		
		Font font = new Font(null, Font.PLAIN, 10);

		FontRenderContext frc = new FontRenderContext(null, false, false);

		TextLayout l = new TextLayout(text, font, frc);

		Rectangle2D textBounds = l.getBounds();

		int margin = Config.getInteger(Config.descriptionMargin);
		int border = 1;
		int distX = 5;
		int distY = 5;
		
		int width = (int) textBounds.getWidth() + 2*margin + 2*border;
		int height = (int) textBounds.getHeight() + 2*margin + 2*border;
		
		int cornerX = originX - width - distX;
		int cornerY = originY - height - distY;
		
		if (cornerX < 0) {
			cornerX = 0;
		}
		if (cornerY < 0) {
			cornerY = 0;
		}
		
		graphic.setColor(Config.getColor(Config.descriptionBorder));
		graphic.fillRect(
				cornerX - margin - border,
				cornerY - margin - border,
				(int) textBounds.getWidth() + 2*margin + 2*border,
				(int) textBounds.getHeight() + 2*margin + 2*border);
		
		graphic.setColor(Config.getColor(Config.descriptionBackground));

		graphic.fillRect(
			cornerX - margin,
			cornerY - margin,
			(int) textBounds.getWidth() + 2*margin,
			(int) textBounds.getHeight() + 2*margin);

		graphic.setColor(Config.getColor(Config.descriptionColor));
		textArea.paint(g);

		l.draw(graphic, cornerX, cornerY +(int)textBounds.getHeight()); 
	}



}
