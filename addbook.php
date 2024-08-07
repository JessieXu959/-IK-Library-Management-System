<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo "<script>alert('Only administrators can add books.'); window.location.href='index.php';</script>";
    exit();
}

$errors = [];
$input = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validate_book($errors, $input)) {
        // Add book to JSON
        $books_str = file_get_contents('books.json');
        $books = json_decode($books_str, true);

        $new_book = [
            "title" => $input['title'],
            "author" => $input['author'],
            "year" => (int)$input['year'],
            "description" => $input['description'],
            "cover_image" => $input['cover_image'],
            "planet" => $input['planet']
        ];

        $books['books'][] = $new_book;
        file_put_contents('books.json', json_encode($books, JSON_PRETTY_PRINT));

        echo "<script>alert('Book added successfully!'); window.location.href='index.php';</script>";
        exit();
    }
}

function validate_book(&$errors, $input)
{
    if (empty($input['title'])) {
        $errors[] = 'Enter a book title!';
    }
    if (empty($input['author'])) {
        $errors[] = 'Enter the author\'s name!';
    }
    if (empty($input['year']) || !is_numeric($input['year'])) {
        $errors[] = 'Enter a valid year!';
    }
    if (empty($input['description'])) {
        $errors[] = 'Enter a description!';
    }
    if (empty($input['cover_image'])) {
        $errors[] = 'Enter the cover image filename!';
    }
    if (empty($input['planet']) || !preg_match('/^P\d{1,2}[A-Z]-\d{3,4}$/', $input['planet'])) {
        $errors[] = 'Enter a valid planet code!';
    }

    return count($errors) === 0;
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="synthwave">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.9.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <h1 class="text-3xl text-primary font-bold text-center py-5">Add a New Book
        <a href="index.php" class="btn btn-primary btn-sm font-bold ml-10 mb-2 ">Back to main page</a>
    </h1>
    <div class="flex">
        <form action="addbook.php" method="post" class="mx-auto mt-3 w-3/12 p-10">
            <h1 class="text-3xl p-5 font-bold">Add Book</h1>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Title</span>
                </div>
                <input type="text" name="title" placeholder="Type here" value="<?= isset($input['title']) ? htmlspecialchars($input['title']) : '' ?>" class="input input-bordered w-full max-w-xs" />
            </label>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Author</span>
                </div>
                <input type="text" name="author" placeholder="Type here" value="<?= isset($input['author']) ? htmlspecialchars($input['author']) : '' ?>" class="input input-bordered w-full max-w-xs" />
            </label>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Year</span>
                </div>
                <input type="text" name="year" placeholder="Type here" value="<?= isset($input['year']) ? htmlspecialchars($input['year']) : '' ?>" class="input input-bordered w-full max-w-xs" />
            </label>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Description</span>
                </div>
                <textarea name="description" placeholder="Type here" class="textarea textarea-bordered w-full max-w-xs"><?= isset($input['description']) ? htmlspecialchars($input['description']) : '' ?></textarea>
            </label>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Cover Image Filename</span>
                </div>
                <input type="text" name="cover_image" placeholder="Type here" value="<?= isset($input['cover_image']) ? htmlspecialchars($input['cover_image']) : '' ?>" class="input input-bordered w-full max-w-xs" />
            </label>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Planet Code</span>
                </div>
                <input type="text" name="planet" placeholder="Type here" value="<?= isset($input['planet']) ? htmlspecialchars($input['planet']) : '' ?>" class="input input-bordered w-full max-w-xs" />
            </label>
            <input type="submit" value="Add Book" class="btn btn-primary font-bold">
        </form>

        <div class="results w-6/12 m-auto p-10">
            <?php if (count($errors) > 0 && !empty($input)): ?>
            <div class="errors">
                <h2 class="text-3xl mb-5 font-bold">Failed to add book</h2>
                <?php foreach ($errors as $error): ?>
                    <div role="alert" class="alert alert-error mb-2">
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
