<?php
include 'backend/Twilio/Services/Twilio/Capability.php';

// put your Twilio API credentials here
$accountSid = 'AC8b68172a95b9232ee35fcf4f78ec3f10';
$authToken  = '2e88b6466fffd55aeeed8cd319ad7b61';

// put your Twilio Application Sid here
$appSid     = 'APce3667629e7078f7bfb6e375b84097da';

// put your default Twilio Client name here
$clientName = 'WeLikePie';

// get the Twilio Client name from the page request parameters, if given
if (isset($_REQUEST['client'])) {
    $clientName = $_REQUEST['client'];
}

$capability = new Services_Twilio_Capability($accountSid, $authToken);
$capability->allowClientOutgoing($appSid);
$capability->allowClientIncoming($clientName);
$token = $capability->generateToken();
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Hello Client Monkey 6</title>
    <script type="text/javascript"
      src="//static.twilio.com/libs/twiliojs/1.1/twilio.min.js"></script>
    <script type="text/javascript"
      src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js">ext/javascript"js/caller.js"
    </script>
    <link rel="stylesheet" type="text/css" href="css.css">
         <script type="text/javascript">

      Twilio.Device.setup("<?php echo $token; ?>");

      Twilio.Device.ready(function (device) {
        $("#log").text("Client '<?php echo $clientName ?>' is ready");
      });

      Twilio.Device.error(function (error) {
        $("#log").text("Error: " + error.message);
      });

      Twilio.Device.connect(function (conn) {
        $("#log").text("Successfully established call");
      });

      Twilio.Device.disconnect(function (conn) {
        $("#log").text("Call ended");
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
            call();
          }).prependTo("#people");
        }
        else {
          // find the item by client name and remove it
          $("#" + pres.from).remove();
        }
      });

      function call() {
        // get the phone number or client to connect the call to
        params = {"PhoneNumber": $("#number").val()};
        Twilio.Device.connect(params);
      }

      function hangup() {
        Twilio.Device.disconnectAll();
      }
    </script>

  </head>
  <body>
    <button class="call" onclick="call();">
      Call
    </button>

    <button class="hangup" onclick="hangup();">
      Hangup
    </button>

    <input type="text" id="number" name="number"
      placeholder="Enter a phone number or client to call"/>

    <div id="log">Loading pigeons...</div>

    <ul id="people"/>
  </body>
</html>
