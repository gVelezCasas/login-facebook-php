<?php
session_start();

require_once './vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); 
$dotenv->load();
$fb = new Facebook\Facebook([
    'app_id' => $_ENV['FB_APP_ID'],
    'app_secret' => $_ENV['FB_APP_SECRET'],
    'default_graph_version' => 'v18.0',
    ]);
  
  $helper = $fb->getRedirectLoginHelper();
  
  $permissions = ['email','public_profile']; // Optional permissions
  $loginUrl = $helper->getLoginUrl('https://takeaway-dev.es/login-ig/', $permissions);
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
                if(isset($_GET['code'])){ 
                    try {
                        $accessToken = $helper->getAccessToken();
                      } catch(Facebook\Exceptions\FacebookResponseException $e) {
                        // When Graph returns an error
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                      } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        // When validation fails or other local issues
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                      }
                      
                      if (! isset($accessToken)) {
                        if ($helper->getError()) {
                          header('HTTP/1.0 401 Unauthorized');
                          echo "Error: " . $helper->getError() . "\n";
                          echo "Error Code: " . $helper->getErrorCode() . "\n";
                          echo "Error Reason: " . $helper->getErrorReason() . "\n";
                          echo "Error Description: " . $helper->getErrorDescription() . "\n";
                        } else {
                          header('HTTP/1.0 400 Bad Request');
                          echo 'Bad request';
                        }
                        exit;
                      } 
                      
                      // Logged in
                      echo '<h3>Access Token</h3>';
                      var_dump($accessToken->getValue());
                      
                      // The OAuth 2.0 client handler helps us manage access tokens
                      $oAuth2Client = $fb->getOAuth2Client();
                      
                      // Get the access token metadata from /debug_token
                      $tokenMetadata = $oAuth2Client->debugToken($accessToken);
                      echo '<h3>Metadata</h3>';
                      var_dump($tokenMetadata);
                      
                      // Validation (these will throw FacebookSDKException's when they fail)
                      $tokenMetadata->validateAppId($config['app_id']);
                      // If you know the user ID this access token belongs to, you can validate it here
                      //$tokenMetadata->validateUserId('123');
                      $tokenMetadata->validateExpiration();
                      
                      if (! $accessToken->isLongLived()) {
                        // Exchanges a short-lived access token for a long-lived one
                        try {
                          $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
                        } catch (Facebook\Exceptions\FacebookSDKException $e) {
                          echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                          exit;
                        }
                      
                        echo '<h3>Long-lived</h3>';
                        var_dump($accessToken->getValue());
                      }
                      
                      $_SESSION['fb_access_token'] = (string) $accessToken;
                }else{ ?>
                    <div class="info">
                        <h1>Login Instagram</h1>
                        <p>Click the button below to login to Instagram</p>
                        <a href="<?php echo $loginUrl; ?>" class="btn btn-primary">Log in with Facebook</a>
                    </div>
                <?php }
            ?>
            
        </div>
    </main>
</body>
</html>