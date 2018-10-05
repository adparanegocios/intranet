<?php

include_once 'global.php';
require_once 'classes/adodb5/adodb.inc.php';

try {
	
	$db = ADONewConnection ( 'odbc_mssql' );
	$dsn = "Driver={SQL Server};Server=" . HOST_BANCO . ";Database=" . NAME_BANCO . ";";
	$conexao = $db->Connect ( $dsn, USER_BANCO, PASS_BANCO );
	
	function &fetch_array(&$rs) {
		$numeroColunas = $rs->FieldCount ();
		while ( ! $rs->EOF ) {
			for($i = 0; $i < $numeroColunas; $i ++) {
				$coluna = $rs->FetchField ( $i );
				$nomeColuna = strtolower ( $coluna->name );
				$tipoColuna = $rs->MetaType ( $coluna->type );
				if ($tipoColuna == 'D') {
					$vetor ["$nomeColuna"] = $this->converteData ( $rs->fields [$i] );
				} else {
					$vetor ["$nomeColuna"] = $rs->fields [$i];
				}
			}
			$vetorRegistro [] = $vetor;
			$rs->MoveNext ();
		}
		return $vetorRegistro;
	}

} catch ( Exception $e ) {
	echo "<h1 align = 'center'>Banco de Dados indisponível. Tente novamente mais tarde, por favor.</h1>'";
	exit ();
}

?>