<!DOCTYPE html>
<html lang="en">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<script>
        // AJAX Script
				function showStatus(id,num) {
					if (id == "" && num=="")
					{
						return;
					}
					else
					{
						if (window.XMLHttpRequest)
						{
							xmlhttp = new XMLHttpRequest();
						}

						xmlhttp.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								document.getElementById("statusOf"+id).innerHTML = this.responseText;
							}
						};
						xmlhttp.open("GET","engagement-verify.php?engagement_id="+id+"&status="+num,true);
						xmlhttp.send();
					}
				}



		</script>
		<script src="../assets/quaggaJS/quagga.min.js"></script>
		<script>
		Quagga.init({
		    inputStream : {
		        name : "Live",
		        type : "LiveStream",
		         // Or '#yourElement' (optional)
		        target: document.querySelector('#yourElement')
		    },
		    decoder : {
		        readers : ["code_39_reader"]
		    }
		}, function(err) {
		    if (err) {
		        console.log(err);
		        return
		    }
		    console.log("Initialization finished. Ready to start");
		    Quagga.start();
		});
		</script>
    <style>
        /* In order to place the tracking correctly */
        canvas.drawing, canvas.drawingBuffer {
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>
</head>

<body>
    <!-- Div to show the scanner -->
    <div id="scanner-container"></div>
    <input type="button" id="btn" value="Start/Stop the scanner" />

    <!-- Include the image-diff library -->
    <script src="../assets/quaggaJS/quagga.min.js"></script>

    <script>
        var _scannerIsRunning = false;

        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#scanner-container'),
                    constraints: {
                        width: window.innerWidth,
                        height: window.innerHeight,
                        facingMode: "environment"
                    },
                },
                decoder: {
                    readers: [
                        "code_128_reader",
                        "ean_reader",
                        "ean_8_reader",
                        "code_39_reader",
                        "code_39_vin_reader",
                        "codabar_reader",
                        "upc_reader",
                        "upc_e_reader",
                        "i2of5_reader"
                    ],
                    debug: {
                        showCanvas: true,
                        showPatches: true,
                        showFoundPatches: true,
                        showSkeleton: true,
                        showLabels: true,
                        showPatchLabels: true,
                        showRemainingPatchLabels: true,
                        boxFromPatches: {
                            showTransformed: true,
                            showTransformedBox: true,
                            showBB: true
                        }
                    }
                },

            }, function (err) {
                if (err) {
                    console.log(err);
                    return
                }

                console.log("Initialization finished. Ready to start");
                Quagga.start();

                // Set flag to is running
                _scannerIsRunning = true;
            });

            Quagga.onProcessed(function (result) {
                var drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;

                if (result) {
                    if (result.boxes) {
                        drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                        result.boxes.filter(function (box) {
                            return box !== result.box;
                        }).forEach(function (box) {
                            Quagga.ImageDebug.drawPath(box, { x: 0, y: 1 }, drawingCtx, { color: "green", lineWidth: 2 });
                        });
                    }

                    if (result.box) {
                        Quagga.ImageDebug.drawPath(result.box, { x: 0, y: 1 }, drawingCtx, { color: "#00F", lineWidth: 2 });
                    }

                    if (result.codeResult && result.codeResult.code) {
                        Quagga.ImageDebug.drawPath(result.line, { x: 'x', y: 'y' }, drawingCtx, { color: 'red', lineWidth: 3 });
                    }
                }
            });


            Quagga.onDetected(function (result) {
                console.log("Barcode detected and processed : [" + result.codeResult.code + "]", result);

            });
        }


        // Start/stop scanner
        document.getElementById("btn").addEventListener("click", function () {
            if (_scannerIsRunning) {
                Quagga.stop();
            } else {
                startScanner();
            }
        }, false);
    </script>
</body>

</html>
