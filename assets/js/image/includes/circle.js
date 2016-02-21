function Circle() {
	var self = this;
	this.drawActive=function(renderer, context, pos){
		/* radius is the amount of distance from down to current */
		var radius = getDistance(renderer.mouseDownPos(), pos) / 1.5;
		console.log('dis: ' + radius);
		/* if current position is higher then click pos we draw from current */

		var xStart = (renderer.mouseDownPos().x > pos.x ? pos.x : renderer.mouseDownPos().x) + radius/2;
		var yStart = (renderer.mouseDownPos().y > pos.y ? pos.y : renderer.mouseDownPos().y)  + radius/2;

		context.beginPath();
		context.arc(xStart,yStart,radius,0,2*Math.PI);
		context.stroke();
	}
	this.name = 'circle';

}


function getDistance(pos1, pos2) {
	var x = Math.pow(pos2.x-pos1.x, 2);
	var y = Math.pow(pos2.y-pos1.y, 2);
	return Math.sqrt(x + y);
}


