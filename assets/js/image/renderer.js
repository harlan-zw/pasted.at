function Renderer() {


  var self = this;
  /* initialiazer for all renderers */
/** 
 * Boolean mouseDown is the left mouse button down
 */
 var isMouseDown = false;
  /** 
 * Boolean mouseDown is the left mouse button down
 */
 var isShiftDown = false;
 /**
  * Canvas canvas the object used for drawing on
  */
  var canvas = false;
  /**
   * Context context the context of the canvas object.
   */
   var context;
   var backingContext;
   var cropContext;
   var textContext;
   /* the tool we're using to draw */
   var tool;
   /**
    * Coordinates that the mouse went down at, will be -1 if mouse is not down.
    */
    var msDownX = false, msDownY = false;
    this.mouseDownPos=function() {
      return {
        x: msDownX,
        y: msDownY
      };
    }
    this.setTool=function(t) {
      tool = t;
      console.log('tool set to: ' + t.name);
      if (typeof tool.onSelect !== 'undefined') {
        tool.onSelect(self, backingContext);
       // tool.onSelect(self, context);

     }
   }
   this.getMousePos=function(e) {
    var rect = canvas.getBoundingClientRect();
    return {
      x: e.clientX - rect.left,
      y: e.clientY - rect.top
    };
  }
  this.getCanvasDimensions=function(e) {
    var rect = canvas.getBoundingClientRect();
    console.log(rect);
    return {
      x: rect.width,
      y: rect.height
    };
  }

  this.resetMouseDown=function() {
    msDownX = false;
    msDownY = false;
  }
  this.keyPress=function(e) {
    console.log('key press: '  + e);
    if (typeof tool.onKeyPress !== 'undefined') {
      console.log(e.keyCode);
      if (e == 'backspace') {
        /* for special keys */
        console.log('kp2: ' + e);
        tool.onKeyPress(this, e);
        return;
      }
      var key = String.fromCharCode(e.keyCode);
      tool.onKeyPress(this, key);
    }
  }
  this.mouseDown=function(e){
    var pos = self.getMousePos(e);
    console.log(pos);
    msDownX = pos.x;
    msDownY = pos.y;

    this.isMouseDown = true;

  }


  this.mouseUp=function(e){
   var pos = self.getMousePos(e);

   if (msDownX > 0 || msDownY > 0) {
    /* apply final changes to backing context */
    tool.drawActive(self, backingContext, pos);
    self.clearTempCanvas();
    self.resetMouseDown();
    if (typeof tool.reset !== 'undefined')
      tool.reset();
  }
  this.isMouseDown = false;

}
this.mouseMoved=function(e) {
  var pos = self.getMousePos(e);
  if (this.isMouseDown) {
    self.clearTempCanvas();
    tool.drawActive(self, context, pos);
  }
  if (typeof tool.mouseMove !== 'undefined')
    tool.mouseMove(pos);
}

this.init=function(cnvs, cntxt, bckCntxt, txtContext, crpCntxt) {
  canvas = cnvs;
  context = cntxt;
  backingContext = bckCntxt;
  cropContext = crpCntxt;
  textContext = txtContext;
  console.log('renderer context set to: ' + this.context);
  self.setDrawOption('lineCap', 'round');
  self.setDrawOption('lineJoin', 'round');
  self.setDrawOption('strokeStyle', 'black');
  self.setDrawOption('lineWidth', 1);
}
this.clearTempCanvas=function() {
  console.log('canvas cleared.');
  context.clearRect(0, 0, canvas.width, canvas.height);
  context.drawImage($('#backing_image_zone').get(0), 0, 0);
}

this.shiftDown=function(e){
 this.isShiftDown = true;
}

this.shiftUp=function(e){
 this.isShiftDown = false;
}

this.getContext=function() {
  return context;
}
this.getCanvas=function() {
  return canvas;
}
this.getCropContext=function() {
  return cropContext;
}
this.getTextContext=function() {
  return textContext;
}
this.setDrawOption=function(option, value) {
  context[option] = value;
  backingContext[option] = value;

}

/* give our init function class constructor args */
this.init.apply(this, arguments);
}

