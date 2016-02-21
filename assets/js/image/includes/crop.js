function Crop() {
	var self = this;
	var pos;
	var dim;
	var corners = [];
	var resizeSelected = 'auto';
	var tempMousePos = {
		x: 0,
		y: 0
	}
	var cornerDimension = {
		width: 14,
		height: 14
	}

	this.drawActive=function(renderer, context, ps) {
		console.log(resizeSelected);
		switch(resizeSelected) {
			case 'nw-resize':
			pos = ps;
			break;
			case 'se-resize':
			dim = ps;
			break;
			case 'sw-resize':
			pos.x = ps.x;
			dim.y = ps.y;
			break;
			case 'ne-resize':
			dim.x = ps.x;
			pos.y = ps.y;
			break;
			case 'e-resize':
			pos.x = ps.x;
			break;
			case 'w-resize':
			dim.x = ps.x;
			break;
			case 'n-resize':
			pos.y = ps.y;
			break;
			case 's-resize':
			dim.y = ps.y;
			break;
			case 'all-scroll':
			if (tempMousePos.x == 0 && tempMousePos.y == 0) {
				tempMousePos = renderer.mouseDownPos();
			}
			var xDiff = ps.x - tempMousePos.x;
			var yDiff = ps.y - tempMousePos.y;
			console.log(xDiff + " , " + yDiff);
			tempMousePos = ps;
			pos.x += xDiff;
			dim.x += xDiff;
			pos.y += yDiff;
			dim.y += yDiff;
			break;
		}
		renderer.getCropContext().clearRect(0, 0, $('#crop_image_zone').width(), $('#crop_image_zone').height());
		self.draw(renderer);
	}
	this.reset=function() {
		tempMousePos.x = tempMousePos.y = 0;
	}
	this.onSelect=function(renderer, context) {
		$('#crop_image_zone').css('display', 'visible');
		$('#text_image_zone').css('display', 'none');

		var cropContext = renderer.getCropContext();
		var dimensions = renderer.getCanvasDimensions();
		var offset = { 
			x: dimensions.x / 10,
			y: dimensions.y / 10
		}
		var end = {
			x: dimensions.x - offset.x,
			y: dimensions.y - offset.y
		};

		pos = offset;
		dim = end;

		self.draw(renderer);


		/* draw a opaque black background over entire screen except for bound box */
		/*var pixelBuffer = cropContext.createImageData(700, 500);
		var pixelCount = pixelBuffer.data.length / 4;
		console.log(pixelCount);
		console.log(end.x + " , " + end.y);
		for (var i =0, pixelIndex = 0; i < pixelBuffer.data.length; i+=4, pixelIndex++) {
			var x = Math.round(pixelIndex % pixelBuffer.width);
			var y = Math.round(pixelIndex / pixelBuffer.height);
			pixelBuffer.data[i+0] = 0;//red
			pixelBuffer.data[i+1] = 0;//green
			pixelBuffer.data[i+2] = 0;//blue
			pixelBuffer.data[i+3] = inRec(x, y) ? 50 : 0;//opacity
		}*/

		//cropContext.putImageData(pixelBuffer, 0, 0);

		
	}

	this.mouseMove=function(msPos) {
		/* check our corners */
		for (var i = 0; i < corners.length; i++) {
			/* if mouse pos is within corner position */
			if (msPos.x >= corners[i].x && msPos.x <= corners[i].x + cornerDimension.width
				&& msPos.y >= corners[i].y && msPos.y <= corners[i].y + cornerDimension.height) {
				$('#crop_image_zone').css('cursor', resizeSelected = corners[i].cursor);
			return;
		}
	}
	/* check sides */
	if (Math.abs(msPos.x - pos.x - 1) <= 4 && insideYCropZone(msPos)) {
		$('#crop_image_zone').css('cursor', resizeSelected =  'e-resize');
	} else if (Math.abs(msPos.x - dim.x + 2) <= 4  && insideYCropZone(msPos)) {
		$('#crop_image_zone').css('cursor', resizeSelected =  'w-resize');
	} else if (Math.abs(msPos.y - pos.y - 1) <= 4 && insideXCropZone(msPos)) {
		$('#crop_image_zone').css('cursor', resizeSelected = 'n-resize');
	} else if (Math.abs(msPos.y - dim.y + 2) <= 4 && insideXCropZone(msPos)) {
		$('#crop_image_zone').css('cursor', resizeSelected = 's-resize');
	} else if (insideCropZone(msPos)) {
		$('#crop_image_zone').css('cursor', resizeSelected = 'all-scroll');
	} else {
		$('#crop_image_zone').css('cursor', resizeSelected = 'auto');
	}
};

function insideCropZone(msPos) {
	return msPos.x > pos.x && msPos.x < dim.x && msPos.y > pos.y && msPos.y < dim.y
}
function insideYCropZone(msPos) {
	return msPos.y >= pos.y && msPos.y <= dim.y;
}
function insideXCropZone(msPos) {
	return msPos.x >= pos.x && msPos.x <= dim.x;
}

this.draw=function(renderer) {
	var cropContext = renderer.getCropContext();
	/* square around thye zone */
		cropContext.strokeStyle = "rgba(255, 255, 255, 0.7)";//white
		cropContext.beginPath();
		cropContext.rect(pos.x -1 , pos.y - 1, dim.x - pos.x + 2, dim.y - pos.y + 2);
		cropContext.stroke();

		cropContext.lineWidth = 1;
		cropContext.strokeStyle = "#FFFFFF";//white
		cropContext.fillStyle = "rgba(0, 0, 0, 0.3)";//black

		/* corner squares */
		cropContext.beginPath();
		cropContext.rect(0, 0,$('#crop_image_zone').width(), $('#crop_image_zone').height());

		corners[0] = {
			x: pos.x - cornerDimension.width/2,
			y: pos.y - cornerDimension.height/2,
			cursor: 'nw-resize'
		};
		corners[1] = {
			x: dim.x - cornerDimension.width/2,
			y: pos.y - cornerDimension.height/2,
			cursor: 'ne-resize'

		};
		corners[2] = {
			x: dim.x - cornerDimension.width/2,
			y: dim.y - cornerDimension.height/2,
			cursor: 'se-resize'

		};
		corners[3] = {
			x: pos.x - cornerDimension.width/2,
			y: dim.y - cornerDimension.height/2,
			cursor: 'sw-resize'

		};
		console.log(corners);
		for (var i = 0; i < corners.length; i++) {
			cropContext.rect(corners[i].x, corners[i].y, cornerDimension.width, cornerDimension.height);
		};
		cropContext.fill();
		cropContext.stroke();


		/* remove the area that is being cleared */
		cropContext.clearRect(pos.x, pos.y, dim.x - pos.x, dim.y - pos.y);
		cropContext.strokeStyle = "rgba(0, 0, 0, 0.3)";//black

		/* draw the grid on the cropped area */
		cropContext.beginPath();
		var segmentX = (dim.x - pos.x) / 3;
		var segmentY = (dim.y - pos.y) / 3;
		console.log('segmnet: ' + segmentX + " , " + segmentY + " pos: " + pos.x + " , " + pos.y);
		for (var i = 1; i <= 2; i++) {
			cropContext.moveTo(pos.x + (segmentX * i), pos.y);
			cropContext.lineTo(pos.x + (segmentX * i), dim.y);

			cropContext.moveTo(pos.x, pos.y + ( segmentY * i));
			cropContext.lineTo(dim.x, pos.y + (segmentY * i));
		}

		cropContext.stroke();
	}
	this.name = 'crop';
}




