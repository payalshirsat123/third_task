<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id = (int)$_GET['id'];

// Fetch post and check if it belongs to the logged-in user
$stmt = $conn->prepare("SELECT title, content, image FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Unauthorized or post not found.";
    exit;
}

$row = $result->fetch_assoc();
$title = $row['title'];
$content = $row['content'];
$image = $row['image'];

$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $target = "uploads/" . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // Delete old image
            if (!empty($image) && file_exists($image)) {
                unlink($image);
            }
            $image = $target;
        } else {
            $msg = "Failed to upload image.";
        }
    }

    // Delete image if checkbox is selected
    if (isset($_POST['delete_image']) && $image && file_exists($image)) {
        unlink($image);
        $image = '';
    }

    // Update post
    $stmt = $conn->prepare("UPDATE posts SET title=?, content=?, image=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sssii", $title, $content, $image, $id, $user_id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}
?>

<!-- Optional Bootstrap Styling -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">


<div class="container mt-5">
    <h2>Edit Post</h2>
    <?php if ($msg): ?>
        <div class="alert alert-warning"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="title" class="form-label">Title:</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Content:</label>
            <textarea name="content" id="content" class="form-control" rows="5" required><?= htmlspecialchars($content) ?></textarea>
        </div>

        <?php if (!empty($image) && file_exists($image)): ?>
            <div class="mb-3">
                <img src="<?= htmlspecialchars($image) ?>" alt="Post Image" class="img-fluid mb-2" style="max-width: 300px;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="delete_image" id="delete_image">
                    <label class="form-check-label" for="delete_image">Delete current image</label>
                </div>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="image" class="form-label">Upload New Image (optional):</label>
            <input type="file" name="image" id="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Post</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>


