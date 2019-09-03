
//var PrintFlags = {
//    A4: 0x0000,
//    A3: 0x0001,
//    A2: 0x0002,
//    A1: 0x0004,
//    TablePlan: 0x0008,
//    GuestList: 0x0010,
//    Landscape: 0x0020
//};

function PrintPlan() {

    // hide arrows
    //for (var tableID in tablePlan.Tables) {
    //    tablePlan.Tables[tableID].ShowControls(false);
    //}
    //for (var objectID in tablePlan.RectObjects) {
    //    tablePlan.RectObjects[objectID].ShowControls(false);
    //}
    //kineticLayer.draw();


    //var tmp_canvas = document.getElementsByTagName('canvas')[0];
    //var imageData = canvasToImage(tmp_canvas, '#FFFFFF');

    //for (var tableID in tablePlan.Tables) {
    //    tablePlan.Tables[tableID].ShowControls(true);
    //}
    //for (var objectID in tablePlan.RectObjects) {
    //    tablePlan.RectObjects[objectID].ShowControls(true);
    //}
    //kineticLayer.draw();

    var printParams = parseInt($('#printType')[0].value) | parseInt($('#printSize')[0].value);// | parseInt($('#printOrient')[0].value);

    if ($("#inputPrintTables")[0].checked) {
        printParams = printParams | 64;
    }
    if ($("#inputPrintSeats")[0].checked) {
        printParams = printParams | 128;
    }
    if ($("#inputPrintColor")[0].checked) {
        printParams = printParams | 256;
    }
    if ($("#inputPrintMenu")[0].checked) {
        printParams = printParams | 512;
    }


    PrintCurrentPlan(printParams);
    HideSubMenu('m11');
}


function canvasToImage(canvas, backgroundColor) {
    //cache height and width        
    var w = canvas.width;
    var h = canvas.height;

    var data;
    var context = canvas.getContext('2d');

    if (backgroundColor) {
        //get the current ImageData for the canvas.
        data = context.getImageData(0, 0, w, h);

        //store the current globalCompositeOperation
        var compositeOperation = context.globalCompositeOperation;

        //set to draw behind current content
        context.globalCompositeOperation = "destination-over";

        //set background color
        context.fillStyle = backgroundColor;

        //draw background / rect on entire canvas
        context.fillRect(0, 0, w, h);
    }

    //get the image data from the canvas
    var imageData = canvas.toDataURL("image/jpeg");

    if (backgroundColor) {
        //clear the canvas
        context.clearRect(0, 0, w, h);

        //restore it with original / cached ImageData
        context.putImageData(data, 0, 0);

        //reset the globalCompositeOperation to what it was
        context.globalCompositeOperation = compositeOperation;
    }

    //return the Base64 encoded data url string
    return imageData;
}