/*
 * Created on May 24, 2004
 *
 * To change the template for this generated file go to
 * Window - Preferences - Java - Code Generation - Code and Comments
 */
package br.arca.morcego.structure;

import java.awt.event.MouseEvent;
import java.util.Hashtable;

import javax.swing.event.MouseInputListener;

import br.arca.morcego.physics.VisibleObject;



/**
 * @author lfagundes
 *
 * To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Generation - Code and Comments
 */
public abstract class GraphElement implements VisibleObject, MouseInputListener{
	
	private Hashtable properties;
	protected Graph graph;
	
	public GraphElement() {
		properties = new Hashtable();
	}
	
	public Object getProperty(String name) {
		return properties.get(name);
	}
	
	public void setProperty(String name, Object value) {
		properties.put(name, value);
	}
	
	public Hashtable getProperties() {
		return properties;
	}
	public void setProperties(Hashtable properties) {
		this.properties = properties;
	}
	public void setGraph(Graph g) {
		graph = g;
	}
	
	public float getDepth() {
		// TODO I don't know how to tell java that subclass must implement this
		return 0;
	}

	/**
	 * @param e
	 * @return
	 */
	public boolean contains(MouseEvent e) {
		// TODO Auto-generated method stub
		return false;
	}

}
