<?php 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /");
    die();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Errore di autenticazione</title>

    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;

            background-image: url('/assets/img/background.png');
            background-color: #0178bc;
            background-size: cover;
            background-position: center;

            font-family: Arial, Helvetica, sans-serif;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 32px 40px;
            max-width: 620px;
            width: 90%;
            margin-left: 5%;
            margin-right: 5%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .error-container h1 {
            margin: 0 0 24px 0;
            color: #0178bc;
            font-size: 1.4rem;
            text-align: center;
            line-height: 1.4;
        }

        .error-details {
            border-top: 1px solid #eee;
            padding-top: 16px;
        }

        .error-details p {
            margin: 10px 0;
            font-size: 0.95rem;
            color: #333;
            word-break: break-word;
        }

        .error-details span {
            font-weight: bold;
            color: #555;
        }

        .error-footer {
            margin-top: 24px;
            text-align: center;
        }

        .error-footer a {
            color: #0178bc;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .error-footer a:hover {
            text-decoration: underline;
        }

        /* 📱 Mobile */
        @media (max-width: 480px) {
            .error-container {
                padding: 24px 20px;
            }

            .error-container h1 {
                font-size: 1.2rem;
            }

            .error-details p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>

<div class="error-container">
    <h1>Errore durante il processo di autenticazione su Vitaever</h1>

    <div class="error-details">
        <p>
            <span>Status Code:</span>
            <?php echo htmlspecialchars($_POST['statusCode']); ?>
        </p>
        <p>
            <span>Status Message:</span>
            <?php echo htmlspecialchars($_POST['statusMessage']); ?>
        </p>
        <p>
            <span>Dettaglio errore:</span>
            <?php echo htmlspecialchars($_POST['errorMessage']); ?>
        </p>
    </div>
</div>

</body>
</html>
