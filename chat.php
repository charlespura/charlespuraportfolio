<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Gemini Chatbot Test</title>
</head>
<body>
  <h1>Chat with Gemini</h1>
  <input type="text" id="userInput" placeholder="Ask me something..." />
  <button onclick="sendMessage()">Send</button>
  <div id="response"></div>

  <script>
    async function sendMessage() {
      const message = document.getElementById('userInput').value;
      if (!message) return;

      const res = await fetch('chatbot.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({message})
      });
      const data = await res.json();

      if(data.reply){
        document.getElementById('response').textContent = 'AI: ' + data.reply;
      } else {
        document.getElementById('response').textContent = 'Error: ' + (data.error || 'Unknown error');
      }
    }
  </script>
</body>
</html>
