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
package br.arca.morcego.physics;

import br.arca.morcego.Config;

/**
 * @author lfagundes
 * 
 * TODO To change the template for this generated type comment go to Window -
 * Preferences - Java - Code Style - Code Templates
 */
public class PunctualBody extends Vector3D {

	private float mass;
	private float charge;

	private Vector3D speed = new Vector3D(0, 0, 0);
	private Vector3D instantForce = new Vector3D(0,0,0);
	
	private boolean positionFixed = false;

	private Spring punctualSpring;
	private float eletrostaticConstant;

	/**
	 * @param x
	 * @param y
	 * @param z
	 */
	public PunctualBody(int x, int y, int z) {
		super(x, y, z);
		construct();
	}

	/**
	 *  
	 */
	public PunctualBody() {
		super();
		construct();
	}
	
	private void construct() {
		mass = Config.getFloat(Config.nodeMass);
		charge = Config.getFloat(Config.nodeCharge);
		eletrostaticConstant = Config.getFloat(Config.eletrostaticConstant);
	}

	/**
	 * @param s
	 */
	public void applyForce() {
		if (!positionFixed) {
			applyUnconditionalForce();
		} else if (punctualSpring != null) {
			instantForce = punctualSpring.strech();
			applyUnconditionalForce();
		}
	}
	
	private void applyUnconditionalForce() {
		instantForce.add(this.friction());
		speed.add(instantForce.multiplyByScalar(1/mass));
		instantForce = new Vector3D(0,0,0);
	}

	public Vector3D friction() {
		float frictionConstant = Config.getFloat(Config.frictionConstant);
		frictionConstant *= mass;
		
		return new Vector3D(speed.x, speed.y, speed.z).multiplyByScalar(-frictionConstant);
	}

	public void releasePosition() {
		positionFixed = false;
		punctualSpring = null;
	}

	public void fixPosition() {
		positionFixed = true;
	}
	
	public void fixPosition(int x, int y, int z) {
		positionFixed = true;
		punctualSpring = new Spring(this, new PunctualBody(x,y,z));
		punctualSpring.setSize(0);
		punctualSpring.setElasticConstant(Config.getFloat(Config.punctualElasticConstant));
	}

	/**
	 *  
	 */
	public void move() {
		moveBy(speed.x, speed.y, speed.z);
	}

	/*
	 * protected void setBounds() { int radius = ((Integer)
	 * Config.getValue(Config.universeRadius)).intValue(); x =
	 * Math.min(Math.max(x, -radius), radius); y = Math.min(Math.max(y,
	 * -radius), radius); z = Math.min(Math.max(z, -radius), radius); }
	 */


	/**
	 * @return Returns the speed.
	 */
	public Vector3D getSpeed() {
		return speed;
	}

	public void rotate(float xTheta, float yTheta) {
		super.rotate(xTheta, yTheta);
		speed.rotate(xTheta, yTheta);
	}

	public Vector3D repel(PunctualBody body) {

		Vector3D force = new Vector3D(x - body.x, y - body.y, z - body.z);

		float distance = force.module();

		force.resize(1 / distance);

		distance /= 25;

		float repelConstant = eletrostaticConstant * this.charge * body.charge;

		force.resize(repelConstant / ((float) Math.pow(distance, 2)));

		//applyForce(force);
		//body.applyForce(force.reverse());
		return force;
	}

	public float getCharge() {
		return charge;
	}
	public void setCharge(float charge) {
		this.charge = charge;
	}
	public float getMass() {
		return mass;
	}
	public void setMass(float mass) {
		this.mass = mass;
	}
	public Vector3D getInstantForce() {
		return instantForce;
	}
	public void setInstantForce(Vector3D instantForce) {
		this.instantForce = instantForce;
	}
}