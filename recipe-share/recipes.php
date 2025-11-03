<?php
require_once __DIR__ . '/lib/Database.php';
$pdo = Database::connect();

$sql = "SELECT recipe_id, title, author, created_at
        FROM recipes
        ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$recipes = $stmt->fetchAll();
?>

<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Recipes</title></head>
<body>
  <h1>Recipes</h1>
  <ul>
    <?php foreach ($recipes as $r): ?>
      <li>
        <a href="recipe.php?id=<?= htmlspecialchars($r['recipe_id']) ?>">
          <?= htmlspecialchars($r['title']) ?>
        </a>
        by <?= htmlspecialchars($r['author']) ?>
      </li>
    <?php endforeach; ?>
  </ul>
</body>
