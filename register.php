<?php
session_start();
$errors = [];
$success = false;
$input = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validate_registration($errors, $input)) {
        // Add user to JSON
        $users_str = file_get_contents('users.json');
        $users = json_decode($users_str, true);

        $new_user = [
            "username" => $input['username'],
            "email" => $input['email'],
            "password" => password_hash($input['password'], PASSWORD_DEFAULT),
            "is_admin" => false,
            "read_books" => []
        ];

        // Add the new user to the users array
        $users['users'][] = $new_user;

        // Write back to the users.json file
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));

        $_SESSION['username'] = $input['username'];
        $success = true;
    }
}

function validate_registration(&$errors, $input)
{
    if (empty($input['username'])) {
        $errors[] = 'Enter a username!';
    }
    if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email!';
    }
    if (empty($input['password'])) {
        $errors[] = 'Enter a password!';
    }
    if ($input['password'] !== $input['confirm_password']) {
        $errors[] = 'Passwords do not match!';
    }

    return count($errors) === 0;
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="synthwave">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.9.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <h1 class="text-3xl text-primary font-bold text-center py-5">Register
        <a href="index.php" class="btn btn-primary btn-sm font-bold ml-10 mb-2 ">Back to main page</a>
    </h1>
    <div class="flex">
        <form action="register.php" method="post" class="mx-auto mt-3 w-3/12 p-10">
            <h1 class="text-3xl p-5 font-bold">Register</h1>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Username</span>
                </div>
                <input type="text" name="username" placeholder="Type here" value="<?= isset($input['username']) ? htmlspecialchars($input['username']) : '' ?>" class="input input-bordered w-full max-w-xs" />
            </label>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Email</span>
                </div>
                <input type="email" name="email" placeholder="Type here" value="<?= isset($input['email']) ? htmlspecialchars($input['email']) : '' ?>" class="input input-bordered w-full max-w-xs" />
            </label>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Password</span>
                </div>
                <input type="password" name="password" placeholder="Type here" class="input input-bordered w-full max-w-xs" />
            </label>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Confirm Password</span>
                </div>
                <input type="password" name="confirm_password" placeholder="Type here" class="input input-bordered w-full max-w-xs" />
            </label>
            <input type="submit" value="Register" class="btn btn-primary font-bold">
        </form>

        <div class="results w-6/12 m-auto p-10">
            <?php if ($success): ?>
                <div class="success">
                    <h2 class="text-3xl mb-2 font-bold">Registration successful 😍</h2>
                    <a class="btn btn-primary font-bold mt-1" href="index.php">Go back to Main Page</a>
                </div>
            <?php endif; ?>

            <?php if (count($errors) > 0 && !empty($input)): ?>
            <div class="errors">
                <h2 class="text-3xl mb-5 font-bold">Registration failed</h2>
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
