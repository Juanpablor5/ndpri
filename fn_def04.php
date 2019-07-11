<?php
	session_start();
	require_once('../libs/auth.php');
	require('../libs/conexionbd.lib.php');
	require('../libs/header.php');

	$idusuario = $_SESSION['idusuario'];

	$unixtime = time();
	$hoy = date('Y-m-d',$unixtime);
	$anno = date("Y");
	$mess = date("m");
	$diaa = date("d");
	
	$sql = "select cod_empl from auth_users where id = ".$idusuario;
    $rst = mysql_query($sql) or die ('Error: auth_users' . mysql_error());
    $rw = mysql_fetch_array($rst);
	
	$codempl = $rw['cod_empl'];

	function suma_fechas($date, $dd=0, $mm=0, $yy=0, $hh=0, $mn=0, $ss=0){

		$date_r = getdate(strtotime($date));
		$date_result = date("Y-m-d h:i:s", mktime(($date_r["hours"]+$hh),($date_r["minutes"]+$mn),($date_r["seconds"]+$ss),($date_r["mon"]+$mm),($date_r["mday"]+$dd),($date_r["year"]+$yy)));
		return $date_result;

	}
  
	function restaFechas($dFecIni, $dFecFin)
	{

		$separa1 = strtotime($dFecIni);
		//defino fecha 1
		$ano1 = date("Y",$separa1);
		$mes1 = date("m",$separa1);
		$dia1 = date("d",$separa1);

		//defino fecha 2
		$separa2 = strtotime($dFecFin);
		//defino fecha 1
		$ano2 = date("Y",$separa2);
		$mes2 = date("m",$separa2);
		$dia2 = date("d",$separa2);

		//calculo timestam de las dos fechas
		$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
		$timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

		//resto a una fecha la otra
		$segundos_diferencia = $timestamp1 - $timestamp2;
		//echo $segundos_diferencia;

		//convierto segundos en d�as
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

		//obtengo el valor absoulto de los d�as (quito el posible signo negativo)
		$dias_diferencia = abs($dias_diferencia);

		//quito los decimales a los d�as de diferencia
		$dias_diferencia = floor($dias_diferencia);

		//echo $dias_diferencia;
		return $dias_diferencia;
	}																			
  
	function cambiacoma($text){
		 $caracteres = array(',',' ','.',);
		 for($n=0;$n<strlen($text);$n++){
		  if(in_array(substr($text,$n,1),$caracteres)){
		   $new_char = '';
		  }else{
		   $new_char = substr($text,$n,1);
		  }
		 $new_text.=$new_char;
		}
		return $new_text;
	}
	
	if (isset($_POST['crearform'])) {
	
		$fecform = $_POST['fec_form'];
		$valst01 = $_POST['val_st01'];
		$valin04 = $_POST['val_in04'];
		$valin05 = $_POST['val_in05'];
		$idtipform = $_POST['idtipform'];

		
		if ($idtipform > 0) {
			$sqlins = "INSERT INTO dp_mafor 
			          ( tip_form,
						fec_form,
						val_st01,
						val_in04,
						val_in05,
						fec_real,
						cod_empl)
					  VALUES 
					  ( $idtipform,
						'$fecform',
						'$valst01',
						'$valin04',
						'$valin05',						
						NOW(),
						$codempl)";
			$rstins = mysql_query($sqlins) or die ('Error INSERT dp_mafor : ' . mysql_error());
		}
		
		$idformato = mysql_insert_id();
	
		header('Location: dp_def04.php?idtipform='.$idtipform.'&idf='.$idformato.'');
	
	}
	
	if (isset($_POST['modifform'])) {
	
		$fecform = $_POST['fec_form'];
		$valst01 = $_POST['val_st01'];
		$valin01 = $_POST['val_in01'];
		$valin02 = $_POST['val_in02'];
		$valfc01 = $_POST['val_fc01'];
		$idtipform = $_POST['idtipform'];
		$idf = $_POST['idf'];

		
		if ($idf > 0 && $idtipform > 0) {
			$sqlupd = "UPDATE dp_mafor SET 
						val_st01 = '$valst01'
					  WHERE id = ".$idf;
			$rstupd = mysql_query($sqlupd) or die ('Error UPDATE dp_mafor : ' . mysql_error());
		}
	
		header('Location: dp_def04.php?idtipform='.$idtipform.'&idf='.$idf.'');
	
	}
	
	if (isset($_POST['insertregi'])) {
		
		$codpara = $_POST['cod_para'];
		$codmues = $_POST['cod_mues'];
		$muedupl = $_POST['mue_dupl'];
		$dilu02 = $_POST['dilu_02'];
		$idtipform = $_POST['idtipform'];
		$idf = $_POST['idf'];
		
		$sql = "SELECT (secuencial + 1) as secuencial from lms_secuencial where nomtabla = 'dp_def04' and anno = ".$anno;
		$rst = mysql_query($sql) or die ('Error: lms_secuencial' . mysql_error());
		$rw = mysql_fetch_array($rst);
		
		$numrela = $rw['secuencial'];
		
			$valst01 = $_POST['val_hor'];
			$valin01 = $_POST['vol_alic'];
			$valin02 = $_POST['vol_alic1'];
			$valin03 = $_POST['vol_afo1'];
			$valin04 = $_POST['con_tit'];
			$valin05 = $_POST['vol_tit1'];
			$valin06 = $_POST['vol_tit2'];
			$observa = $_POST['val_obs'];
			
			if ($muedupl != 1) $muedupl = 0;
		
			if ($idtipform > 0 && $idf > 0) {
				$sqlins = "INSERT INTO dp_def04 
						  ( cod_maes,
							cod_para,
							cod_mues,
							mue_dupl,
							dilu_02,
							val_st01,
							val_in01,
							val_in02,
							val_in03,
							val_in04,
							val_in05,
							val_in06,
							obs_erva,
							emp_rep1,
							fec_rep1 )
						  VALUES 
						  ( $idf,							
							'$codpara',
							'$codmues',
							'$muedupl',
							'$dilu02',
							'$valst01',
							'$valin01',
							'$valin02',
							'$valin03',
							'$valin04',
							'$valin05',
							'$valin06',
							'$observa',
							'$codempl',
							NOW())";
				$rstins = mysql_query($sqlins) or die ('Error INSERT dp_def04 : ' . mysql_error());
			}
			
			//$idformato = mysql_insert_id();
				
		$sqlupd = "UPDATE lms_secuencial SET secuencial = ".$numrela." WHERE nomtabla = 'dp_def04'";
		$rstupd = mysql_query($sqlupd) or die ('Error: lms_secuencial' . mysql_error());

		header('Location: dp_def04.php?idtipform='.$idtipform.'&idf='.$idf.'');
	
	}
	
	if (isset($_POST['verificar'])) {

		$a = $_POST['emp_ver1'];
		$dqo = $_POST['dqo'];
		$idtipform = $_POST['idtipform'];
		$idf = $_POST['idf'];

		if (!empty($a)){
		  foreach($a as $v) {

			$partes = explode('/', $v);
			$id = $partes[0];

			$sqlapr = "UPDATE dp_def04 SET emp_ver1 = $codempl, 
										   fec_ver1 = NOW(), 
										   dqo='".$_POST['dqo'][$id]."'
					   WHERE id = ".$id;
			//echo $sqlapr;
			$rstapr = mysql_query($sqlapr) or die ('Error UPDATE dp_def04: ' . mysql_error());

		  }
		  
		}
		
		header('Location: dp_def04.php?idtipform='.$idtipform.'&idf='.$idf.'');
	}
	
	if (isset($_POST['actualizar'])) {
	
		$idtipform = $_POST['idtipform'];
		$idf = $_POST['idf'];

		$a = $_POST['edi_regi'];
		//print_r($_POST['edi_regi']);
		
		//echo "<br>";
		
		if (!empty($a)){
		  foreach($a as $v) {

		  $partes = explode('/', $v);
		  $id = $partes[0];
						
			$sqlapr = "UPDATE dp_def04 SET 	val_st01 = '".$_POST['val_st01'][$id]."', 
											val_in01 = '".$_POST['val_in01'][$id]."', 
											val_in02 = '".$_POST['val_in02'][$id]."', 
											val_in03 = '".$_POST['val_in03'][$id]."', 
											val_in06 = '".$_POST['val_in06'][$id]."',
											val_in07 = '".$_POST['val_in07'][$id]."',
											val_in08 = '".$_POST['val_in08'][$id]."', 
											obs_erva = '".$_POST['obs_erva'][$id]."',  
											emp_mod1 = $codempl, 
											fec_mod1 = NOW()
					   WHERE id = ".$id;
			//echo $sqlapr;
			$rstapr = mysql_query($sqlapr) or die ('Error UPDATE dp_def04 actualizar: ' . mysql_error());

		  }
		  
		}
	
		header('Location: dp_def04.php?idtipform='.$idtipform.'&idf='.$idf.'');
	
	}
	
	if (isset($_POST['reportar'])) {
	
		$idtipform = $_POST['idtipform'];
		$idf = $_POST['idf'];

		$a = $_POST['rep_regi'];
		//print_r($_POST['rep_regi']);
		
		//echo "<br>";
		
		if (!empty($a)){
		  foreach($a as $v) {

		  $partes = explode('/', $v);
		  $id = $partes[0];
			
			$sqlapr = "UPDATE dp_def04 SET 	val_in07 = '".$_POST['val_in07'][$id]."',
											val_in08 = '".$_POST['val_in08'][$id]."', 
											obs_erva = '".$_POST['obs_erva'][$id]."',  
											emp_rep2 = $codempl, 
											fec_rep2 = NOW()
					   WHERE id = ".$id;
			$rstapr = mysql_query($sqlapr) or die ('Error UPDATE dp_def04 reporte: ' . mysql_error());

		  }
		  
		}

		header('Location: dp_def04.php?idtipform='.$idtipform.'&idf='.$idf.'');
	
	}
	
	
?>