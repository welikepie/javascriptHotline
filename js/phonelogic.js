var access;
		var call = false;
			window.onload = function() {
				var host = "localhost", port = 8887, arduino;
				console.log("testThing");
				// Enable debug output
				BO.enableDebugging = true;
				//11, 10, 9
				arduino = new BO.IOBoard(host, port);
				arduino.addEventListener(BO.IOBoardEvent.READY, function(evt) {
					console.log("ready");
					arduino.setDigitalPinMode(7, BO.Pin.DIN);
					access = new Array(new BO.io.LED(arduino, arduino.getDigitalPin(10)), new BO.io.Button(arduino, arduino.getDigitalPin(7)));
					access[0].blink(250, 0, BO.generators.Oscillator.SIN);
				});
			}
		