<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nutty API</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links {
            display: flex;
            margin: 0 0 25px 0;
        }

        .links > p {
            color: #636b6f;
            font-size: 13px;
            font-weight: 600;
            width: 30%;
        }

        .links > textarea {
            color: #636b6f;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 20px;
            width: 70%;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .button {
            margin: 0 0 25px 0;
            text-align: center;
        }

        .button button {
            background: none;
            border: 1px solid #636b6f;
            border-radius: 5px;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref">
    <div class="content">
        <div class="title m-b-md">Nutty RSA Encryption</div>
        <form method="post" action="">
            <div class="links">
                <p>Client Key : </p>
                <textarea name="clientKey" rows="10">{{$data["clientKey"]}}</textarea>
            </div>
            <div class="links">
                <p>Decrypted String : </p>
                <textarea name="decrypted" rows="10">{{$data["decrypted"]}}</textarea>
            </div>
            <div class="links">
                <p>Encrypted String : </p>
                <textarea name="encrypted" rows="10">{{$data["encrypted"]}}</textarea>
            </div>
            <div class="button">
                <button type="submit">Process</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
