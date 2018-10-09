<?php

define('DB_HOST', "10.10.15.66");
define('DB_USER', "sa");
define('DB_PASSWORD', "RBA13#");
define('DB_NAME', "CorporeRM");
define('DB_DRIVER', "sqlsrv");

define ( 'HOST_EMAIL', 'mail.gruporba.com.br' );
define ( 'USER_EMAIL', 'rm@rbadecomunicacao.com.br' );
define ( 'PASS_EMAIL', 'RBA13#' );
define ( 'PORT_EMAIL', '25' );
define ( 'PERCENTAGEM_LIMITE', '80' );

require_once "conexao.php";
require_once "classes/phpmailer/src/PHPMailer.php";
require_once "classes/phpmailer/src/SMTP.php";
require_once "classes/phpmailer/class.phpmailer.php";

try{
    $conexao = Conexao::getConnection();
}catch(Exception $e){
    echo $e->getMessage();
    exit;
}

?>