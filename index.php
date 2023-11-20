<?php
require_once './vendor/autoload.php';

use Instagram\FacebookLogin\FacebookLogin;

$config = array( // instantiation config params
    'app_id' => '<FB_APP_ID>', // facebook app id
    'app_secret' => '<FB_APP_SECRET>', // facebook app secret
);

// uri facebook will send the user to after they login
$redirectUri = 'https://path/to/fb/login/redirect.php';

$permissions = array( // permissions to request from the user
    'instagram_basic',
    'instagram_content_publish', 
    'instagram_manage_insights', 
    'instagram_manage_comments',
    'pages_show_list', 
    'ads_management', 
    'business_management', 
    'pages_read_engagement'
);

// instantiate new facebook login
$facebookLogin = new FacebookLogin( $config );
// display login dialog link
echo '<a href="' . $facebookLogin->getLoginDialogUrl( $redirectUri, $permissions ) . '">' .
    'Log in with Facebook' .
'</a>';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Instagram</title>
    <link href="./css/app.css">
    <script src="./js/app.js"></script>
</head>
<body>
    <main class="container">
        <div class="grid-item">
            <h1>Login Instagram</h1>
            <p>Click the button below to login to Instagram</p>
            <a href="<?php echo $facebookLogin->getLoginDialogUrl( $redirectUri, $permissions ); ?>" class="btn btn-primary">Log in with Facebook</a>
        </div>
    </main>
</body>
</html>