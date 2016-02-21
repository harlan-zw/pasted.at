function Rectangle() {
	var self = this;
	this.drawActive=function(renderer, context, pos){
		var height = Math.abs(renderer.mouseDownPos().y - pos.y);
		var width = Math.abs(renderer.mouseDownPos().x - pos.x);
		var fromX = renderer.mouseDownPos().x < pos.x ? renderer.mouseDownPos().x : pos.x;
		var fromY = renderer.mouseDownPos().y < pos.y ? renderer.mouseDownPos().y : pos.y;
		context.beginPath();
		context.rect(fromX, fromY, width, height);
		context.stroke();
	}
	this.name = 'rectangle';
}




