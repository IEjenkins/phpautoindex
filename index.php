<?php
function listFiles($dir = null) {
    $exclude = ['index.php', 'secret.txt', 'config.json', 'jenkins.png'];
    $search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

    $dir = $dir ?: __DIR__;

    if (!is_dir($dir)) {
        return '<tr><td colspan="3">folder not found.</td></tr>';
    }

    $files = array_diff(scandir($dir), ['.', '..']);
    $rows = '';

    foreach ($files as $file) {
        $path = "$dir/$file";
        if (is_file($path) && !in_array($file, $exclude)) {
            if ($search && strpos(strtolower($file), $search) === false) {
                continue;
            }
            $size = round(filesize($path) / 1024, 2) . ' KB';
            $date = date("d.m.Y | H:i", filemtime($path));
            $rows .= "<tr>
                        <td><a href=\"$file\">$file</a></td>
                        <td>$size</td>
                        <td>$date</td>
                      </tr>";
        }
    }

    return $rows ?: '<tr><td colspan="3">Nothing...</td></tr>';
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>iexpl.org | index</title>
<style>
  html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: transparent;
    overflow-x: hidden;
  }

  canvas#starfield {
    position: fixed;
    top: 0;
    left: 0;
    z-index: -1;
    width: 100%;
    height: 100%;
  }

  .header {
    background-color: #005533;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    color: white;
  }

  .logo-title {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
  }

  .logo-title img {
    height: 60px;
  }

  .logo-title h1 {
    margin: 0;
    font-size: 24px;
    color: white;
  }

  .search-bar {
    display: flex;
    gap: 6px;
    flex-grow: 1;
  }

  .search-bar input {
    flex-grow: 1;
    padding: 10px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
  }

  .search-bar button {
    padding: 10px 14px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    background: #fff;
    color: #005533;
    cursor: pointer;
  }

  .intro, .container, .ad-space {
    background-color: rgba(255, 255, 255, 0.9);
    margin: 20px;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
  }

  .intro {
    border-left: 5px solid #005533;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    padding: 10px 14px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }

  th {
    background-color: #f2f2f2;
    cursor: pointer;
  }

  th a {
    text-decoration: none;
    color: #333;
  }

  td a {
    color: #0066cc;
    text-decoration: none;
  }

  td a:hover {
    text-decoration: underline;
  }

  .ad-space {
    background-color: #eaeaea;
    border: 2px dashed #ccc;
    text-align: center;
    font-size: 14px;
    color: #555;
  }

  .footer {
    background-color: #f2f2f2;
    padding: 10px 20px;
    text-align: center;
    color: #555;
    margin-top: 20px;
  }

  @media (max-width: 600px) {
    .header {
      flex-direction: column;
      align-items: stretch;
    }

    .logo-title {
      justify-content: flex-start;
    }

    .search-bar {
      flex-direction: column;
      max-width: 100%;
    }

    .search-bar button {
      width: 100%;
    }
  }
</style>


</head>
<body>

  <canvas id="starfield"></canvas>

  <div class="header">
    <img src="jenkins.png" alt="Jenkins Logo">
    <form method="GET" class="search-bar">
      <input type="text" name="search" placeholder="🔍 Search..">
      <button type="submit">🔍</button>
      <button type="button" onclick="history.back()">🔙</button>
	  <button type="button" onclick="location.href=location.pathname">🏠</button>
    </form>
  </div>

  <div class="intro">
    <p>📁 Welcome to <strong>iExpl.org!</strong></p>
  </div>

  <div class="container">
    <table>
      <thead>
        <tr>
          <th><a href="#">File Name &#x25BC;</a></th>
          <th><a href="#">File Size &#x25BC;</a></th>
          <th><a href="#">Date &#x25BC;</a></th>
        </tr>
      </thead>
      <tbody>
        <?= listFiles() ?>
      </tbody>
    </table>
  </div>

  <div class="ad-space">
    ?
  </div>

  <script>
    const canvas = document.getElementById('starfield');
    const ctx = canvas.getContext('2d');
    let stars = [];

    function resizeCanvas() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    function createStars(count) {
      stars = [];
      for (let i = 0; i < count; i++) {
        stars.push({
          x: Math.random() * canvas.width,
          y: Math.random() * canvas.height,
          radius: Math.random() * 1.5,
          speed: Math.random() * 0.5 + 0.2
        });
      }
    }

    function drawStars() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = 'black';
      ctx.fillRect(0, 0, canvas.width, canvas.height);

      ctx.fillStyle = 'white';
      for (let star of stars) {
        ctx.beginPath();
        ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
        ctx.fill();

        star.y += star.speed;
        if (star.y > canvas.height) {
          star.y = 0;
          star.x = Math.random() * canvas.width;
        }
      }
    }

    createStars(150);
    setInterval(drawStars, 30);
  </script>

</body>
</html>
