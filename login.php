<?php
session_start();
$errors = [];
$success = false;
$input = $_POST;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (validate_login($errors, $input)) {
        // Log the user in and update the last login time
        $data_str = file_get_contents("users.json");
        $users = json_decode($data_str, true);
        foreach ($users['users'] as &$user) {
            if ($user['username'] === $input['username']) {
                $_SESSION['username'] = $input['username'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                // Update last login time
                $user['last_login'] = date('Y-m-d H:i:s');
                break;
            }
        }
        // Save the updated users data
        file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));
        
        header("Location: index.php");
        exit();
    }
}


function validate_login(&$errors, $input)
{
    if (empty($input['username'])) {
        $errors[] = 'Enter a username!';
    }

    if (empty($input['password'])) {
        $errors[] = 'Enter a password!';
    }

    if (count($errors) === 0) {
        $data_str = file_get_contents("users.json");
        $users = json_decode($data_str, true);
        $found = false;
        foreach ($users['users'] as $user) {
            if ($user['username'] === $input['username'] && password_verify($input['password'], $user['password'])) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $errors[] = 'Invalid username or password!';
        }
    }

    return count($errors) === 0;
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="synthwave">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.9.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <h1 class="text-3xl text-primary font-bold text-center py-5">Login
        <a href="index.php" class="btn btn-primary btn-sm font-bold ml-10 mb-2 ">Back to main page</a>
    </h1>
    <div class="flex">
        <form action="login.php" method="post" class="mx-auto mt-3 w-3/12 p-10">
            <h1 class="text-3xl p-5 font-bold">Login</h1>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Username</span>
                </div>
                <input type="text" name="username" placeholder="Type here" value="<?= isset($input['username']) ? htmlspecialchars($input['username']) : '' ?>" class="input input-bordered w-full max-w-xs" />
            </label>
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">Password</span>
                </div>
                <input type="password" name="password" placeholder="Type here" class="input input-bordered w-full max-w-xs" />
            </label>
            <input type="submit" value="Login" class="btn btn-primary font-bold">
        </form>

        <div class="results w-6/12 m-auto p-10">
            <?php if ($success): ?>
                <div class="success">
                    <h2 class="text-3xl mb-2 font-bold">Login successful 😍</h2>
                    <a class="btn btn-primary font-bold mt-1" href="index.php">Go back to Main Page</a>
                </div>
                <script>
                    setTimeout(function(){
                        window.location.href = 'index.php';
                    }, 2000);
                </script>
            <?php endif; ?>

            <?php if (count($errors) > 0 && !empty($input)): ?>
            <div class="errors">
                <h2 class="text-3xl mb-5 font-bold">Login failed</h2>
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
