<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>You Found the Hidden Dojo</title>
  <style>
    body {
      margin: 0;
      overflow: hidden;
      font-family: monospace;
      color: #00ff00;
      background-color: black;
      text-align: center;
      padding-top: 50px;
      position: relative;
      z-index: 1;
    }

    h1, p, .pill, .belt {
      position: relative;
      z-index: 2;
    }

    canvas {
      position: fixed;
      top: 0;
      left: 0;
      z-index: 0;
    }

    h1 {
      font-size: 2.5em;
      text-shadow: 0 0 10px #00ff00;
    }

    p {
      font-size: 1.3em;
      margin: 15px 0;
      text-shadow: 0 0 5px #00ff00;
    }

    .glow {
      animation: glow 1.5s infinite alternate;
    }

    @keyframes glow {
      from { text-shadow: 0 0 10px #00ff00; }
      to   { text-shadow: 0 0 20px #00ff00, 0 0 30px #00ff00; }
    }

    .belt {
      margin-top: 30px;
    }

    .belt img {
      width: 200px;
      animation: floaty 3s ease-in-out infinite;
    }

    @keyframes floaty {
      0%, 100% { transform: translateY(0); }
      50%      { transform: translateY(-10px); }
    }

    .pill {
      display: inline-block;
      padding: 12px 30px;
      margin: 20px 10px;
      border-radius: 30px;
      font-size: 1.2em;
      font-family: monospace;
      text-decoration: none;
      box-shadow: 0 0 12px rgba(255,255,255,0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .pill:hover {
      transform: scale(1.1);
      box-shadow: 0 0 20px rgba(255,255,255,0.5);
    }

    .pill.red {
      background-color: #ff2e2e;
      color: white;
      border: 2px solid #ff9999;
    }

    .pill.blue {
      background-color: #2e9bff;
      color: white;
      border: 2px solid #99ccff;
    }
  </style>
</head>
<body>
  <canvas id="matrix"></canvas>

  <h1 class="glow">Welcome to the Hidden Dojo</h1>
  <p>You followed the white belt. Not everyone makes it here...</p>
  <p>“Are you ready to unlock the true secrets of Jiu Jitsu?”</p>
  <p class="glow">You must choose between the Blue Pill or the Red Pill. But there is no turning back!</p>

  <div>
    <a href="#" class="pill red">Red Pill</a>
    <a href="/pages/list.php" class="pill blue">Blue Pill</a>
  </div>

  <div class="belt">
    <img src="https://csusmchronicle.com/wp-content/uploads/2024/09/1727280323433.png" alt="White Belt">
  </div>
  
  <script>
    const canvas = document.getElementById('matrix');
    const ctx = canvas.getContext('2d');
    canvas.height = window.innerHeight;
    canvas.width = window.innerWidth;

    const letters = 'アカサタナハマヤラワ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
    const fontSize = 14;
    const columns = canvas.width / fontSize;
    const drops = new Array(Math.floor(columns)).fill(1);

    function draw() {
      ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = '#0F0';
      ctx.font = fontSize + 'px monospace';

      for (let i = 0; i < drops.length; i++) {
        const text = letters[Math.floor(Math.random() * letters.length)];
        ctx.fillText(text, i * fontSize, drops[i] * fontSize);
        if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
          drops[i] = 0;
        }
        drops[i]++;
      }
    }

    setInterval(draw, 33);

    window.addEventListener('resize', () => {
      canvas.height = window.innerHeight;
      canvas.width = window.innerWidth;
    });
  </script>
</body>
</html>

