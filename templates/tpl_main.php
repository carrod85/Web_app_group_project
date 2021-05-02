<!DOCTYPE html >
<head>
    <html lang="en" class="theme-light">
    <meta charset="utf-8">
    <title >{TITLE}</title >
    <link rel ="stylesheet" type ="text/css" href ="styles/style_main.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <script src="lib/functions_javascript.js"></script>
</head >
<body >
    <header>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="form.php">Form</a></li>
                <li><a href="results.php">Results</a></li>
                <li><a href="employees.php">Employees</a></li>
                <li><a href="support.php">Support</a></li>
                {ADMIN}
                <li><a href="lib/exit_session.php">Log out</a></li>
            </ul>
        </nav>
    </header>
    <section>
        <div class= "evaluation">
            <h2>{TITLE1}</h2>
            <h3>{SUBTITLE1}</h3>
            {ERROR1}
            <p>{CONTENT}</p>
            <p>{FORM}</p>
            <p>{TABLE}</p>
        </div>
        <div class="updates">
            <p class="users">{USER}<button style="margin-left: 30px;" id="switch" onclick="toggleTheme()">Switch Color Mode</button></p>
            <h2>{TITLE2}</h2>
            <h3>{SUBTITLE2}</h3>
            {ERROR2}
            <p>{SIDECONTENT1}</p>
            <h2>{TITLE3}</h2>
            <h3>{SUBTITLE3}</h3>
            {ERROR3}
            <p>{SIDECONTENT2}</p>
        </div>
    </section>
    <footer>@All rights reserved Cyberbees® | For internal use only | {TIME} </footer>
</body >
</html >





        
