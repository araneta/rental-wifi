<?php
$base = "http://localhost:8080/";
$manifest = json_decode(file_get_contents(__DIR__ . "/assets/.vite/manifest.json"), true);
//var_dump($manifest);
$script = $manifest['src/main.js']['file'];
$styles = $manifest['src/main.js']['css'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <?php foreach ($styles as $css): ?>
    <link rel="stylesheet" href="<?= $base ?>/assets/<?= $css ?>">
  <?php endforeach; ?>
</head>
<body>
  <div id="app"></div>

  <script type="module" src="<?= $base ?>/assets/<?= $script ?>"></script>
</body>
</html>
