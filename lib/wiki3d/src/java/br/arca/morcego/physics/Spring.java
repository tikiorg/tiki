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
package br.arca.morcego.physics;

import br.arca.morcego.Config;


/**
 * @author lfagundes
 *
 */
public class Spring {
	
	private float size;
	private float elasticConstant;

	protected PunctualBody body1, body2;
	
	/**
	 * 
	 */
	public Spring(PunctualBody b1, PunctualBody b2) {
		super();
		body1 = b1;
		body2 = b2;
		
		size = Config.getFloat(Config.springSize);
		elasticConstant = Config.getFloat(Config.elasticConstant);
	}

	public Vector3D strech() {
		Vector3D force = new Vector3D(body1.x - body2.x,
									  body1.y - body2.y,
									  body1.z - body2.z);
		
		float distance = force.module();
		
		if (distance > 0) {
			force.resize(1/distance);
		}
		
		force.resize((distance-this.size) * this.elasticConstant);
		
		//body2.applyForce(force);
		//body1.applyForce(force.reverse());
		return force.opposite();
	}

	public float getElasticConstant() {
		return elasticConstant;
	}
	public void setElasticConstant(float elasticConstant) {
		this.elasticConstant = elasticConstant;
	}
	public float getSize() {
		return size;
	}
	public void setSize(float size) {
		this.size = size;
	}
}

