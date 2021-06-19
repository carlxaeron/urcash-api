<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>B2B - @yield('title')</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <style type="text/css">
        body {
            font-family: 'Roboto', sans-serif;
        }
        #website-title {
            margin: auto;
            padding: 10px;
        }
        #verification-code {
            letter-spacing: 6px;
            padding: 12px;
        }
        .circle-bordered-dark-blue {
            border: 2px solid #5b82fb;
            border-radius: 12px;
            width: 95%;
            padding: 8px;
            margin-top: 8px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <header class="container row">
        <div class="col" id="website-title">
            <h2>B2B</h2>
        </div>
    </header>

    <section class="container circle-bordered-dark-blue">
        @yield('content')

        <p>All the best,</p>
        <p>B2B Customer Support Team</p>
    </section>

    <footer class="container text-center">
        <p style="text-align: center;">Copyright &copy; 2020 B2B. All rights reserved.</p>
    </footer>
</body>
</html>