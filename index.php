<?php
session_start();
require_once 'connection.php'; // Include your database connection file

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] === 'ADMIN') {
        header("Location: /DCL/DLMS/dashboard.php");
        exit();
    } else {
        header("Location: /DCL/DLMS/dtms_dashboard.php");
        exit();
    }
}

// Initialize the error variable
$error = ''; // Initialize as an empty string

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database for the user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (($password)) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === 'ADMIN') {
                header("Location: /DCL/DLMS/dashboard.php");
            } else {
                header("Location: /DCL/DLMS/dtms_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password."; // Set error message for incorrect password
        }
    } else {
        $error = "Invalid username or password."; // Set error message for unknown username
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('media/fint1.png') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            flex-direction: column; /* Changed to column for footer placement */
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            position: relative;
        }

        h2 {
            text-align: center;
            color: white;
        }

        form {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            width: 300px;
        }

        form div {
            margin-bottom: 15px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-popup {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: orangered;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: none; 
            z-index: 100;
            font-size: 20px;
            animation: fade-in 0.7s ease;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        footer {
            position: absolute; /* Position footer at the bottom */
            bottom: 20px;
            text-align: center;
            color: black;
            font-size: 22px;
            opacity: 1; /* Slight transparency */
        }
    </style>
</head>
<body>

    <!-- Error pop-up -->
    <div id="errorPopup" class="error-popup"></div>
    
    <form method="POST" action="">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>

    <script>
        // JavaScript pop-up error display
        window.onload = function() {
            // Only show the error if the error variable is set
            <?php if ($error): ?>
                const errorPopup = document.getElementById('errorPopup');
                errorPopup.textContent = "<?php echo addslashes($error); ?>"; // Escape the error message
                errorPopup.style.display = 'block';

                // Hide the pop-up after 5 seconds
                setTimeout(function() {
                    errorPopup.style.display = 'none';
                }, 5000);
            <?php endif; ?>
        };
    </script>

    <!-- Footer -->
    <footer>
        &copy; <?php echo date("Y"); ?> ESCO SOLUTIONS. All rights reserved.
    </footer>

</body>
</html>
