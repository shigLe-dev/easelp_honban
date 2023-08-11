<html>
    <head>
        <meta charset="UTF-8">
        <title>easeLp - Sign Up</title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/login.css">

        <link rel="preconnect" href="https://rsms.me/">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700">

        <link rel="icon" type="image/svg+xml" href="./logo.svg" />

        <meta name="viewport" content="width=device-width,initial-scale=1">

    </head>

    <body>
        <header>
        <?php include("./header.php"); ?>
        </header>

        <div class="login_box_main">
            <div style="text-align: left;display: inline-block;">
                <div class="login_text">Sign Up</div><br>
                <form method="POST" action="./signup.php">
                    <span>user id</span><br>
                    <input type="text" name="user_id" id="user_id" class="input_box" pattern="^[a-zA-Z0-9]+$" required placeholder="user id">
                    <div style="padding-top:2em;">
                        <span>password</span><br>
                        <input type="password" name="password" id="password" class="input_box" required placeholder="password"><br>
                    </div>
                    <div style="padding-top:2em;">
                        <span>Please re-enter your passowrd.</span><br>
                        <input type="password" name="re_password" id="re_password" class="input_box" required placeholder="re-enter password"><br>
                        <input type="submit" class="btn" value="Sign up">
                        </div>
                </from>

                <div style="text-align:center;padding-top:1em;">
                    <a href="./login_form.php" style="color:#888888;text-decoration:none;">Login</a>
                </div>
            </div>
        </div>

        </div>
    </body>
</html>