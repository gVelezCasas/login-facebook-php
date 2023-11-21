<?php
session_start();
require_once './vendor/autoload.php';
use Instagram\FacebookLogin\FacebookLogin;
use Instagram\AccessToken\AccessToken;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); 
$dotenv->load();

//* config facebook login with facebook 3 party library *//

$config = array( // instantiation config params
  'app_id' => $_ENV['FB_APP_ID'], // facebook app id
  'app_secret' => $_ENV['FB_APP_SECRET'], // facebook app secret
  'graph_version' => $_ENV['GRAPH_VERSION'], // default is v2.10
);
$redirect_uri = 'https://takeaway-dev.es/login-facebook/';// facebook redirect uri

$permissions = array( // permissions to request from the user
  'public_profile',
  'email',
);
// instantiate new facebook login
$facebookLogin = new FacebookLogin( $config );

// instantiate our access token class
$accessToken = new AccessToken( $config );

//* config facebook login with facebook 3 party library *//
//* config facebook login with facebook sdk wrapper *//
$fb = new Facebook\Facebook([
    'app_id' => $_ENV['FB_APP_ID'],
    'app_secret' => $_ENV['FB_APP_SECRET'],
    'default_graph_version' => 'v18.0',
    ]);
  
$helper = $fb->getRedirectLoginHelper();
  
  // $permissions = ['email']; // Optional permissions
  // if(!isset($_GET['code'])){
  //   $loginUrl = $helper->getLoginUrl('https://localhost/login-facebook/', $permissions);
  // }
//* config facebook login with facebook sdk wrapper *//
?> 
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Instagram</title>
    <link href="./css/app.css" rel="stylesheet">
    <script src="./js/app.js"></script>
</head> 
<body>
    <main class="container"> 
        <div class="grid-item">
            <?php 
            
                if(isset($_GET['code']) &&  !isset($_SESSION['fb_access_token'])){ 
                    $newToken = $accessToken->getAccessTokenFromCode( $_GET['code'], $redirect_uri );
                    if ( !$accessToken->isLongLived() ) { // check if our access token is short lived (expires in hours)
                      // exchange the short lived token for a long lived token which last about 60 days
                      echo 'entra en el if no es long lived';
                      $newToken = $accessToken->getLongLivedAccessToken( $newToken['access_token'] );
                      $_SESSION['fb_access_token'] = (string) $newToken;
                    }
                    $_SESSION['fb_access_token'] = (string) $newToken['access_token'];
                      
                }else if(!isset($_SESSION['fb_access_token']) && !isset($_GET['code'])){ ?>
                    <div class="info">
                        <h1>Login Instagram</h1>
                        <p>Click the button below to login to Instagram</p>
                        <a href="<?php echo $facebookLogin->getLoginDialogUrl( $redirect_uri, $permissions ); ?>" class="btn btn-primary">Log in with Facebook</a>
                    </div>
                <?php }
                if(isset($_SESSION['fb_access_token'])){
                  try {
                    // Get the \Facebook\GraphNodes\GraphUser object for the current user.
                    // If you provided a 'default_access_token', the '{access-token}' is optional.
                    $response = $fb->get('/me?fields=email,name,first_name,picture,middle_name', $_SESSION['fb_access_token']);
                  } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    // When Graph returns an error
                    echo 'Graph returned an error: ' . $e->getMessage();
                    exit;
                  } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    // When validation fails or other local issues
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    exit;
                  }
                  
                  $me = $response->getGraphUser();
                  echo 'Logged in as ' . $me->getEmail().$me->getName().$me->getFirstName().$me->getMiddleName();
                  echo '<img src="'.$me->getPicture()->getUrl().'"/>';
                }
            ?>
            
        </div>
    </main>
</body>
</html>