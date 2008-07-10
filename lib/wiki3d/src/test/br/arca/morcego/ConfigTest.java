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
package br.arca.morcego;

import java.lang.reflect.Field;
import java.util.Enumeration;

import junit.framework.TestCase;

/**
 * @author lfagundes
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class ConfigTest extends TestCase {
	
	public void setUp() throws Exception {
		super.setUp();
		Config.init();		
	}
	
	public void testStoreRetrieve() {
		Object obj = new Object();
		Config.setValue("TestConfig", obj);
		assertEquals( obj, Config.getValue("TestConfig"));
	}
	
	public void testListConfigVars() {
		Enumeration e = Config.listConfigVars();
		int count = 0;
		while (e.hasMoreElements()) {
			count++;
			e.nextElement();
		}
		
		Config.setValue("TestConfigUniqueString", new Object());
		
		e = Config.listConfigVars();
		int newCount = 0;
		boolean foundNew = false;
		while (e.hasMoreElements()) {
			newCount++;
			String key = (String) e.nextElement();
			if (key.equals(new String("TestConfig"))) {
				foundNew = true;
			}
		}
		
		assertEquals("Element count should increase one with new value", newCount, count+1);
		assertTrue("New config var should have been listed", foundNew);
		
	}
	
	public void testConsistency() throws ClassNotFoundException {
		Class configClass = Class.forName("br.arca.morcego.Config");
		Field[] fields = configClass.getDeclaredFields();
		for (int i = 0; i < fields.length; i++) {
			Field field = fields[i];
			try {
				Object obj = Config.getValue((String) field.get(null));
				assertNotNull("All public fields in config must be configuration keys:"+(String) field.get(null), obj);
			} catch (Exception e) {
				// IllegalAccessException and NullPointerException indicates
				// fields are not accessible or not static, so they don't need
				// testing
			}
		}
	}

}
