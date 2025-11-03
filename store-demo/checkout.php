<?php
require_once __DIR__ . '/lib/Database.php';
$pdo = Database::connect();

// Example cart: product_id => quantity
$cart = [
  1 => 2,   // 2 notebooks
  2 => 5,   // 5 pens
  3 => 1    // 1 backpack
];

try {
  // Begin the transaction
  $pdo->beginTransaction();

  // Calculate total
  $total = 0;
  foreach ($cart as $id => $qty) {
    $stmt = $pdo->prepare('SELECT price, stock FROM products WHERE product_id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch();
    if (!$product) {
      throw new Exception("Product $id not found");
    }
    if ($product['stock'] < $qty) {
      throw new Exception("Insufficient stock for product $id");
    }
    $lineTotal = $product['price'] * $qty;
    $total += $lineTotal;
  }

  // Insert the order
  $stmtOrder = $pdo->prepare('INSERT INTO orders (total) VALUES (:total)');
  $stmtOrder->bindValue(':total', $total);
  $stmtOrder->execute();
  $orderId = $pdo->lastInsertId();

  // Insert order items and update inventory
  foreach ($cart as $id => $qty) {
    $stmtProduct = $pdo->prepare('SELECT price FROM products WHERE product_id = :id');
    $stmtProduct->bindValue(':id', $id);
    $stmtProduct->execute();
    $price = $stmtProduct->fetchColumn();
    $lineTotal = $price * $qty;

    $stmtItem = $pdo->prepare('INSERT INTO order_items (order_id, product_id, qty, line_total)
                               VALUES (:order_id, :pid, :qty, :line)');
    $stmtItem->execute([
      ':order_id' => $orderId,
      ':pid' => $id,
      ':qty' => $qty,
      ':line' => $lineTotal
    ]);

    $stmtUpdate = $pdo->prepare('UPDATE products SET stock = stock - :qty WHERE product_id = :id');
    $stmtUpdate->execute([':qty' => $qty, ':id' => $id]);
  }

  // Commit all changes
  $pdo->commit();
  echo "<p>Order #$orderId completed successfully.  Total: $$total</p>";

} catch (Exception $e) {
  // Roll back if any error occurs
  $pdo->rollBack();
  echo '<p style="color:red;">Transaction failed: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
