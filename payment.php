<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conn.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$productId = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate payment processing
    $cardNumber = trim($_POST['card_number'] ?? '');
    
    if (!empty($cardNumber)) {
        $message = "<div class='alert alert-success' style='text-align: center; font-size: 1.2rem;'>
            <strong>Payment Successful!</strong><br>
            Thank you for purchasing <em>" . htmlspecialchars($product['name']) . "</em>.<br>
            Your order is being processed. 
            <div style='margin-top: 1rem;'>
                <a href='dashboard.php' class='btn'>Return to Dashboard</a>
            </div>
        </div>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 900px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 2rem;">

    <?php if (!empty($message)): ?>
        <div style="flex: 1 1 100%;">
            <?php echo $message; ?>
        </div>
    <?php else: ?>
        <!-- Product Summary -->
        <div style="flex: 1 1 350px;">
            <div class="card" style="position: sticky; top: 100px;">
                <h3 style="margin-top:0; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Order Summary</h3>
                <div style="text-align: center; margin: 1rem 0;">
                    <?php if (!empty($product['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product" style="max-width: 100%; border-radius: 8px;">
                    <?php endif; ?>
                </div>
                <h4 style="margin: 0; color: #1E293B;"><?php echo htmlspecialchars($product['name']); ?></h4>
                <p style="color: #64748B; font-size: 0.9rem; margin-top: 0.5rem;"><?php echo htmlspecialchars($product['category_name']); ?></p>
                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <span>Total</span>
                    <span style="color: var(--primary);"><?php echo number_format($product['price'], 0, ',', ' '); ?> FCFA</span>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div style="flex: 2 1 400px;">
            <div class="card">
                <h3 style="margin-top:0; color:var(--primary);">Secure Checkout</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name_on_card">Name on Card</label>
                        <input type="text" id="name_on_card" name="name_on_card" class="form-control" required placeholder="John Doe">
                    </div>
                    
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" class="form-control" required placeholder="XXXX XXXX XXXX XXXX" pattern="\d*" maxlength="16">
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label for="expiry">Expiry Date</label>
                            <input type="text" id="expiry" name="expiry" class="form-control" required placeholder="MM/YY">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" class="form-control" required placeholder="123" maxlength="4">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 1.5rem;">
                        <button type="submit" class="btn" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Pay <?php echo number_format($product['price'], 0, ',', ' '); ?> FCFA</button>
                    </div>
                    <div class="text-center" style="font-size: 0.8rem; color: #94A3B8; margin-top: 1rem;">
                        <span style="display: inline-block; margin-right: 5px;">&#128274;</span> Payments are secure and encrypted.
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
