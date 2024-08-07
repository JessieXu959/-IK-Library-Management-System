<?php
session_start();
if (!isset($_GET['title'])) {
    echo "No book title provided.";
    exit;
}

$bookTitle = urldecode($_GET['title']);
$data_str = file_get_contents('books.json');
$books = json_decode($data_str, true);

$book = null;
foreach ($books['books'] as $b) {
    if ($b['title'] === $bookTitle) {
        $book = $b;
        break;
    }
}

if ($book === null) {
    echo "Book not found.";
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$errors = [];
$input = $_POST;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($username)) {
    if (validate_review($errors, $input)) {
        // Add review to JSON
        $reviews_str = file_get_contents('reviews.json');
        $reviews = json_decode($reviews_str, true);

        $new_review = [
            "username" => $username,
            "review" => $input['review'],
            "rating" => (int)$input['rating']
        ];

        if (!isset($reviews[$bookTitle])) {
            $reviews[$bookTitle] = [];
        }
        $reviews[$bookTitle][] = $new_review;
        file_put_contents('reviews.json', json_encode($reviews, JSON_PRETTY_PRINT));

        $success = true;
    }
}

if (isset($_POST['mark_read']) && isset($username)) {
    $users_str = file_get_contents('users.json');
    $users = json_decode($users_str, true);

    foreach ($users['users'] as &$user) {
        if ($user['username'] === $username) {
            if (!in_array($bookTitle, $user['read_books'])) {
                $user['read_books'][] = $bookTitle;
            }
            break;
        }
    }

    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    header("Location: book_detail.php?title=" . urlencode($bookTitle));
    exit();
}

function validate_review(&$errors, $input)
{
    if (empty($input['review']) || strlen($input['review']) < 4) {
        $errors[] = 'Enter a review of at least 4 characters!';
    }
    if (empty($input['rating']) || !is_numeric($input['rating']) || $input['rating'] < 1 || $input['rating'] > 5) {
        $errors[] = 'Enter a valid rating between 1 and 5!';
    }

    return count($errors) === 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.9.0/dist/full.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white">
    <h1 class="text-3xl text-primary font-bold text-center py-5"><?php echo htmlspecialchars($book['title']); ?></h1>
    <div class="md:w-8/12 mx-auto p-3">
        <div class="card bg-base-200 shadow-xl">
            <div class="card-body">
                <div class="flex">
                    <div class="w-1/3">
                        <img src="assets/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                    </div>
                    <div class="w-2/3 pl-5">
                        <h2 class="card-title text-secondary mb-3"><?php echo htmlspecialchars($book['title']); ?></h2>
                        <p>Author: <?php echo htmlspecialchars($book['author']); ?></p>
                        <p>Year: <?php echo htmlspecialchars($book['year']); ?></p>
                        <p>Description: <?php echo htmlspecialchars($book['description']); ?></p>
                        <p>Planet: <?php echo htmlspecialchars($book['planet']); ?></p>
                        <p>Genre: <?php echo htmlspecialchars($book['genre']); ?></p>
                        <?php if ($username): ?>
                            <form action="book_detail.php?title=<?= urlencode($bookTitle) ?>" method="post">
                                <input type="hidden" name="mark_read" value="1">
                                <button type="submit" class="btn btn-primary mt-3">Mark as Read</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($username): ?>
            <div class="card bg-base-200 shadow-xl mt-5">
                <div class="card-body">
                    <h2 class="card-title text-secondary mb-3">Add a Review</h2>
                    <form action="book_detail.php?title=<?= urlencode($bookTitle) ?>" method="post">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Review</span>
                            </label>
                            <textarea name="review" class="textarea textarea-bordered w-full"><?= htmlspecialchars($input['review'] ?? '') ?></textarea>
                        </div>
                        <div class="form-control mt-3">
                            <label class="label">
                                <span class="label-text">Rating</span>
                            </label>
                            <input type="number" name="rating" class="input input-bordered w-full" min="1" max="5" value="<?= htmlspecialchars($input['rating'] ?? '') ?>" />
                        </div>
                        <div class="form-control mt-5">
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </div>
                    </form>
                    <?php if ($success): ?>
                        <div class="alert alert-success mt-5">
                            <span>Review submitted successfully!</span>
                        </div>
                    <?php elseif (count($errors) > 0): ?>
                        <div class="alert alert-error mt-5">
                            <?php foreach ($errors as $error): ?>
                                <span><?= htmlspecialchars($error) ?></span><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-5">
                <span>You need to be logged in to submit a review.</span>
            </div>
        <?php endif; ?>
        <div class="text-center mt-5">
            <a href="index.php" class="btn btn-primary">Back to Main Page</a>
        </div>
    </div>
</body>

</html>
