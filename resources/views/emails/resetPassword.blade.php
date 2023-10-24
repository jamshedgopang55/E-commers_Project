<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; font-size:16px">
    <p>Hello , {{$formData['user']->name}}</p>
    <h1>You Have a requested to Change password</h1>
    <p>Please Click the link given blow to reset password</p>
    <a href="{{route('front.resetPassword',$formData['token'])}}">Click Here</a>
    <p>Thanks</p>
</body>

</html>
