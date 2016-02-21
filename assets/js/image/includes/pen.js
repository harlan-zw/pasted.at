function Pen() {
	var points = [];
	var self = this;
	this.drawActive=function(renderer, context, pos){
		if (points.length == 0) {
			points.push(renderer.mouseDownPos());
		}
		if (typeof pos !== 'undefined')
			points.push(pos);
		drawPoints(context);
	}
	this.reset=function() {
		points = [];
	}
	this.name = 'pen';

	function drawPoints(context) {

		context.beginPath();

		context.moveTo(points[0].x, points[0].y);

		for (i = 1; i < points.length - 2; i++) {
			var c = (points[i].x + points[i + 1].x) / 2,
			d = (points[i].y + points[i + 1].y) / 2;
			context.quadraticCurveTo(points[i].x, points[i].y, c, d)
		}
		context.quadraticCurveTo(points[i].x, points[i].y, points[i + 1].x, points[i + 1].y);

		context.stroke()
	}
}




