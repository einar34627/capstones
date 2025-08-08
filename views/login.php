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
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            background: url('images/images (1).jpg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(8px);
        }
        .login-container {
            background: rgba(255,255,255,0.12);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.18);
            width: 350px;
            max-width: 90vw;
            margin: 60px auto;
            padding: 32px 28px 24px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-container h2 {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 24px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .input-group {
            width: 100%;
            margin-bottom: 18px;
            position: relative;
        }
        .input-group input {
            width: 100%;
            padding: 12px 40px 12px 16px;
            border-radius: 8px;
            border: none;
            background: rgba(255,255,255,0.25);
            color: #222;
            font-size: 1rem;
            outline: none;
        }
        .input-group .icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 1.2rem;
        }
        .options {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            font-size: 0.95rem;
            color: #fff;
        }
        .options label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        .options a {
            color: #fff;
            text-decoration: underline;
            font-size: 0.95rem;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            border-radius: 24px;
            border: none;
            background: #fff;
            color: #cfcdbdff;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 12px;
            transition: background 0.2s;
        }
        .login-btn:hover {
            background: #e0e0e0;
        }
        .signup-link {
            color: #fff;
            font-size: 1rem;
            text-align: center;
        }
        .signup-link a {
            color: #fff;
            font-weight: bold;
            text-decoration: underline;
        }
        .error {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
        .errors {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .errors ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <form action="process_login.php" method="post">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
                <span class="icon"><i class="fa fa-user"></i></span>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <span class="icon"><i class="fa fa-lock"></i></span>
            </div>
            <div class="options">
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
                <a href="#">Forgot Password</a>
            </div>
            <input type="submit" name="login" value="Login" class="login-btn">
        </form>
        <div class="signup-link">
            <p style="margin: 0; color: #fff; font-size: 0.9rem;">Administrator Access Only</p>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</body>
</html>