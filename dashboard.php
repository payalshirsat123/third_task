<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

// Handle search input
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$whereClause = $search ? "WHERE title LIKE '%$search%' OR content LIKE '%$search%'" : '';

// Pagination setup
$postsPerPage = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startFrom = ($page - 1) * $postsPerPage;

// Main query with search + pagination
$query = "SELECT * FROM posts $whereClause ORDER BY created_at DESC LIMIT $startFrom, $postsPerPage";
$result = mysqli_query($conn, $query);

// Total post count for pagination
$totalQuery = "SELECT COUNT(*) as total FROM posts $whereClause";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPosts = $totalRow['total'];
$totalPages = ceil($totalPosts / $postsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard | Blog by Payal Shirsat</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      background-color: #fff;
      color: #007bff;
      padding: 15px 20px;
      text-align: center;
      font-size: 26px;
      font-weight: 700;
      letter-spacing: 2px;
      border-bottom: 2px solid #007bff;
      font-style: italic;
    }

    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      background-color: #333;
      color: white;
      text-align: center;
      font-size: 14px;
      padding: 15px 20px;
    }

    main {
      padding: 100px 20px 100px; /* space for header and footer */
      min-height: 100vh;
      background-color: #f8f9fa;
    }

    .card img {
      max-width: 100%;
      height: auto;
    }

    .page-link {
      cursor: pointer;
    }
  </style>
</head>
<body>

<header>
  <i class="fas fa-feather-alt"></i> My Blogs
</header>

<main>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Your Posts</h2>
      <div>
        <a href="add_post.php" class="btn btn-success">Add New Post</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
      </div>
    </div>

    <!-- Search Form -->
    <form method="GET" action="dashboard.php" class="mb-4 d-flex">
      <input type="text" name="search" class="form-control me-2" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>" required>
      <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Posts -->
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="card mb-4 shadow-sm">
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($row['title']) ?></h3>
            <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>

            <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
              <img src="<?= htmlspecialchars($row['image']) ?>" alt="Post Image" class="img-fluid mb-3 rounded">
            <?php endif; ?>

            <small class="text-muted">Posted on <?= $row['created_at'] ?></small><br>
            <a href="edit_post.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm mt-2">Edit</a>
            <a href="delete_post.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Are you sure?')">Delete</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-muted">No posts found.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav>
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
              <a class="page-link" href="dashboard.php?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</main>

<footer>
  <p><i class="fas fa-heart"></i> Made by Payal Shirsat</p>
  <p>&copy; 2025 Payal Shirsat. All rights reserved.</p>
</footer>

</body>
</html>
