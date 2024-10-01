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
            background: url('/media/fint1.png') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
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

        ul {
            display: none; 
        }
    </style>
</head>
<body>

    <!-- Error pop-up -->
    <div id="errorPopup" class="error-popup"></div>
    
    <form method="POST" action="login.php">
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

    <ul id="errorMessages">
        <!-- PHP code for displaying error messages -->
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <li><?php echo $message; ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <script>
        // JavaScript pop-up error display
        window.onload = function() {
            // Check for PHP error messages
            const messages = document.querySelectorAll('#errorMessages li');
            if (messages.length > 0) {
                let errorPopup = document.getElementById('errorPopup');
                let errorMessage = '';

                // Loop through the messages
                messages.forEach(function(message) {
                    errorMessage += message.textContent + ' ';
                });

                // Display the error message in the pop-up
                errorPopup.textContent = errorMessage.trim();
                errorPopup.style.display = 'block';

                // Hide the pop-up after 5 seconds
                setTimeout(function() {
                    errorPopup.style.display = 'none';
                }, 5000);
            }
        };
    </script>

</body>
</html>
