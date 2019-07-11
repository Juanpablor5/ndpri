<?php
session_start();
require('../libs/conexionbd.lib.php');
require_once('../libs/auth.php');
require_once('./libs/principal.php');

#Validar el id del Usuario y que la Sesion este activa
if (!empty($_SESSION['idusuario']) && !empty($_SESSION['usuario'])) {

	require('./libs/header.php');
	$idusuario = $_SESSION['idusuario'];

	$sql = "SELECT idcliente, area FROM auth_users WHERE id = ".$idusuario;
	$rst = mysql_query($sql) or die ('Error: ' . mysql_error());
	$row = mysql_fetch_array($rst);

	$idcliente = $row['idcliente'];
	$area = $row['area'];
  
	$unixtime = time();
	$hoy = date('Y-m-d',$unixtime);
	$anno = date("Y");
	$mess = date("m");
	$diaa = date("d");
	$fecinicio = ($anno - 1).'-10-01';
	
	if (isset($_POST['idtipform'])) {
		$idtipform = $_POST['idtipform'];
		$idf = $_POST['idf'];
    } else {
		$idtipform = $_GET['idtipform'];
		$idf = $_GET['idf'];
    }

	if ($idf > 0) {	
		$sqlmafor = "SELECT * FROM dp_mafor WHERE id = ".$idf;
		$rstmafor = mysql_query($sqlmafor) or die ('Error: dp_mafor' . mysql_error());
		$rowmafor = mysql_fetch_array($rstmafor);
		$tipform = $rowmafor['tip_form'];
		$fecform = $rowmafor['fec_form'];
		$valst01 = $rowmafor['val_st01'];
		$valin04 = $rowmafor['val_in04'];
		$valin05 = $rowmafor['val_in05'];
		// $valfc01 = $rowmafor['val_fc01'];
		// $fecreal = $rowmafor['fec_real'];
		// $codempl = $rowmafor['cod_empl'];
		
		$sqlAudi = "SELECT * FROM lms_admglobal WHERE id = 1";
		$rstAudi = mysql_query($sqlAudi) or die ('Error: lms_admglobal' . mysql_error());
		$rowAudi = mysql_fetch_array($rstAudi);
		$estAudi = $rowAudi['est_audi'];

		$sqlVeri = "SELECT COUNT(DISTINCT id) AS cantidad FROM dp_def04 WHERE cod_maes=".$idf." AND cod_mues<>100000002 AND cod_mues<>100000004 AND cod_mues<>100000006 AND cod_mues<>100000007 AND cod_mues<>100000008 AND mue_dupl<>1";
		$rstVeri = mysql_query($sqlVeri) or die ('Error: dp_mafor' . mysql_error());
		$rowVeri = mysql_fetch_array($rstVeri);
		$numVeri = $rowVeri['cantidad'];
	}
	echo "<script type='text/javascript' src='../js/valid.js'></script>\n"; // script de validacion js
?>	

<form id="form1" name="form1" method="post" action="fn_def04.php">
  <table width="90%" border="1" align="center" cellpadding="0" cellspacing="1">
    <tr>
      <td><table width="98%" border="0" align="center" cellpadding="2" cellspacing="2">
        <tr>
          <td align="center" bgcolor="#CCCCCC"><strong>AN&#193;LISIS DE DQO - METODO SM 5220 C</strong></td>
        </tr>
        <tr>
          <td align="center" valign="middle"><strong> FECHA DE AN&#193;LISIS
            (A&Ntilde;O-MES-DIA):
            <?php			
				if (!empty($fecform)) {
					echo "<strong>".$fecform."</strong>";
				} else {
					echo '<input name="fec_form" type="date" id="fec_form" size="15"/>';
				}			
			?>
			| CODIGO DEL EQUIPO: <?php 
				if (!empty($valst01)) { 
				echo "$valst01";
				}else{
					echo "<input align='center' name='val_st01' type='text' id='val_st01' size='15' value=".$valst01.">";
				}
			?>
			<br>
			<br><br/>
			CONCENTRAC&Oacute;N TITULANTE (N): <?php
				if (!empty($valin04)) { 
					echo "$valin04";
					}else{
						echo "<input align='center' name='val_in04' type='text' id='val_in04' size='15' value=".$valin04.">";
					}
			?>
			| VOLUMEN TITULANTE BLANCO (mL) (1): <?php
				if (!empty($valin05)) { 
					echo "$valin05";
					}else{
						echo "<input align='center' name='val_in05' type='text' id='val_in05' size='15' value=".$valin05.">";
					}
			?>
			<br/>

            </strong></td>
        </tr>
        <tr>
          <td align="right" valign="middle"><br />
            <?php 
				if ($idf == 0) {
					echo '<input type="submit" name="crearform" id="crearform" value="Crear Registro Tecnico" />';
					echo "<br><br><br><a href='dp_cons_mafor.php?idtipform=55&nomform=dp_def04.php' class='mnulink'>REGRESAR</a>";
				}
			?></td>
        </tr>
        <tr>
          <td><input type="hidden" name="idtipform" id="idtipform" value="<?php echo $idtipform; ?>"/>
			<input type="hidden" name="idf" id="idf" value="<?php echo $idf; ?>"/></td>
        </tr>
      </table></td>
    </tr>
	
  </table>
  
</form>
<p>
  <?php
if ($idf > 0) {
	$sqldeta = "SELECT a.*, (SELECT nromuestra FROM lms_muestras WHERE id = a.cod_mues) AS nro_mcs, b.parametro AS nom_para	FROM dp_def04 a, lms_lstparam b
	WHERE a.cod_para = b.id AND cod_maes =".$idf."
	order BY id desc";

	$sqlNom = "SELECT cod_terc, nombre, apellido FROM adm_terc WHERE emp=1";

	$rstdeta = mysql_query($sqldeta) or die ('Error consulta principal "sqlNom": ' . mysql_error());
	$rstdetaAux = mysql_query($sqldeta) or die ('Error Select auxiliar: "rstdetaAux": ' . mysql_error());

	$rstNom = mysql_query($sqlNom) or die ('Error Select para los nombres "rstNom": ' . mysql_error());

	$nrwdeta = mysql_num_rows($rstdeta);
?>
</p>
<form id="form4" name="form4" method="post" action="fn_def04.php">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center"><p><strong>INGRESO DE RESULTADOS</strong></p>
        <p><strong>No. MUESTRA MCS: </strong>
          <select required name="cod_mues" id="cod_mues">
           
		    <?php								
				
				if ($nrwdeta<3){
					echo "<option value='100000006'>FAS</option>\n";
					$autoComVol=2;
					$autoComD1=0;
					$autoComVT=0;
				}elseif($nrwdeta>=3 && $nrwdeta<=5){
					echo "<option value='100000002'>BLANCO</option>\n";
					$autoComVol=2.5;
					$autoComD1=1;
					$autoComVT=0;
				}elseif($nrwdeta==6){
					echo "<option value='100000007'>LC 20</option>\n";
					$autoComVol=2.5;
					$autoComD1=1;
					$autoComVT="";
				}elseif($nrwdeta==7){
					echo "<option value='100000008'>BFL 50</option>\n";
					$autoComVol=2.5;
					$autoComD1=1;
					$autoComVT="";
				}elseif($nrwdeta==8){
					echo "<option value='100000004'>STD 200</option>\n";
					$autoComVol=2.5;
					$autoComD1=1;
					$autoComVT="";
				}else{
					$sqlmues = "select id, nromuestra, fecrecep
					from lms_muestras
					where fecrecep >= date_sub(CURDATE(),interval 10 day)
					order by 1 desc";			
					$rstmues = mysql_query($sqlmues) or die ('Error Select dp_patip: '.mysql_error());
					echo "<option value='' selected>Selec. muestra</option>\n";
					echo "<option value='100000002'>BLANCO</option>\n";
					echo "<option value='100000004'>STD 200</option>\n";
					echo "<option value='100000007'>LC 20</option>\n";
					echo "<option value='100000008'>BFL 50</option>\n";
					$autoComVol="";
					$autoComD1="";
					$autoComVT="";
				}
				while ($rowmues = mysql_fetch_array($rstmues)) {
					echo "<option value='$rowmues[id]' \>$rowmues[nromuestra]</option>\n";	
				}
				
				?>
          </select>
          <strong>Duplicado:</strong>
          <input name="mue_dupl" type="checkbox" id="mue_dupl" value="1" />
		  <strong>&nbsp;&nbsp;Diluci&#243;n 2:</strong>
          <input name="dilu_02" type="checkbox" id="dilu_02" value="1" />
          <strong>PARAMETRO:

          <select required name="cod_para" id="cod_para">
            <?php
				
					$sqlmatr = "SELECT a.cod_tipf, a.cod_para, b.parametro, b.unidades, b.tecnica, b.metodo
								FROM dp_patip a, lms_lstparam b
								WHERE a.cod_para = b.id and a.cod_tipf = 55
								ORDER BY a.id";			
					$rstmatr = mysql_query($sqlmatr) or die ('Error Select dp_patip: ' . mysql_error());
					echo "<option value=''>.::SELECCIONE PARAMETRO::.</option>";
					
					while ($rowmatr = mysql_fetch_array($rstmatr)) {
						echo "<option value='$rowmatr[cod_para]' \>$rowmatr[parametro]::$rowmatr[unidades]::$rowmatr[tecnica]::$rowmatr[metodo]</option>\n";	
					}
				
				?>
          </select>
        </strong> </p>
        <table width="88%" border="1" align="center" cellpadding="4" cellspacing="2">
          <tr>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Hora de la An&#225;lisis (HH:MM)</strong></td>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen de alicuota</strong></td>
            <td colspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Diluci&#243;n 1</strong></td>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Concentraci&#243;n del Titulante (N)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen Titulante Blanco (mL) (1)</strong></td>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen Titulante Muestra (mL) (2)</strong></td>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Observaciones</strong></td>
          </tr>
          <tr>
            <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen Alicuota</strong></td>
            <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen Aforo (mL)</strong></td>
          </tr>
          <tr>
            <td align="center" valign="middle"><input name="val_hor" required type="text" id="val_hor" size="5" maxlength="5" /></td>
            <td align="center" valign="middle"><input name="vol_alic" required type="text" value="<?php echo "$autoComVol" ?>" id="vol_alic" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="vol_alic1" value="<?php echo "$autoComD1" ?>" required type="text" id="vol_alic1" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="vol_afo1" value="<?php echo "$autoComD1" ?>" required type="text" id="vol_afo1" size="6" maxlength="6" /></td>
			<td align="center" valign="middle"><input name="con_tit" value="<?php echo "$valin04" ?>" required type="hidden" id="con_tit" size="10" maxlength="10" /><?php echo "$valin04" ?></td>			  
            <td align="center" valign="middle"><input name="vol_tit1" value="<?php echo "$valin05" ?>" required type="hidden" id="vol_tit1" size="9" maxlength="9" /><?php echo "$valin05" ?></td>
			<td align="center" valign="middle"><input name="vol_tit2" required type="text" value="<?php echo "$autoComVT" ?>" id="vol_tit2" size="9" maxlength="9" /></td>
            <td align="center" valign="middle"><input name="val_obs" type="text" id="val_obs" size="60" /></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td align="center"><br />
        <input type="hidden" name="idtipform" id="idtipform" value="<?php echo $idtipform; ?>"/>
        <input type="hidden" name="idf" id="idf" value="<?php echo $idf; ?>"/>
		<?php
			if ($estAudi==0) {
				echo "<input type='submit' name='insertregi' id='insertregi' value='Ingresar Resultados'/></td>";
			}else{
				if ($fecform == $hoy) {
					echo "<input type='submit' name='insertregi' id='insertregi' value='Ingresar Resultados'/></td>";
				}
			}
		?>
    </tr>
  </table>
</form>
<p>&nbsp;</p>
	
	<!-- TODO: Cambiar este mensaje al correspondiente de DQO -->
<p>* El m&#233;todo de lectura de oxigeno disuelto, tanto del dia cero como el dia 5: <strong>O</strong>=Oximetro. SM: "Standard Methods for the Examination of Water and Wastewater".</p>
	<p><strong>Cantidad de Registros: </strong><?php echo $nrwdeta;?></p>
	
	<form id="form3" name="form3" method="post" action="fn_def04.php">
	
		<table width="100%" border="1" cellspacing="0" cellpadding="2">
		  <tr>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>No. An&#225;lisis</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>No. Muestra MCS</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Hora de la Incubaci&#243;n (HH:MM)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen de alicuota</strong></td>
			<td colspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Diluci&#243;n 1</strong></td>
			<td colspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Diluci&#243;n 2</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Factor de diluci&#243;n</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Concentraci&#243;n titulante (N)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen Titulante Blanco (mL) (1)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen Titulante Muestra (mL) (2)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>DQO (mg/L)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Dupl.</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Observaciones</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Analista (Nombre)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Analista (Nombre)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Verifico (Nombre)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Actualizar</strong></td>
		  </tr>
		  <tr>
		    <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen alicuota</strong></td>
		    <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen aforo (mL)</strong></td>
			<td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen alicuota</strong></td>
		    <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen aforo (mL)</strong></td>
	      </tr>
		  
		  <tr>
		    <td colspan="16" align="right" valign="middle"><input type="hidden" name="idtipform" id="idtipform" value="<?php echo $idtipform; ?>"/>
            <input type="hidden" name="idf" id="idf" value="<?php echo $idf; ?>"/></td>
			<td align="center" valign="middle"><input type="submit" name="reportar" id="reportar" value="Reportar" /></td>
		    <td align="center" valign="middle"><input type="submit" name="verificar" id="verificar" value="Verificar" /></td>
		    <td align="center" valign="middle"><input type="submit" name="actualizar" id="actualizar" value="Actualizar" /></td>
	      </tr>
		  
		  <?php

			$i=1;
			while ($rowdeta = mysql_fetch_array($rstdeta)) {
				extract($rowdeta);
                $frm = $id;
				$swc = $i % 2; // switch para alternar color de las filas
				if ($swc == 1) {
				  $cf = '#EAEAEA';
				} else {
				  $cf = '#FFFFFF';
				}
				#Numero de Analisis 
				echo "<tr bgcolor='$cf'>";
				echo "<td align='center' valign='middle'>".$i."</td>";
					
				#No.de muestra MCS 
				if (empty($cod_mues)) { 
					echo "<td align='center' valign='middle'>&nbsp;</td>\n";
				} else {
					if($cod_mues == 100000002) {
						echo "<td align='center' valign='middle'>BLANCO</td>";
					}elseif ($cod_mues == 100000004) {
						echo "<td align='center' valign='middle'>STD 200</td>";
					}elseif ($cod_mues == 100000006) {
						echo "<td align='center' valign='middle'>FAS</td>";
					}elseif ($cod_mues == 100000007) {
						echo "<td align='center' valign='middle'>LC 20</td>";
					}elseif ($cod_mues == 100000008) {
						echo "<td align='center' valign='middle'>BFL 50</td>";
					}else {
						echo "<td align='center' valign='middle'>".$nro_mcs."</td>"; 
					}						
				}
				
				#Hora de incubacón 
				if (empty($val_st01)) { 
					echo "<td align='center' valign='middle'><input name='val_st01[".$id."]' type='text' id='val_st01[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_st01[".$id."]' type='text' id='val_st01[".$id."]' size='7' maxlength='7' value='".$val_st01."'/></td>";
				}

				#Volumen de alicuota
				if (empty($val_in01)) { 
					echo "<td align='center' valign='middle'><input name='val_in01[".$id."]' type='text' id='val_in01[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_in01[".$id."]' type='text' id='val_in01[".$id."]' size='7' maxlength='7' value='".$val_in01."'/></td>";
				}

				#Dilución 1: alicuota
				if ($val_in02==0) {
					echo "<td align='center' valign='middle'><input name='val_in02[".$id."]' type='text' id='val_in02[".$id."]' size='7' maxlength='7' value='0' /></td>\n";
				}elseif (empty($val_in02)) { 
					echo "<td align='center' valign='middle'><input name='val_in02[".$id."]' type='text' id='val_in02[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_in02[".$id."]' type='text' id='val_in02[".$id."]' size='7' maxlength='7' value='".$val_in02."'/></td>";
				}

				#Dilución 1: aforo
				if ($val_in03==0) {
					echo "<td align='center' valign='middle'><input name='val_in02[".$id."]' type='text' id='val_in02[".$id."]' size='7' maxlength='7' value='0' /></td>\n";
				}elseif (empty($val_in03)) { 
					echo "<td align='center' valign='middle'><input name='val_in03[".$id."]' type='text' id='val_in03[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_in03[".$id."]' type='text' id='val_in03[".$id."]' size='7' maxlength='7' value='".$val_in03."'/></td>";
				}

				#Dilución 2: alicuota
				if (empty($dilu_02)) { 
					echo "<td align='center' valign='middle'>&nbsp;</td>\n";
				} else { 
					if (empty($val_in07)) { 
						echo "<td align='center' valign='middle'><input name='val_in07[".$id."]' type='text' id='val_in07[".$id."]' size='7' maxlength='7' /></td>\n";
					} else { 
						echo "<td align='center' valign='middle'><input name='val_in07[".$id."]' type='text' id='val_in07[".$id."]' size='7' maxlength='7' value='".$val_in07."'/></td>";
					}
				}

				#Dilución 2: aforo
				if (empty($dilu_02)) { 
					echo "<td align='center' valign='middle'>&nbsp;</td>\n";
				} else { 
					if (empty($val_in08)) { 
						echo "<td align='center' valign='middle'><input name='val_in08[".$id."]' type='text' id='val_in08[".$id."]' size='7' maxlength='7' /></td>\n";
					} else { 
						echo "<td align='center' valign='middle'><input name='val_in08[".$id."]' type='text' id='val_in08[".$id."]' size='7' maxlength='7' value='".$val_in08."'/></td>";
					}
				}			

				#Factor de dilución
				$facDil=0;
				if (empty($val_in05) || $cod_mues==100000006) { 
					echo "<td align='center' valign='middle'></td>";
				} else {
					$facDil = (!empty($val_in07) && !empty($val_in08)) ? $val_in03/$val_in02*$val_in08/$val_in07 : $val_in03/$val_in02 ;

					echo "<td align='center' valign='middle'>".round($facDil,3)."</td>";
				}

				#Concentración titulante (N)
				if (empty($val_in04)) { 
					echo "<td align='center' valign='middle'></td>\n";
				} else { 
					echo "<td align='center' valign='middle'>".$val_in04."</td>";
				}
				
				#Volumen titulante Blanco (1)
				if (empty($val_in05)) { 
					echo "<td align='center' valign='middle'></td>\n";
				} else { 
					echo "<td align='center' valign='middle'>".$val_in05."</td>";
				}

				#Volumen titulante Muestra (2)
				if ($cod_mues==100000002 || $cod_mues==100000006) { 
					echo "<td align='center' valign='middle'></td>";
				}elseif (empty($val_in06)) { 
					echo "<td align='center' valign='middle'><input name='val_in06[".$id."]' type='text' id='val_in06[".$id."]' size='10' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_in06[".$id."]' type='text' id='val_in06[".$id."]' size='10' maxlength='7' value='".$val_in06."'/></td>";
				}

				#DQO
				$dqo=0;
				if($cod_mues==100000002 || $cod_mues==100000006){
					echo "<td align='center' valign='middle'> </td>";
				}else{
					$dqo=(($val_in05-$val_in06)*$val_in04*$facDil*8000)/$val_in01;

					if($dqo<=20){
						echo "<td align='center' valign='middle'><input name='dqo[".$id."]' type='hidden' id='dqo[".$id."]' value='<20'/><20</td>";
					}else{
						echo "<td align='center' valign='middle'><input name='dqo[".$id."]' type='hidden' id='dqo[".$id."]' value='".round($dqo,3)."'/>".round($dqo,3)."</td>";
					}						
				}

				#DUPLICADO
				if (empty($mue_dupl)) { 
					echo "<td align='center' valign='middle'>&nbsp;</td>\n";
				} else { 
					echo "<td align='center' valign='middle'>X</td>"; 
				}
				
				#OBSERVACION 
				if (empty($obs_erva)) { 
					echo "<td align='center' valign='middle'><label for='textarea'></label><textarea name='obs_erva[".$id."]' id='obs_erva[".$id."]' cols='25' rows='2'></textarea></td>";
				} else { 
					echo "<td align='center' valign='middle'><label for='textarea'></label><textarea name='obs_erva[".$id."]' id='obs_erva[".$id."]' cols='25' rows='2'>".$obs_erva."</textarea></td>";
				}

				$nom_rep1="";
				$nom_rep2="";
				$nom_ver1="";
				$nom_mod1="";

				while ($rowNom = mysql_fetch_array($rstNom)) {
					extract($rowNom);
					if ($emp_rep1==$cod_terc) {
						$nomsep1=explode(" ",$nombre." ".$apellido);
						$nom_rep1=$nomsep1[0]." ".$nomsep1[2];
					}
					if ($emp_rep2==$cod_terc) {
						$nomsep2=explode(" ",$nombre." ".$apellido);
						$nom_rep2=$nomsep2[0]." ".$nomsep2[2];
					}
					if ($emp_ver1==$cod_terc) {
						$nomsep3=explode(" ",$nombre." ".$apellido);
						$nom_ver1=$nomsep3[0]." ".$nomsep3[2];
					}
					if ($emp_mod1==$cod_terc) {
						$nomsep4=explode(" ",$nombre." ".$apellido);
						$nom_mod1=$nomsep4[0]." ".$nomsep4[2];
					}
				}
				mysql_data_seek($rstNom,0);

				// EMPLEADO QUE REPORTA 1
				if (empty($emp_rep1)) { 
					echo "<td align='center' valign='middle' size='13'>&nbsp;</td>\n";
				} else { 
					echo "<td align='center' valign='middle' size='13'><a href='' title='".$nom_rep1."'>".$nom_rep1."</a></td>"; 
				}

				// EMPLEADO QUE REPORTA 2
				if (empty($emp_rep2)) {
					if ($estAudi==0) {
						echo "<td align='center' valign='middle'><input type='checkbox' name='rep_regi[]' value=$id/></td>\n";
					}else{
						if (date("Y-m-d")==$fecform) {
							echo "<td align='center' valign='middle'><input type='checkbox' name='rep_regi[]' value=$id/></td>\n";
						}else{
							echo "<td align='center' valign='middle'></td>\n";
						}
					}
				} else { 
					echo "<td align='center' valign='middle' size='13'><a href='' title='".$nom_rep2."'>".$nom_rep2."</a></td>"; 
				}

				// EMPLEADO QUE VERIFICA 3
				if (empty($emp_ver1) && !empty($emp_rep2)) {
					if ($estAudi==0) {
						echo "<td align='center'><input type='checkbox' name='emp_ver1[]' value=$id/></td>";
					}else{
						if (date("Y-m-d")==$fecform) {
							echo "<td align='center'><input type='checkbox' name='emp_ver1[]' value=$id/></td>";
						}else{
							echo "<td align='center' valign='middle'></td>\n";
						}
					}
				} else {
					if ($emp_ver1 > 0) {
						echo "<td align='center' size='13'><a href='' title='".$nom_ver1."'>$nom_ver1</a></td>\n";
					} else {
						echo "<td align='center' valign='middle'>&nbsp;</td>";
					}
				}

				// EMPLEADO QUE VERIFICA 4
				if (empty($emp_ver1)) {
					if ($estAudi==0) {
						echo "<td align='center'><input type='checkbox' name='edi_regi[]' value=$id/></td>";
					}else {
						if (date("Y-m-d")==$fecform) {
							echo "<td align='center'><input type='checkbox' name='edi_regi[]' value=$id/></td>";
						}else{
							echo "<td align='center' valign='middle'></td>\n";
						}
					}
				} else {
					if ($emp_mod1 > 0) {
						echo "<td align='center' valign='middle' size='13'><a href='' title='".$nom_mod1."'>".$nom_mod1."</a></td>";
					} else {
						echo "<td align='center' valign='middle'>&nbsp;</td>";
					}	
				}
					
			    echo "</tr>";
				
				$valmuesrepl = $cod_mues;
				
                $i++;
				$j++;
			}

		  
		  ?>
		    <!--FUNCIONES DE VALIDACION REPORTAR VERIFICAR Y ACTUALIZAR -->
		  <tr>
		    <td colspan="16" align="right" valign="middle"><input type="hidden" name="idtipform" id="idtipform" value="<?php echo $idtipform; ?>"/>
            <input type="hidden" name="idf" id="idf" value="<?php echo $idf; ?>"/></td>
			<td align="center" valign="middle"><input type="submit" name="reportar" id="reportar" value="Reportar" /></td>
		    <td align="center" valign="middle"><input type="submit" name="verificar" id="verificar" value="Verificar" /></td>
		    <td align="center" valign="middle"><input type="submit" name="actualizar" id="actualizar" value="Actualizar" /></td>
	      </tr>
		
		</table>
	
	</form>

	<a href='dp_cons_mafor.php?idtipform=55&nomform=dp_def04.php' class='mnulink'>REGRESAR</a>

	<p><strong>Cantidad de Registros:</strong> <?php echo $nrwdeta;?></p>
<?php
}
?>
<?php
	require('../libs/footer.php');

} else {
	
	header('Location: ../index.php');
	
}
//TODO: Verificar los grupos de 20 muestras.
if($numVeri % 20==0 && $numVeri!=0){
	?>
	<script type="text/javascript">
		alert("Se han completado 20 muestras desde el \u00FAltimo est\u00E1ndar, favor de ingresar el duplicado, un blanco y un nuevo est\u00E1ndar para continuar.");
	</script>
	<?php
}

mysql_close($conn);

?>