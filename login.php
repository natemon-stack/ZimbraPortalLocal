<?php
session_start();
include "connectdb.php";

$email = $password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    if (empty($email)) $errors[] = "Email    is required";
    if (empty($password)) $errors[] = "Password is required";
    
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["email"] = $row["email"];
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Incorrect password";
            }
        } else {
            $errors[] = "User not found";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
        }
    }
    ?>
    <form method="POST" action="">
        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <br>
    <a href="signup.php">Create an account</a>
</div>
</body>
</html>
