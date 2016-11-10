<html>
  <head>
    <script type="text/javascript">
      // Your Client ID can be retrieved from your project in the Google
      // Developer Console, https://console.developers.google.com
      var CLIENT_ID = '353367700880-regrfjt5gdpj1oc89sbq78jbrblq4lfa.apps.googleusercontent.com';

      var SCOPES = ['https://www.googleapis.com/auth/drive.file'];

      /**
       * Check if current user has authorized this application.
       */
      function checkAuth() {
        gapi.auth.authorize(
          {
            'client_id': CLIENT_ID,
            'scope': SCOPES,
            'immediate': true
          }, handleAuthResult);
      }
      /**
       * Handle response from authorization server.
       *
       * @param {Object} authResult Authorization result.
       */
      function handleAuthResult(authResult) {
        var authorizeDiv = document.getElementById('authorize-div');
        if (authResult && !authResult.error) {
          // Hide auth UI, then load client library.
          authorizeDiv.style.display = 'none';
          loadDriveApi();
        } else {
          // Show auth UI, allowing the user to initiate authorization by
          // clicking authorize button.
          authorizeDiv.style.display = 'inline';
        }
      }
      /**
       * Initiate auth flow in response to user clicking authorize button.
       *
       * @param {Event} event Button click event.
       */
      function handleAuthClick(event) {
        gapi.auth.authorize(
          {client_id: CLIENT_ID, scope: SCOPES, immediate: false},
          handleAuthResult);
        return false;
      }
      /**
       * Load Drive API client library.
       */
      function loadDriveApi() {
        gapi.client.load('drive', 'v2', listFiles);
      }
      /**
       * Print files.
       */
      function listFiles() {
        var request = gapi.client.drive.files.list({
            'maxResults': 10
          });
          request.execute(function(resp) {
            //appendPre('Files:');
            var files = resp.items;
            if (files && files.length > 0) {
              for (var i = 0; i < files.length; i++) {
                var file = files[i];
                appendPre(file.title,file.webContentLink);
                //appendPre2(file);
              }
            } else {
             //appendPre('No files found.');
            }
          });
      }

      /**
       * Append a pre element to the body containing the given message
       * as its text node.
       *
       * @param {string} message Text to be placed in pre element.
       */
      function appendPre(message, download) {
        var pre = document.getElementById('output');
        var textContent = document.createTextNode(message);
        var miDiv = document.createElement("div");
        var miLink = document.createElement("a");
        miLink.href=download;
        miLink.appendChild(textContent);
        miDiv.appendChild(miLink);
		pre.appendChild(miDiv);
        
        //pre.appendChild(textContent);
      }
      /**
       * Append a pre element to the body containing the given message
       * as its text node.
       *
       * @param {string} message Text to be placed in pre element.
       */
      function appendPre2(message) {
        var pre = document.getElementById('output2');
        salida ="";
        for (var p in message) {
			salida += p + ': ' + message[p] + '\n';
		}
        
        
        var textContent = document.createTextNode(salida);
		pre.appendChild(textContent);
      }
    </script>
    <script src="https://apis.google.com/js/client.js?onload=checkAuth">
    </script>
  </head>
  <body>
    <div id="authorize-div" style="display: none">
      <span>Authorize access to Drive API</span>
      <!--Button for the user to click to initiate auth sequence -->
      <button id="authorize-button" onclick="handleAuthClick(event)">
        Authorize
      </button>
    </div>
    <div id="output"></div>
    <pre id="output2"></pre>
  </body>
</html>