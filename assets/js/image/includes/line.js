function Line() {
	var self = this;
	this.drawActive=function(renderer, context, pos){
		context.beginPath();

		context.moveTo(renderer.mouseDownPos().x, renderer.mouseDownPos().y);
		context.lineTo(pos.x, pos.y);
		context.stroke();

	}
	this.name = 'line';
}




