<?php 
session_start();
$data_str = file_get_contents('books.json');
$books = json_decode($data_str, true);

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IK-Library Books</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.9.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header class="flex justify-between p-5 bg-base-200">
        <h1 class="text-3xl text-primary font-bold">IK-Library Books</h1>
        <div>
            <?php if (isset($_SESSION['username'])): ?>
                <span class="text-lg font-bold">Hello, <a href="user_detail.php" class="text-blue-500 underline"><?= htmlspecialchars($_SESSION['username']) ?></a></span>
                <a href="logout.php" class="btn btn-primary btn-sm font-bold ml-5 mb-2 ">Logout</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary btn-sm font-bold ml-5 mb-2 ">Register</a>
                <a href="login.php" class="btn btn-primary btn-sm font-bold ml-5 mb-2 ">Login</a>
            <?php endif; ?>
            <?php if ($is_admin): ?>
                <a href="addbook.php" class="btn btn-primary btn-sm font-bold ml-5 mb-2 ">Add Book</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="md:w-8/12 mx-auto p-3 ">
        <h2 class="mb-3 text-xl text-bold text-secondary">Filters and options</h2>
        <form action="index.php" method="get" class="grid lg:grid-cols-6 grid-cols-1 gap-3">
            <label class="input input-bordered flex items-center lg:col-span-2">
                <input type="text" class="grow" placeholder="Search" name="search" />
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70">
                    <path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" />
                </svg>
            </label>
            <select class="select select-bordered w-full" name="author">
                <option disabled selected>Filter by author</option>
                <?php
                $authors = array_unique(array_column($books['books'], 'author'));
                foreach ($authors as $author) {
                    echo "<option value=\"$author\">$author</option>";
                }
                ?>
            </select>
            <select class="select select-bordered w-full" name="genre">
                <option disabled selected>Filter by genre</option>
                <?php
                $genres = array_unique(array_column($books['books'], 'genre'));
                foreach ($genres as $genre) {
                    echo "<option value=\"$genre\">$genre</option>";
                }
                ?>
            </select>
            <select class="select select-bordered w-full" name="theme">
                <option disabled selected>Set the theme!</option>
                <option value="forest">Forest</option>
                <option value="cyberpunk">Cyberpunk</option>
                <option value="synthwave">Synthwave</option>
                <option value="coffee">Coffee</option>
            </select>
            <input type="submit" class="btn btn-primary w-full">
        </form>
    </div>

    <div class="flex flex-wrap md:w-8/12 mx-auto">
        <?php
        $theme = isset($_GET['theme']) ? $_GET['theme'] : 'default';
        echo '<html lang="en" data-theme="' . $theme . '">';

        foreach ($books['books'] as $book) {
            $bookTitle = $book['title'];
            $bookAuthor = $book['author'];
            $bookCover = $book['cover_image'];

            if (isset($_GET['search']) && !str_contains(strtolower($book['title']), strtolower($_GET['search']))) {
                continue;
            }

            if (isset($_GET['author']) && $_GET['author'] != $book['author']) {
                continue;
            }

            if (isset($_GET['genre']) && $_GET['genre'] != $book['genre']) {
                continue;
            }

            echo '<div class="md:w-3/12 w-full p-3">';
            echo '<div class="card bg-base-200 shadow-xl">';
            echo '<div class="card-body items-center text-center">';
            echo '<div class="image">';
            echo '<img src="assets/' . htmlspecialchars($bookCover) . '" alt="' . htmlspecialchars($bookTitle) . '">';
            echo '</div>';
            echo '<h2 class="card-title text-secondary mb-3">' . htmlspecialchars($bookTitle) . '</h2>';
            echo '<div class="details">';
            echo '<p class="text-sm mb-2">Author: ' . htmlspecialchars($bookAuthor) . '</p>';
            echo '</div>';
            echo '<div class="card-actions justify-end mt-3">';
            echo '<a href="book_detail.php?title=' . urlencode($bookTitle) . '" class="btn btn-primary">View Details</a>';
            if ($is_admin) {
                echo '<a href="editbook.php?title=' . urlencode($bookTitle) . '" class="btn btn-secondary ml-2">Edit</a>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</body>

</html>
