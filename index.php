<?php
include 'backend/Twilio/Services/Twilio/Capability.php';

// put your Twilio API credentials here
$accountSid = 'AC8b68172a95b9232ee35fcf4f78ec3f10';
$authToken = '2e88b6466fffd55aeeed8cd319ad7b61';

// put your Twilio Application Sid here
$appSid = 'APce3667629e7078f7bfb6e375b84097da';

// put your default Twilio Client name here
$clientName = 'WeLikePie';

// get the Twilio Client name from the page request parameters, if given
if (isset($_REQUEST['client'])) {
	$clientName = $_REQUEST['client'];
}

$capability = new Services_Twilio_Capability($accountSid, $authToken);
$capability -> allowClientOutgoing($appSid);
$capability -> allowClientIncoming($clientName);
$token = $capability -> generateToken();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Hello Client Monkey 6</title>
		<script src="js/Breakout.js" type="text/javascript"></script>
		<script src="js/phonelogic.js" type="text/javascript"></script>
		<script type="text/javascript"
		src="//static.twilio.com/libs/twiliojs/1.1/twilio.min.js"></script>
		<script type="text/javascript"
		src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css.css">
		<script type="text/javascript">
			var access = new Array();
			window.onload = function() {
		
		
			var call = false;
	
			var host = "localhost"; 
			var port = 8887;
			var arduino = new BO.IOBoard(host,port);
			console.log("testThing");
			// Enable debug output
			BO.enableDebugging = true;
			//11, 10, 9
	
			arduino.addEventListener(BO.IOBoardEvent.READY, function(evt) {
				//pin 3 and 5 are buzzers.
				console.log("ready");
				arduino.setDigitalPinMode(7, BO.Pin.DIN);	
				arduino.setDigitalPinMode(6, BO.Pin.PWM);		
				arduino.enableAnalogPin(0);

				
				access = new Array(new BO.io.LED(arduino, arduino.getDigitalPin(10)), 
				new BO.io.Button(arduino, arduino.getDigitalPin(7)),
				arduino.getAnalogPin(0), arduino.getDigitalPin(6));
				
				access[0].blink(250, 0, BO.generators.Oscillator.SIN);
				
				access[1].addEventListener(BO.io.ButtonEvent.RELEASE, function(evt) {
					call = !call;
					console.log(call);
					if(call){dial();}else{hangup();}
				
				});
				
			    access[2].addEventListener(BO.PinEvent.CHANGE,function(evt){
			    	console.log(evt.target.value);
			    	access[3].value = evt.target.value;
			    })
			    
			    
			
	
			Twilio.Device.setup("<?php echo $token; ?>");
	
			
			});
			}
		
				
							
				Twilio.Device.ready(function (device) {
					access[0].stopBlinking();
					access[0].off();
					$("#log").text("Client '<?php echo $clientName ?>' is ready");
					console.log("doing");
				});
	
				Twilio.Device.error(function (error) {
					access[0].blink(500, 0, BO.generators.Oscillator.SIN);
					$("#log").text("Error: " + error.message);
				});
	
				Twilio.Device.connect(function (conn) {
					$("#log").text("Successfully established call");
					access[0].blink(10000, 0, BO.generators.Oscillator.SIN);
				});
	
				Twilio.Device.disconnect(function (conn) {
					$("#log").text("Call ended");
					access[0].stopBlinking();
					access[0].on();
				});
				 Twilio.Device.cancel(function(conn) {
			       $("#log").text("Call Canceled");
					access[0].stopBlinking();
					access[0].on();
			    });
			    
				  Twilio.Device.offline(function() {
        // Called on network connection lost.
 					access[0].blink(100,0,BO.generators.Oscillator.SIN);
    				});
    				
				Twilio.Device.incoming(function (conn) {
					$("#log").text("Incoming connection from " + conn.parameters.From);
					// accept the incoming connection and start two-way audio
					conn.accept();
				});
	
				Twilio.Device.presence(function (pres) {
					if (pres.available) {
						// create an item for the client that became available
						$("<li>", {id: pres.from, text: pres.from}).click(function () {
							$("#number").val(pres.from);
							dial();
						}).prependTo("#people");
					}
					else {
						// find the item by client name and remove it
						$("#" + pres.from).remove();
					}
				});
				
				function dial() {
					console.log($("#number").val());
					// get the phone number or client to connect the call to
					params = {"PhoneNumber": $("#number").val()};
					Twilio.Device.connect(params);
					access[0].blink(1000, 0, BO.generators.Oscillator.SIN);
				}
	
				function hangup() {
					console.log("hanging up");
					Twilio.Device.disconnectAll();
				}
	
				
				
		</script>

	</head>
	<body>
		<button class="call" onclick="dial();">
			Call
		</button>

		<button class="hangup" onclick="hangup();">
			Hangup
		</button>

		<input type="text" id="number" value="+447838952793" name="number" placeholder="Enter a phone number or client to call"/>

		<div id="log">
			Loading pigeons...
		</div>

		<ul id="people"/>
	</body>
</html>
