login.php
<?php
include 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            if ($username == 'admin_math') {
                $_SESSION['subject'] = 'math';
                header("Location: admin.php");
            } elseif ($username == 'admin_science') {
                $_SESSION['subject'] = 'science';
                header("Location: admin.php");
            } elseif ($username == 'admin_history') {
                $_SESSION['subject'] = 'history';
                header("Location: admin.php");
            } else {
                header("Location: subjects.html");
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found.";
    }
    $conn->close();
}
?>
