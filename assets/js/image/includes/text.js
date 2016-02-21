function Text() {
	var self = this;
	var pos;
	var text = "";
	var dim;
	var blinkOn = true;
	var blinkingEvent = false;
	var resizeSelected = 'auto';
	var textInput = '';
	var carretPos = -1;
	var carretIndex = -1;
	var dragCarretIndex = -1;
	var dragCarretPos = -1;
	var carretDragStart;

	var tempMousePos = {
		x: 0,
		y: 0
	}
	this.reset=function() {
		tempMousePos.x = tempMousePos.y = 0;
	}
	this.onKeyPress=function(renderer, key) {
		var context = renderer.getTextContext();
		console.log('text.js: ' + key);
		if (key == 'backspace') {
			console.log(carretIndex);
			if (carretIndex > 0) {
				var result = textInput.split('');
				result.splice(carretIndex -1 , 1);
				textInput = result.join('');
				carretIndex--;
				var newPos = context.measureText(textInput.substring(0, carretIndex)).width;
				carretPos = pos.x + newPos + 5;
			}
		} else {
			var newInput = textInput + key;
			var width = context.measureText(newInput).width;
			if (carretIndex == -1) {
				carretPos = pos.x + width + 5;
				console.log('caret pos: ' +carretPos + 'pos:x' + pos.x + ' width: '+ width);
				textInput += key;
				carretIndex += 2;
			} else {
				console.log('width: ' + width);
				textInput = textInput.splice(carretIndex, 0, key);
				var newPos = context.measureText(textInput.substring(0, carretIndex) + key).width;
				carretPos = pos.x + newPos + 5;
				carretIndex++;

			}
		}
		console.log('carret pos: ' + carretPos);
		renderer.getCropContext().clearRect(0, 0, 700, 500);
		self.draw(renderer);
	}
	this.drawActive=function(renderer, cntx, ps){
		var context = renderer.getTextContext();
		if (tempMousePos.x == 0 && tempMousePos.y == 0) {
			tempMousePos = renderer.mouseDownPos();
		}
		var xDiff = ps.x - tempMousePos.x;
		var yDiff = ps.y - tempMousePos.y;
		console.log('diff: ' + xDiff + ',' + yDiff);
		switch(resizeSelected) {
			case 'all-scroll':
			console.log(xDiff + " , " + yDiff);
			tempMousePos = ps;
			pos.x += xDiff;
			dim.x += xDiff;
			pos.y += yDiff;
			dim.y += yDiff;
			carretPos += xDiff;
			break;
			case 'text':
			if (Math.abs(xDiff) > 0) {
				console.log('drag!');
				// they are dragging ! 
				var endInfo = findCarretInfo(context, ps);
				dragCarretPos = endInfo.x;
				dragCarretIndex = endInfo.i;

				var startInfo = findCarretInfo(context, tempMousePos);
				carretPos = startInfo.x;
				carretIndex = startInfo.i;

			} else {
				dragCarretIndex = -1;
				dragCarretPos = -1;
				console.log('no drag!');
			//inside box
			//find carret position and move it
			//loop through each character group and check if position is greater then group
			var info = findCarretInfo(context, ps);
			carretPos = info.x;
			carretIndex = info.i;
		}
		break;
		default:
		return;
	}

	renderer.getCropContext().clearRect(0, 0, 700, 500);
	self.draw(renderer);
}
function findCarretInfo(context, msPos) {
	var xPos = -1;
	var index = -1;
	for (var i = 0; i <= textInput.length; i++) {
		var text = textInput.substring(0, i);
		var width = context.measureText(text).width;
		if (msPos.x > pos.x + width) {
			xPos = pos.x + width + 5;
			index = i;
		}
	}
	return { x: xPos, i: index };
}
this.mouseMove=function(msPos) {
	if (!insideBox(msPos)) {
		$('#text_image_zone').css('cursor',  resizeSelected = 'all-scroll');
	} else {
		$('#text_image_zone').css('cursor', resizeSelected = 'text');
	}

}
function insideBox(msPos) {
	return msPos.x > pos.x && msPos.x < dim.x && msPos.y > pos.y && msPos.y < dim.y
}
this.onSelect=function(renderer, context) {
	$('#crop_image_zone').css('display', 'none');
	$('#text_image_zone').css('display', 'visible');
	var end = { x: 300, y: 150};
	carretPos = 100 + 10;
	var start = { 
		x: 100,
		y: 100 
	};

	pos = start;
	dim = end;

	self.draw(renderer);

	blinkingEvent = setInterval(function() {
		self.draw(renderer);
	}, 500);

}
this.draw=function(renderer) {

	var context = renderer.getTextContext();
	context.clearRect(0,0, 700, 500);
	var textWidth = context.measureText(textInput).width;
	var boxWidth = dim.x - pos.x;
	if (textWidth > boxWidth) {
		dim.x = pos.x + textWidth + 30;
	} else if (boxWidth > textWidth && boxWidth > 200) {
		dim.x = pos.x + textWidth + 30
	}
	context.strokeStyle = "blue";
	context.font = "40px Arial";
	context.textAlign = 'left';
	context.beginPath();
	context.rect(pos.x, pos.y, dim.x - pos.x, dim.y - pos.y);
	context.stroke();
	context.fillStyle = "blue";

	context.beginPath();

	if (dragCarretPos !== -1) {
		context.rect(dragCarretPos, pos.y+ 5, carretPos - dragCarretPos, dim.y - pos.y - 10);
	}
	context.fill();
	context.fillStyle = "black";

	context.strokeStyle = "black";


		//context.beginPath();
		//context.fillText('bla', pos.x + 20, pos.y + 20);​
		//context.stroke();

		if (blinkOn) {
			context.beginPath();
			//context.fillText('bla', 20, 20);​
			context.moveTo(carretPos, pos.y + 5);
			context.lineTo(carretPos, dim.y - 5);

			context.stroke();
		}
		context.fillText(textInput, pos.x + 5, pos.y + 35);

		blinkOn = !blinkOn;


	}
	this.name = 'text';
}