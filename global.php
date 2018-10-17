<?php

define('DB_HOST', "<SERVIDOR>");
define('DB_USER', "<USUARIO>");
define('DB_PASSWORD', "<SENHA>");
define('DB_NAME', "<BANCO>");
define('DB_DRIVER', "sqlsrv");

define ( 'HOST_EMAIL', '<SERVIDOR>' );
define ( 'USER_EMAIL', '<USUARIO>' );
define ( 'PASS_EMAIL', '<SENHA>' );
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
