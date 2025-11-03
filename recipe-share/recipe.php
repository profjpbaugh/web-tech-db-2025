<?php
require_once __DIR__ . '/lib/Database.php';
$pdo = Database::connect();

// Validate id (make sure it's an int!)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
  http_response_code(400);
  exit('Invalid recipe id');
}

// Get recipe
$sqlRecipe = "SELECT recipe_id, title, author, created_at
              FROM recipes
              WHERE recipe_id = :id";
$s1 = $pdo->prepare($sqlRecipe);
$s1->bindValue(':id', $id, PDO::PARAM_INT);
$s1->execute();
$recipe = $s1->fetch();

if (!$recipe) {
  http_response_code(404);
  exit('Recipe not found');
}

// Get ingredients via join
$sqlIng = "SELECT i.name, i.amount
           FROM ingredients i
           WHERE i.recipe_id = :id
           ORDER BY i.ingredient_id ASC";


$s2 = $pdo->prepare($sqlIng);
$s2->bindValue(':id', $id, PDO::PARAM_INT);
$s2->execute();
$ingredients = $s2->fetchAll();
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title><?= htmlspecialchars($recipe['title']) ?></title></head>
<body>
  <a href="recipes.php">Back to recipes</a>
  <h1><?= htmlspecialchars($recipe['title']) ?></h1>
  <p>By <?= htmlspecialchars($recipe['author']) ?>  Posted <?= htmlspecialchars($recipe['created_at']) ?></p>

  <h2>Ingredients</h2>
  <ul>
    <?php foreach ($ingredients as $ing): ?>
      <li><?= htmlspecialchars($ing['amount']) ?>  <?= htmlspecialchars($ing['name']) ?></li>
    <?php endforeach; ?>
  </ul>
</body>
</html>
