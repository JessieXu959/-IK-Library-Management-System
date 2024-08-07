<?php
$admin_password = 'admin'; // orginal password
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

$users = [
    [
        "username" => "admin",
        "email" => "admin@example.com",
        "password" => $hashed_password,
        "is_admin" => true,
        "read_books" => []
    ]
];

file_put_contents('users.json', json_encode(['users' => $users], JSON_PRETTY_PRINT));

echo "Admin user created successfully.";
?>
