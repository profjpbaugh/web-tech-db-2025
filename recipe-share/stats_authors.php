<?php
require_once __DIR__ . '/lib/Database.php';
$pdo = Database::connect();

$sql = "SELECT author, COUNT(*) AS recipe_count
        FROM recipes
        GROUP BY author
        ORDER BY recipe_count DESC, author ASC";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Author Stats</title></head>
<body>
  <a href="recipes.php">Back to recipes</a>
  <h1>Recipes by Author</h1>
  <table border="1" cellpadding="6">
    <tr><th>Author</th><th>Recipe Count</th></tr>
    <?php foreach ($rows as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['author']) ?></td>
        <td><?= htmlspecialchars($row['recipe_count']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
