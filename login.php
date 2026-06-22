<?php
session_start();
$error = '';
$success = '';

// Direct database connection
$conn = new mysqli("localhost", "root", "", "Airline");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        
        $result = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");
        if($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password! Try: user@gmail.com / 1234";
        }
    }
    
    if(isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        $phone = $_POST['phone'];
        
        $result = $conn->query("INSERT INTO users (name, email, password, phone) VALUES ('$name', '$email', '$password', '$phone')");
        if($result) {
            $success = "Registration successful! Please login.";
        } else {
            $error = "Registration failed. Email may already exist.";
        }
    }
    
    if(isset($_POST['admin_login'])) {
        if($_POST['admin_email'] == 'admin@gmail.com' && $_POST['admin_password'] == 'password') {
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_name'] = 'Administrator';
            $_SESSION['is_admin'] = true;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid admin credentials!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Airways - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a1428 0%, #03060f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-container {
            background: rgba(18, 28, 48, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            border: 1px solid rgba(0,224,255,0.3);
        }
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .tab {
            padding: 0.5rem 1rem;
            cursor: pointer;
            background: none;
            border: none;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
        }
        .tab.active {
            color: #00e0ff;
            border-bottom: 2px solid #00e0ff;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #b9c7dd;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(0,224,255,0.3);
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
        }
        .btn-submit {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(95deg, #00e0ff, #0077ff);
            border: none;
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        .error {
            background: rgba(255,0,0,0.2);
            padding: 0.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            color: #ff6b6b;
        }
        .success {
            background: rgba(0,255,0,0.2);
            padding: 0.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            color: #6bff6b;
        }
        .form-pane {
            display: none;
        }
        .form-pane.active {
            display: block;
        }
        h2 {
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h2>✈ Nexus Airways</h2>
    
    <?php if($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <div class="tabs">
        <button class="tab active" data-tab="user">User Login</button>
        <button class="tab" data-tab="admin">Admin Login</button>
        <button class="tab" data-tab="register">Sign Up</button>
    </div>
    
    <div id="user" class="form-pane active">
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="user@gmail.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required value="1234">
            </div>
            <button type="submit" name="login" class="btn-submit">Login as User</button>
        </form>
    </div>
    
    <div id="admin" class="form-pane">
        <form method="POST">
            <div class="form-group">
                <label>Admin Email</label>
                <input type="email" name="admin_email" required value="admin@gmail.com">
            </div>
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="admin_password" required value="password">
            </div>
            <button type="submit" name="admin_login" class="btn-submit">Login as Admin</button>
        </form>
    </div>
    
    <div id="register" class="form-pane">
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="register" class="btn-submit">Create Account</button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            document.querySelectorAll('.form-pane').forEach(pane => pane.classList.remove('active'));
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });
</script>
</body>
</html>