<?php
class Util {
	function Util() {
	}
	
	static function getMsg($mod) {
		
		switch ($mod) {
			case "S" :
				$msg = "A operação foi realizada com sucesso.";
				break;
			case "E" :
				$msg = "A operação não foi realizada com sucesso!";
				break;
			case "N" :
				$msg = "Nenhum registro foi encontrado, verifique os dados informados!";
				break;
		}
		
		return $msg;
	
	}
	
	static function getDataAtual() {
		return date ( "Y-m-d" );
	}
	
	static function getHoraAtual() {
		return date ( "H:i:s" );
	}
	
	static function converteData($data) {
		//return $this->converteAmdParaDma($data);
		return Util::converteAmdParaDma ( $data );
	}
	
	static function converteDataBanco($data) {
		//return $this->converteDmaParaAmd($data);
		return Util::converteDmaParaAmd ( $data );
	}
	
	static function converteMdaParaDma(&$data) {
		$data = substr ( $data, 0, 10 );
		list ( $mes, $dia, $ano ) = explode ( "/", $data );
		$data = $dia . "/" . $mes . "/" . $ano;
		return $data;
	}
	
	static function converteDmaParaMda(&$data) {
		$data = substr ( $data, 0, 10 );
		list ( $dia, $mes, $ano ) = explode ( "/", $data );
		$data = $mes . "/" . $dia . "/" . $ano;
		return $data;
	}
	
	static function converteDmaParaAmd(&$data) {
		$data = substr ( $data, 0, 10 );
		list ( $dia, $mes, $ano ) = explode ( "/", $data );
		$data = $ano . "-" . $mes . "-" . $dia;
		return $data;
	}
	
	static function converteAmdParaDma(&$data) {
		$data = substr ( $data, 0, 10 );
		list ( $ano, $mes, $dia ) = explode ( "-", $data );
		$data = $dia . "/" . $mes . "/" . $ano;
		return $data;
	}
	
	## funÃ§Ãµes de formataÃ§Ã£o e tratamento de strings
	static function forValorBanco($valor) {
		return str_replace ( ",", ".", $valor );
	}
	
	static function forValor($valor) {
		return str_replace ( ".", ",", $valor );
	}
	
	static function forStringBanco($str) {
		$str = addslashes ( $str );
		return $str;
	}
	
	static function forString($str) {
		$str = stripslashes ( $str );
		return $str;
	}
	
	static function encode() {
		$vetParametros = func_get_args ();
		while ( $parametro = array_shift ( $vetParametros ) ) {
			$vetEncode [] .= urlencode ( $parametro );
		}
		return implode ( "|", $vetEncode );
	}
	
	static function decode($codigo) {
		$vetVarDecode = explode ( "|", $codigo );
		while ( $varDecode = urldecode ( array_shift ( $vetVarDecode ) ) ) {
			$vetVar [] = $varDecode;
		}
		return $vetVar;
	}
	
	static function iterateMenu($vetor, $atributoLabel, $atributoValor = false, $valorPadrao = false) {
		foreach ( $vetor as $objeto ) {
			$strValor = "\$objeto->get" . (($atributoValor) ? $atributoValor : $atributoLabel) . "()";
			if (is_array ( $atributoLabel ))
				for($i = 0; $i < count ( $atributoLabel ); $i ++)
					$strLabel .= "\$objeto->get" . $atributoLabel [$i] . "()" . (($i == count ( $atributoLabel ) - 1) ? '' : '." - ".');
			else
				$strLabel = "\$objeto->get" . $atributoLabel . "()";
			
			eval ( "\$strValor = $strValor;" );
			eval ( "\$strLabel = $strLabel;" );
			print "<option value='" . $strValor . "'" . (($valorPadrao) ? (($strValor == $valorPadrao) ? "selected " : "") : '') . " >" . $strLabel . "</option>\n";
			$strLabel = '';
		}
	}
	
	static function viewAgregation($pk, $nameObject, $property) {
		if (is_array ( $pk ))
			$pk = implode ( ",", $pk );
		eval ( "\$object = Fachada::get$nameObject($pk);" );
		if ($object)
			eval ( "\$valor = \$object->get$property();" );
		return $valor;
	}
	
	// METODOS ADICIONADOS	
	

	static function validar_data($data) {
		
		if (strstr ( $data, '/' )) {
			list ( $dia, $mes, $ano ) = explode ( "/", $data );
		} else {
			list ( $ano, $mes, $dia ) = explode ( "-", $data );
		}
		
		if (checkdate ( $mes, $dia, $ano )) {
			return true;
		} else {
			return false;
		}
	}
	
	static function validar_email($email) {
		if (filter_var ( $email, FILTER_VALIDATE_EMAIL ) == false) {
			return false;
		} else {
			return true;
		}
	}
	
	static function validar_cpf($cpf) {
		$erro = false;
		$aux_cpf = "";
		for($j = 0; $j < strlen ( $cpf ); $j ++)
			if (substr ( $cpf, $j, 1 ) >= "0" and substr ( $cpf, $j, 1 ) <= "9")
				$aux_cpf .= substr ( $cpf, $j, 1 );
		if (strlen ( $aux_cpf ) != 11)
			$erro = true;
		else {
			$cpf1 = $aux_cpf;
			$cpf2 = substr ( $cpf, - 2 );
			$controle = "";
			$start = 2;
			$end = 10;
			for($i = 1; $i <= 2; $i ++) {
				$soma = 0;
				for($j = $start; $j <= $end; $j ++)
					$soma += substr ( $cpf1, ($j - $i - 1), 1 ) * ($end + 1 + $i - $j);
				if ($i == 2)
					$soma += $digito * 2;
				$digito = ($soma * 10) % 11;
				if ($digito == 10)
					$digito = 0;
				$controle .= $digito;
				$start = 3;
				$end = 11;
			}
			if ($controle != $cpf2)
				$erro = true;
		}
		return $erro;
	}
	
	static function is_cnpj($str) {
		if (! preg_match ( '|^(\d{2,3})\.?(\d{3})\.?(\d{3})\/?(\d{4})\-?(\d{2})$|', $str, $matches ))
			return false;
		
		array_shift ( $matches );
		
		$str = implode ( '', $matches );
		if (strlen ( $str ) > 14)
			$str = substr ( $str, 1 );
		
		$sum1 = 0;
		$sum2 = 0;
		$sum3 = 0;
		$calc1 = 5;
		$calc2 = 6;
		
		for($i = 0; $i <= 12; $i ++) {
			$calc1 = $calc1 < 2 ? 9 : $calc1;
			$calc2 = $calc2 < 2 ? 9 : $calc2;
			
			if ($i <= 11)
				$sum1 += $str [$i] * $calc1;
			
			$sum2 += $str [$i] * $calc2;
			$sum3 += $str [$i];
			$calc1 --;
			$calc2 --;
		}
		
		$sum1 %= 11;
		$sum2 %= 11;
		
		return ($sum3 && $str [12] == ($sum1 < 2 ? 0 : 11 - $sum1) && $str [13] == ($sum2 < 2 ? 0 : 11 - $sum2)) ? true : false;
	}
	
	static function validar_Cep($cep) {
		$cep = trim ( $cep );
		$cep = Util::somente_numeros ( $cep );
		
		if (! preg_match ( "/^[0-9]{8}$/i", $cep )) {
			return false;
		} else {
			return true;
		}
	
	}
	
	static function validar_telefone($telefone) {
		$telefone = trim ( $telefone );
		$telefone = Util::somente_numeros ( $telefone );
		$telefone = ltrim ( $telefone, 0 );
		
		if (! preg_match ( "/^[0-9]{10}$/i", $telefone )) {
			return false;
		} else {
			return true;
		}
	}
	
	static function retira_acentos($texto) {
		$array1 = array ("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" );
		$array2 = array ("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C" );
		return str_replace ( $array1, $array2, $texto );
	}
	
	static function cortaFrase($frase, $qtde_letras = 12) {
		/*
    *
    * $frase = string com o conteúdo a ser formatada
    * $qtde_letras = quantidade de caracteres máximo
    *
    *
    * OBS:
    * CONSIDERAR A RETICÊNCIAS ADICIONADA CASO A STRING
    * SEJA MAIOR QUE A QUANTIDADE MÁXIMA DE CARACTERES
    *
    */
		
		$p = explode ( ' ', $frase );
		$c = 0;
		$cortada = '';
		
		foreach ( $p as $p1 ) {
			if ($c < $qtde_letras && ($c + strlen ( $p1 ) <= $qtde_letras)) {
				$cortada .= ' ' . $p1;
				$c += strlen ( $p1 ) + 1;
			} else {
				break;
			}
		}
		
		return strlen ( $cortada ) < $qtde_letras ? $cortada . '...' : $cortada;
	}
	
	static function somente_numeros($string) {
		return preg_replace ( "([^0-9])", "", $string );
	}
	
	static function retornar_arquivos_diretorio($diretorio) {
		
		$arquivos = array ();
		
		if (is_dir ( $diretorio )) {
			if ($dir = opendir ( $diretorio )) {
				while ( false !== ($arq = readdir ( $dir )) ) {
					if (is_file ( $diretorio . $arq )) {
						$arquivos [] = $arq;
						return $arquivos;
					}
				}
			}
		}
	}
	
	static function validaInscricaoEstadual($ie) {
		if (strlen ( $ie ) != 9) {
			return false;
		}
		
		if (substr ( $ie, 0, 2 ) != '15') {
			return false;
		}
		
		$nro = $ie;
		$b = 9;
		$soma = 0;
		
		for($i = 0; $i <= 7; $i ++) {
			$soma += $nro [$i] * $b;
			$b --;
		}
		
		$i = $soma % 11;
		
		if ($i <= 1) {
			$dig = 0;
		} else {
			$dig = 11 - $i;
			if ($dig == $nro [8]) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	static function dias_feriados($ano = null) {
		if ($ano === null) {
			$ano = intval ( date ( 'Y' ) );
		}
		
		$pascoa = easter_date ( $ano ); // Limite de 1970 ou após 2037 da easter_date PHP consulta http://www.php.net/manual/pt_BR/function.easter-date.php
		$dia_pascoa = date ( 'j', $pascoa );
		$mes_pascoa = date ( 'n', $pascoa );
		$ano_pascoa = date ( 'Y', $pascoa );
		
		$feriados = array (// Tatas Fixas dos feriados Nacionail Basileiras
mktime ( 0, 0, 0, 1, 1, $ano ), // Confraternização Universal - Lei nº 662, de 06/04/49
mktime ( 0, 0, 0, 4, 21, $ano ), // Tiradentes - Lei nº 662, de 06/04/49
mktime ( 0, 0, 0, 5, 1, $ano ), // Dia do Trabalhador - Lei nº 662, de 06/04/49
mktime ( 0, 0, 0, 9, 7, $ano ), // Dia da Independência - Lei nº 662, de 06/04/49
mktime ( 0, 0, 0, 10, 12, $ano ), // N. S. Aparecida - Lei nº 6802, de 30/06/80
mktime ( 0, 0, 0, 11, 2, $ano ), // Todos os santos - Lei nº 662, de 06/04/49
mktime ( 0, 0, 0, 11, 15, $ano ), // Proclamação da republica - Lei nº 662, de 06/04/49
mktime ( 0, 0, 0, 12, 25, $ano ), // Natal - Lei nº 662, de 06/04/49


		// These days have a date depending on easter
		mktime ( 0, 0, 0, $mes_pascoa, $dia_pascoa - 48, $ano_pascoa ), //2ºferia Carnaval
mktime ( 0, 0, 0, $mes_pascoa, $dia_pascoa - 47, $ano_pascoa ), //3ºferia Carnaval	
mktime ( 0, 0, 0, $mes_pascoa, $dia_pascoa - 2, $ano_pascoa ), //6ºfeira Santa  
mktime ( 0, 0, 0, $mes_pascoa, $dia_pascoa, $ano_pascoa ), //Pascoa
mktime ( 0, 0, 0, $mes_pascoa, $dia_pascoa + 60, $ano_pascoa ) ); //Corpus Cirist
		

		sort ( $feriados );
		
		return $feriados;
	}

}

function printvar($args) {
	$args = func_get_args ();
	$dbt = debug_backtrace ();
	$linha = $dbt [0] ['line'];
	$arquivo = $dbt [0] ['file'];
	echo "<fieldset style='border:1px solid; border-color:#F00;background-color:#FFF000;legend'><b>Arquivo:</b>" . $arquivo . "<b><br>Linha:</b><legend><b>Debug On : printvar ( )</b></legend> $linha</fieldset>";
	
	$args = func_get_args ();
	foreach ( $args as $idx => $arg ) {
		echo "<fieldset style='background-color:#CBA; border:1px solid; border-color:#00F;'><legend><b>ARG[$idx]</b></legend>";
		echo "<pre style='background-color:#CBA; width:100%; heigth:100%;'>";
		print_r ( $arg );
		echo "</pre>";
		echo "</fieldset><br>";
	}
}

function printvardie($args) {
	$args = func_get_args ();
	$dbt = debug_backtrace ();
	$linha = $dbt [0] ['line'];
	$arquivo = $dbt [0] ['file'];
	echo "<fieldset style='border:1px solid; border-color:#F00;background-color:#FFF000;legend'><b>Arquivo:</b>" . $arquivo . "<b><br>Linha:</b><legend><b>Debug On : printvardie ( )</b></legend> $linha</fieldset>";
	
	foreach ( $args as $idx => $arg ) {
		echo "<fieldset style='background-color:#CBA; border:1px solid; border-color:#00F;'><legend><b>ARG[$idx]</b></legend>";
		echo "<pre style='background-color:#CBA; width:100%; heigth:100%;'>";
		print_r ( $arg );
		echo "</pre>";
		echo "</fieldset><br>";
	}
	die ();
}

function mostra_erros() {
	print ini_set ( "display_errors", true );
}

?>