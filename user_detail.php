<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$users_str = file_get_contents('users.json');
$users = json_decode($users_str, true);

$user = null;
foreach ($users['users'] as $u) {
    if ($u['username'] === $username) {
        $user = $u;
        break;
    }
}

if ($user === null) {
    echo "User not found.";
    exit();
}

$reviews_str = file_get_contents('reviews.json');
$reviews = json_decode($reviews_str, true);
$user_reviews = [];

foreach ($reviews as $book_title => $book_reviews) {
    foreach ($book_reviews as $review) {
        if ($review['username'] === $username) {
            $user_reviews[] = [
                'book_title' => $book_title,
                'review' => $review['review'],
                'rating' => $review['rating']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="synthwave">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.9.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <h1 class="text-3xl text-primary font-bold text-center py-5">User Details</h1>
    <div class="md:w-8/12 mx-auto p-3">
        <h2 class="text-2xl text-secondary font-bold mb-5">Profile</h2>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Last Login:</strong> <?php echo htmlspecialchars($user['last_login'] ?? 'Never'); ?></p>
        
        <h2 class="text-2xl text-secondary font-bold mt-5">Books Read</h2>
        <?php if (isset($user['read_books']) && count($user['read_books']) > 0): ?>
            <ul>
                <?php foreach ($user['read_books'] as $book_title): ?>
                    <li><?php echo htmlspecialchars($book_title); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No books read yet.</p>
            <?php endif; ?>

<h2 class="text-2xl text-secondary font-bold mt-5">Reviews Written</h2>
<?php if (count($user_reviews) > 0): ?>
    <?php foreach ($user_reviews as $review): ?>
        <div class="border border-gray-300 p-3 my-2">
            <p><strong>Book:</strong> <?php echo htmlspecialchars($review['book_title']); ?></p>
            <p><strong>Review:</strong> <?php echo htmlspecialchars($review['review']); ?></p>
            <p><strong>Rating:</strong> <?php echo htmlspecialchars($review['rating']); ?>/5</p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No reviews written yet.</p>
<?php endif; ?>
</div>
<div class="text-center mt-5">
<a href="index.php" class="btn btn-primary">Back to Main Page</a>
</div>
</body>

</html>
