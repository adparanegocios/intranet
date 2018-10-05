<?php

error_reporting ( 0 );
ini_set ( 'display_errors', 0 );
set_time_limit ( 0 );

include_once 'global.php';
include_once 'conexao.php';
include_once 'classes/phpmailer/class.phpmailer.php';

//$dir = "http://10.10.15.150/refeicaoimport/importar/";
$dir = "http://10.10.15.150/refeicaoimport/";
$nomes = array ("1_DIARIODOPARA.txt", "2_RBATV.txt", "4_99FM.txt", "5_RADIOCLUBE.txt", "6_SISTEMACLUBE.txt", "8_DIARIOONLINE.txt" );
$log = array ();
$apagar = array ();

foreach ( $nomes as $n ) {
	$colaboradores = array ();
	list ( $codigo, $nome ) = explode ( "_", $n );
	
	$sql = "
				SELECT DISTINCT
					PF.CHAPA AS [CHAPA]
				FROM PFFINANC PF (NOLOCK)
				WHERE
					PF.CODCOLIGADA = $codigo AND
					PF.ANOCOMP = (SELECT P.ANOCOMP FROM PPARAM P (NOLOCK) WHERE P.CODCOLIGADA = PF.CODCOLIGADA) AND
					PF.MESCOMP = (SELECT P.MESCOMP FROM PPARAM P (NOLOCK) WHERE P.CODCOLIGADA = PF.CODCOLIGADA) AND
					PF.NROPERIODO = ((SELECT P.PERIODO FROM PPARAM P (NOLOCK) WHERE P.CODCOLIGADA = PF.CODCOLIGADA)) AND
					NOT EXISTS (
						SELECT 
							* 
						FROM PFFINANC A (NOLOCK)
						WHERE
							A.CODCOLIGADA = PF.CODCOLIGADA AND
							A.ANOCOMP = PF.ANOCOMP AND
							A.MESCOMP = PF.MESCOMP AND
							A.NROPERIODO = PF.NROPERIODO AND
							A.CHAPA = PF.CHAPA AND
							A.CODEVENTO = '0189'
					)
				";
	
	$rs = $db->Execute ( $sql );
	
	if ($rs) {
		while ( $o = $rs->FetchNextObject () ) {
			$colaboradores [] = str_replace ( " ", "", $o->CHAPA );
		}
	}
	
	$arquivo = file ( $dir . $n );
	
	if (count ( $arquivo ) > 0) {
		foreach ( $arquivo as $a ) {
			list ( $codcoligada, $chapa, $anocomp, $mescomp, $nroperiodo, $codevento, $dtpagto, $hora, $ref, $valor, $valororiginal, $verbaferias, $alteradomanual, $reccreatedby, $reccreatedon, $recmodifiedby, $recmodifiedon ) = explode ( ";", $a );
			$codcoligada = str_replace ( " ", "", $codcoligada );
			$chapa = str_replace ( " ", "", $chapa );
			$anocomp = str_replace ( " ", "", $anocomp );
			$mescomp = str_replace ( " ", "", $mescomp );
			$nroperiodo = str_replace ( " ", "", $nroperiodo );
			$codevento = str_replace ( " ", "", $codevento );
			$dtpagto = str_replace ( " ", "", $dtpagto );
			$hora = str_replace ( " ", "", $hora );
			$ref = str_replace ( " ", "", $ref );
			$valor = str_replace ( " ", "", $valor );
			$valororiginal = str_replace ( " ", "", $valororiginal );
			$verbaferias = str_replace ( " ", "", $verbaferias );
			$alteradomanual = str_replace ( " ", "", $alteradomanual );
			$reccreatedby = str_replace ( " ", "", $reccreatedby );
			$reccreatedon = str_replace ( " ", "", $reccreatedon );
			$recmodifiedby = str_replace ( " ", "", $recmodifiedby );
			$recmodifiedon = str_replace ( " ", "", $recmodifiedon );
			
			if (in_array ( $chapa, $colaboradores )) {
				$sql = "
							SET DATEFORMAT ymd;
							INSERT INTO PFFINANC ( 
								CODCOLIGADA, 
								CHAPA, 
								ANOCOMP, 
								MESCOMP, 
								NROPERIODO, 
								CODEVENTO, 
								DTPAGTO, 
								HORA, 
								REF, 
								VALOR, 
								VALORORIGINAL, 
								VERBAFERIAS, 
								ALTERADOMANUAL, 
								RECCREATEDBY, 
								RECCREATEDON, 
								RECMODIFIEDBY, 
								RECMODIFIEDON ) VALUES ( 
								$codcoligada, 
								'$chapa', 
								$anocomp, 
								$mescomp, 
								$nroperiodo, 
								'$codevento', 
								'$dtpagto', 
								$hora, 
								$ref, 
								$valor, 
								$valororiginal, 
								$verbaferias, 
								$alteradomanual, 
								'mestre', 
								CONVERT ( DATETIME, CONVERT ( VARCHAR, GETDATE(), 120) ), 
								'mestre', 
								CONVERT ( DATETIME, CONVERT ( VARCHAR, GETDATE(), 120) ));";
				$db->Execute ( $sql );
				$apagar [] = $n;
			} else {
				$log [$codcoligada] .= "$chapa;";
			}
		}
	}
}

if (count ( $log ) > 0) {
	$timestamp = mktime ( date ( "H" ) - 3, date ( "i" ), date ( "s" ), date ( "m" ), date ( "d" ), date ( "Y" ), 0 );
	$data = gmdate ( "d/m/Y H:i:s", $timestamp );
	
	$mail = new PHPMailer ();
	$mail->SetLanguage ( "br", "../classes/phpmailer/language/" );
	$mail->IsSMTP ();
	$mail->Host = HOST_EMAIL;
	$mail->SMTPAuth = true;
	$mail->Port = PORT_EMAIL;
	$mail->Username = USER_EMAIL;
	$mail->Password = PASS_EMAIL;
	$mail->From = USER_EMAIL;
	$mail->AddReplyTo ( USER_EMAIL );
	$mail->FromName = "BI";
	$mail->WordWrap = 50;
	$mail->IsHTML ( true );
	$mail->Subject = "CHAPAS QUE NÃO IMPORTARAM EM $data.";
	
	foreach ( $log as $i => $c ) {
		
		if ($i == 1) {
			$conteudo .= "$i - " . utf8_encode ( "DIÁRIO DO PARÁ" ) . "<br /><hr> $c <br /><hr>";
		} elseif ($i == 2) {
			$conteudo .= "$i - RBA TV<br /><hr> $c <br /><hr>";
		} elseif ($i == 3) {
			$conteudo .= "$i - " . utf8_encode ( "DIÁRIO FM" ) . "<br /><hr> $c <br /><hr>";
		} elseif ($i == 4) {
			$conteudo .= "$i - 99 FM<br /><hr> $c <br /><hr>";
		} elseif ($i == 5) {
			$conteudo .= "$i - " . utf8_encode ( "RÁDIO CLUBE" ) . "<br /><hr> $c <br /><hr>";
		} elseif ($i == 6) {
			$conteudo .= "$i - SISTEMA CLUBE<br /><hr> $c <br /><hr>";
		} elseif ($i == 8) {
			$conteudo .= "$i - " . utf8_encode ( "DIÁRIO ONLINE" ) . "<br /><hr> $c <br /><hr>";
		}
	
	}
	
	$mail->Body = utf8_decode ( $conteudo );
	//$mail->AddAddress ( 'adeilson@diarioonline.com.br' );
	$mail->AddAddress ( 'desenv@diarioonline.com.br' );
	
	$mail->Send ();
	$mail->SmtpClose ();
}

$apagar = array_unique ( $apagar, SORT_REGULAR );

foreach ( $apagar as $a ) {
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, "http://intranet.rbadecomunicacao.com.br/painel/relatoriorefeicaoconsolidado/gerar?acao=del&arquivo=$a" );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_exec ( $ch );
	curl_close ( $ch );
}

?>