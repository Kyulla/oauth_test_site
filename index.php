<!DOCTYPE html>
<html lang="it">
  <head>
	<link href="./style.css" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" href=""/>
	<meta name="description" content="OAuth login test">
	<meta name="keywords" content="OAuth, GitHub, login, test">
	<meta name="author" content="Alessandro Colla">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8">
	<title>OAuth login</title>
  </head>
  <body>
        <?php
            session_start();

            if(isset($_GET["code"])){
                $code = $_GET["code"];
                $client_id = "9b4c8e814163f84f29a1";
                $client_secret = "4d7f37db0cd56f36b0f1fe1c143b654385730e71";

                $token_request = curl_init("https://github.com/login/oauth/access_token");
                curl_setopt($token_request, CURLOPT_POST, true);
                curl_setopt($token_request, CURLOPT_POSTFIELDS, http_build_query([
                    "client_id" => $client_id,
                    "client_secret" => $client_secret,
                    "code"=> $code,
                ]));

                curl_setopt($token_request, CURLOPT_HTTPHEADER, ["Accept: application/json", "User-Agent: Simple login test"]);
                curl_setopt($token_request, CURLOPT_RETURNTRANSFER, true);

                $token_response = curl_exec($token_request);
                $token_data = json_decode($token_response, true);

                if(isset($token_data["access_token"])){
                    $access_token = $token_data["access_token"];
                    $user_request = curl_init("https://api.github.com/user");
                    curl_setopt($user_request, CURLOPT_HTTPHEADER,[
                        "Authorization: token ". $access_token,
                        "User-Agent: Simple login test",
                    ]);

                    curl_setopt($user_request, CURLOPT_RETURNTRANSFER, true);

                    $user_response = curl_exec($user_request);

                    if($user_response == false){
                        echo("Errore cUrl:". curl_error($user_request));
                    }

                    $user_data = json_decode($user_response, true);

                    if(isset($user_data["login"])){
                        echo("<h3>Benvenuto ". $user_data["login"]. "</h3><a href='./'><button>Log out</button>");
                    }
                }
                else{
                    echo("Errore con il token di accesso ");
                }
            }
            if(!isset($user_data["login"])){
                echo("<a href='https://github.com/login/oauth/authorize?client_id=9b4c8e814163f84f29a1&scope=read:user'><button>Log in</button></a>");
            }
        ?>
  </body>
</html>