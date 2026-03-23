<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
require_once 'conn.php';
$message = '';

// Fetch categories for the dropdown
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    
    // Image Upload Handling
    $imagePath = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Basic validation
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $fileName;
            } else {
                $message = "<div class='alert alert-error'>Failed to upload image.</div>";
            }
        } else {
            $message = "<div class='alert alert-error'>Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.</div>";
        }
    }

    if (empty($message) && !empty($name) && !empty($price) && !empty($category_id)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image_path) VALUES (:name, :description, :price, :category_id, :image_path)");
            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $category_id,
                'image_path' => $imagePath
            ]);
            $message = "<div class='alert alert-success'>Product added successfully!</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
        }
    } elseif(empty($message)) {
        $message = "<div class='alert alert-error'>Please fill in all required fields.</div>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <h2 style="margin-top:0; color:var(--primary);">Add New Product</h2>
        <?php echo $message; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="Product Name">
            </div>
            
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price *</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label for="description">Description (Optional)</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Product details..."></textarea>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Add Product</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
