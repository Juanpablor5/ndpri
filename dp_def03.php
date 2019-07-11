<?php
session_start();
require('../libs/conexionbd.lib.php');
require_once('../libs/auth.php');
require_once('./libs/principal.php');

// Validar el id del Usuario y que la Sesion este activa
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
		$valin01 = $rowmafor['val_in01'];
		$valin02 = $rowmafor['val_in02'];
		$valfc01 = $rowmafor['val_fc01'];
		$fecreal = $rowmafor['fec_real'];
		$codempl = $rowmafor['cod_empl'];
		
		$sqlAudi = "SELECT * FROM lms_admglobal WHERE id = 1";
		$rstAudi = mysql_query($sqlAudi) or die ('Error: lms_admglobal' . mysql_error());
		$rowAudi = mysql_fetch_array($rstAudi);
		$estAudi = $rowAudi['est_audi'];

		$sqlVeri = "SELECT COUNT(DISTINCT num_rela) AS cantidad FROM dp_def03 WHERE cod_maes=".$idf." AND cod_mues<>100000001 AND cod_mues<>100000002 AND cod_mues<>100000003 AND cod_mues<>100000004 AND cod_mues<>100000005 AND mue_dupl<>1";
		$rstVeri = mysql_query($sqlVeri) or die ('Error: dp_mafor' . mysql_error());
		$rowVeri = mysql_fetch_array($rstVeri);
		$numVeri = $rowVeri['cantidad'];
	}
	echo "<script type='text/javascript' src='../js/valid.js'></script>\n"; // script de validacion js
?>	

<form id="form1" name="form1" method="post" action="fn_def03.php">
  <table width="90%" border="1" align="center" cellpadding="0" cellspacing="1">
    <tr>
      <td><table width="98%" border="0" align="center" cellpadding="2" cellspacing="2">
        <tr>
          <td align="center" bgcolor="#CCCCCC"><strong>ANALISIS DE DBO - METODO SM 5210 B, 4500-O G / 4500-O C</strong></td>
        </tr>
        <tr>
          <td align="center" valign="middle"><strong> FECHA DE ANALISIS
            (A&Ntilde;O-MES-DIA):
            <?php
			
				if (!empty($fecform)) {
					echo "<strong>".$fecform."</strong>";
				} else {
					echo '<input name="fec_form" type="date" id="fec_form" size="15"/>';
				}
			
			?>
		   CODIGO DEL EQUIPO: <?php 
		   if (!empty($valst01)) { 
			   echo "$valst01";
			}else{
				echo "<input name='val_st01' type='text' id='val_st01' size='15' value=".$valst01.">";
			}
			?>
            </strong></td>
        </tr>
        <tr>
          <td align="right" valign="middle"><br />
            <?php 
				if ($idf == 0) {
					echo '<input type="submit" name="crearform" id="crearform" value="Crear Registro Tecnico" />';
					echo "<br><br><br><a href='dp_cons_mafor.php?idtipform=13&nomform=dp_def03.php' class='mnulink'>REGRESAR</a>";
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
	$sqldeta = "select a.*, (select nromuestra from lms_muestras where id = a.cod_mues) as nro_mcs, b.parametro as nom_para,
	((a.val_in05 - a.val_in06)) as abatimiento
	from dp_def03 a, lms_lstparam b
	where a.cod_para = b.id and cod_maes =".$idf."
	order BY num_rela desc, num_bot asc";

	$sqlNom = "Select cod_terc, nombre, apellido FROM adm_terc WHERE emp=1";

	$rstdeta = mysql_query($sqldeta) or die ('Error Select dp_def03 a, lms_lstparam b, lms_muestras c, rh_emple d: ' . mysql_error());
	$rstdetaAux = mysql_query($sqldeta) or die ('Error Select auxiliar: ' . mysql_error());

	$rstNom = mysql_query($sqlNom) or die ('Error Select dp_def03 a, lms_lstparam b, lms_muestras c, rh_emple d: ' . mysql_error());

	$nrwdeta = mysql_num_rows($rstdeta);
?>
</p>
<form id="form4" name="form4" method="post" action="fn_def03.php">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center"><p><strong>INGRESO DE RESULTADOS</strong></p>
        <p><strong>No. MUESTRA MCS: </strong>
          <select required name="cod_mues" id="cod_mues">
           
		    <?php								
				
				if ($nrwdeta==0){
					echo "<option value='100000002'>BLANCO</option>\n";
					$autoComVol=300;
					$autoComFD=1;
					$autoComI1=0;
					$autoComI2=0;
					$autoComI3=0;				
				}elseif($nrwdeta==3){
					echo "<option value='100000001'>CEPA</option>\n";
					$autoComVol=300;
					$autoComFD=1;
					$autoComI1=2;
					$autoComI2=2.5;
					$autoComI3=3;
				}elseif($nrwdeta==6){
					echo "<option value='100000004'>STD 200</option>\n";
					$autoComVol=5;
					$autoComFD=1;
					$autoComI1=1;
					$autoComI2=1;
					$autoComI3=1;
				}elseif($nrwdeta==9){
					echo "<option value='100000003'>STD 10</option>\n";
					$autoComVol=100;
					$autoComFD=1;
					$autoComI1=1;
					$autoComI2=1;
					$autoComI3=1;
				}elseif($nrwdeta==12){
					echo "<option value='100000005'>LCM</option>\n";
					$autoComVol=200;
					$autoComFD=1;
					$autoComI1=1;
					$autoComI2=1;
					$autoComI3=1;
				}else{
					$sqlmues = "select id, nromuestra, fecrecep
					from lms_muestras
					where fecrecep >= date_sub(CURDATE(),interval 10 day)
					order by 1 desc";			
					$rstmues = mysql_query($sqlmues) or die ('Error Select dp_patip: '.mysql_error());
					echo "<option value='' selected>Selec. muestra</option>\n";
					echo "<option value='100000002'>BLANCO</option>\n";
					echo "<option value='100000003'>STD 10</option>\n";
					echo "<option value='100000004'>STD 200</option>\n";
					echo "<option value='100000005'>LCM</option>\n";
					$autoComVol="";
					$autoComFD="";
					$autoComI1="";
					$autoComI2="";
					$autoComI3="";
				}
				while ($rowmues = mysql_fetch_array($rstmues)) {
					echo "<option value='$rowmues[id]' \>$rowmues[nromuestra]</option>\n";	
				}
				
				?>
          </select>
          <strong>Duplicado:</strong>
          <input name="mue_dupl" type="checkbox" id="mue_dupl" value="1" />
          <strong>PARAMETRO:

          <select required name="cod_para" id="cod_para">
            <?php
				
					$sqlmatr = "SELECT a.cod_tipf, a.cod_para, b.parametro, b.unidades, b.tecnica, b.metodo
								FROM dp_patip a, lms_lstparam b
								WHERE a.cod_para = b.id and a.cod_tipf = ".$idtipform."
								ORDER BY a.id";			
					$rstmatr = mysql_query($sqlmatr) or die ('Error Select dp_patip: ' . mysql_error());
					echo "<option value=''>.::SELECCIONE PARAMETRO::.</option>";
					
					while ($rowmatr = mysql_fetch_array($rstmatr)) {
						echo "<option value='$rowmatr[cod_para]' \>$rowmatr[parametro]::$rowmatr[unidades]::$rowmatr[tecnica]::$rowmatr[metodo]</option>\n";	
					}
				
				?>
          </select>
        </strong> </p>
        <table width="98%" border="1" align="center" cellpadding="4" cellspacing="2">
          <tr>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Hora de la Incubaci&#243;n (HH:MM)</strong></td>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen de Muestra (mL)</strong></td>
            <td colspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Factor de Diluci&#243;n Previa (mL)</strong></td>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen Inoculo (mL)</strong></td>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>OD Inicial (mg/L)</strong></td>
            <td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Observaciones</strong></td>
          </tr>
          <tr>
            <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Muestra</strong></td>
            <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Aforo</strong></td>
          </tr>
          <tr>
            <td align="center" valign="middle"><label for="val_hor1"></label>
            <input name="val_hor1" required type="text" id="val_hor1" size="5" maxlength="5" /></td>
            <td align="center" valign="middle"><label for="val_vom1"></label>
            <input name="val_vom1" required type="text" value="<?php echo "$autoComVol" ?>" id="val_vom1" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="val_mue1" value="<?php echo "$autoComFD" ?>" required type="text" id="val_mue1" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="val_afo1" value="<?php echo "$autoComFD" ?>" required type="text" id="val_afo1" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><select required name="val_vol1"  id="val_vol1">
              <option value="<?php echo "$autoComI1" ?>" selected><?php echo "$autoComI1" ?></option>
			  <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="2.5">2.5</option>
              <option value="3">3</option>
			  
            <td align="center" valign="middle"><input name="val_odi1" required type="text" id="val_odi1" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><label for="val_obs1"></label>
            <input name="val_obs1" type="text" id="val_obs1" size="70" /></td>
          </tr>
          <tr>
            <td align="center" valign="middle"><input name="val_hor2" required type="text" id="val_hor2" size="5" maxlength="5" /></td>
            <td align="center" valign="middle"><input name="val_vom2" required value="<?php echo "$autoComVol" ?>" type="text" id="val_vom2" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="val_mue2" required value="<?php echo "$autoComFD" ?>" type="text" id="val_mue2" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="val_afo2" required value="<?php echo "$autoComFD" ?>" type="text" id="val_afo2" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><select required name="val_vol2"  id="val_vol2">
			  <option value="<?php echo "$autoComI2" ?>" selected><?php echo "$autoComI2" ?></option>
			  <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="2.5">2.5</option>
              <option value="3">3</option>

            <td align="center" valign="middle"><input name="val_odi2" required  type="text" id="val_odi2" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="val_obs2" type="text" id="val_obs2" size="70" /></td>
          </tr>
          <tr>
            <td align="center" valign="middle"><input name="val_hor3" required  required type="text" id="val_hor3" size="5" maxlength="5" /></td>
            <td align="center" valign="middle"><input name="val_vom3" value="<?php echo "$autoComVol" ?>"  required  type="text" id="val_vom3" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="val_mue3" value="<?php echo "$autoComFD" ?>" required  type="text" id="val_mue3" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="val_afo3" value="<?php echo "$autoComFD" ?>" required  type="text" id="val_afo3" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><select required name="val_vol3" id="val_vol3">
			  <option value="<?php echo "$autoComI3" ?>" selected><?php echo "$autoComI3" ?></option>
			  <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="2.5">2.5</option>
              <option value="3">3</option>
            <td align="center" valign="middle"><input name="val_odi3" required  type="text" id="val_odi3" size="6" maxlength="6" /></td>
            <td align="center" valign="middle"><input name="val_obs3" type="text" id="val_obs3" size="70" /></td>
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
	
<p>* El m&#233;todo de lectura de oxigeno disuelto, tanto del dia cero como el dia 5: <strong>O</strong>=Oximetro. SM: "Standard Methods for the Examination of Water and Wastewater".</p>
	<p><strong>Cantidad de Registros: </strong><?php echo $nrwdeta;?></p>
	
	<form id="form3" name="form3" method="post" action="fn_def03.php">
	
		<table width="100%" border="1" cellspacing="0" cellpadding="2">
		  <tr>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>No. An&#225;lisis</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>No. Muestra MCS</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>D&iacute;as de incub.</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Estado</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Hora de la Incubaci&#243;n (HH:MM)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen de Muestra (mL)</strong></td>
			<td colspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Factor de Diluci&#243;n Previa (mL)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Volumen Inoculo (mL)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>OD Inicial (mg/L)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Fecha de Lectura OD Final</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Hora de Lectura OD Final (HH:MM)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>OD Final (mg/L)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><p><strong>S, mg O2/mL CEPA</strong></p></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Abatimiento</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>DBO (mg O2/L)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Promedio</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Dupli.</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Observaciones</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Analista (Nombre)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Analista (Nombre)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Verifico (Nombre)</strong></td>
			<td rowspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><strong>Actualizar</strong></td>
		  </tr>
		  <tr>
		    <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Muestra</strong></td>
		    <td align="center" valign="middle" bgcolor="#CCCCCC"><strong>Aforo</strong></td>
	      </tr>
		  
		  <tr>
		    <td colspan="20" align="right" valign="middle"><input type="hidden" name="idtipform" id="idtipform" value="<?php echo $idtipform; ?>"/>
            <input type="hidden" name="idf" id="idf" value="<?php echo $idf; ?>"/></td>
			<td align="center" valign="middle"><input type="submit" name="reportar" id="reportar" value="Reportar" /></td>
		    <td align="center" valign="middle"><input type="submit" name="verificar" id="verificar" value="Verificar" /></td>
		    <td align="center" valign="middle"><input type="submit" name="actualizar" id="actualizar" value="Actualizar" /></td>
	      </tr>
		  
		  <?php

		  /*############################################################
		   * Comienza método para calcular pendiente, DBO5 y promedio.
		   ############################################################*/

            #Contadores generales del While principal
			$i = 1;  
            $j = 0;  
            
			#Contador auxiliar para los calculos.
			$contCalc=0;

			#Variables de pendiente.
            $pendiente=0; //Variable de la pendiente de cada muestra.
            $arrXY=array(); //Arreglo de Productos de Volumen del inoculo * Abatimiento 
            $arrY=array();  //Arreglo de  Abatimientos 
            $sumXY=0; //Suma de los datos del arreglo XY
            $sumY=0; //Suma de los datos del arreglo Y

            #Datos para calcular la pendiente por el método de Minimos Cuadrados 
            while ($rowdetaAux = mysql_fetch_array($rstdetaAux)){
                extract($rowdetaAux);
                //Calculo de datos de pendiente
                if($cod_mues==100000001){   // Sólo si es CEPA.
                    $arrXY[$contCalc] = $val_in04*($abatimiento);  
                    $arrY[$contCalc] = $abatimiento;
                    $contCalc++;
                }                
            }
            mysql_data_seek($rstdetaAux,0);

            //Calcula la pendiente por el método de Minimos Cuadrados.
            for($c=0;$c<count($arrXY);$c++){
				if($i%3!=0){ //Hace la suma de los dos primeros datos de los arreglos XY y Y
                    $sumXY+=$arrXY[$c];
                    $sumY+=$arrY[$c];
                }elseif($i%3==0){ //Al llegar al tercer dato...
                    $sumXY+=$arrXY[$c];
                    $sumY+=$arrY[$c];
                    $pendiente=(3*$sumXY-7.5*$sumY)/1.5; //...calcula la pendiente. 
					$sumXY=0; //Reinicia contadores.
                    $sumY=0;
                }
                $i++;
            }
            
			#Variables para promedio
			$promedio=0; //Variable de promedio de cada muestra.
			$noRepr=0; //Variable que determina el número de DBO's no representativos.
            $arrProm=array(); //Arreglo de promedios
			$arrDbo=array(); //Arreglo de DBO's
			$arrAba=array(); //Arreglo de abatimientos.
            $sumDBO=0; //Suma para calcular promedios.

            //Calculos de DBO5.
            $contCalc=0;
            while ($rowdetaAux = mysql_fetch_array($rstdetaAux)){
				extract($rowdetaAux);
				$arrAba[$contCalc]=$abatimiento; //Llena el arreglo de abatimientos.
                $arrDbo[$contCalc]=((($abatimiento)-($pendiente*$val_in04))/($val_in01/300))*($val_in03/$val_in02); //Llena el arreglo de DBO's ya calculados.
                $contCalc++;
            }
			mysql_data_seek($rstdetaAux,0);

			//Calculo de promedio
			$i=1;
            for($c=0;$c<count($arrDbo);$c++){
                if($i%3!=0){ //Suma los dos primeros DBO's del arreglo de DBO's
					if ($arrAba[$c]>=2) { 
						$sumDBO+=$arrDbo[$c];						
					}else{
						$noRepr++;
					}				
					$arrProm[$c]=0;
                }elseif($i%3==0){ //Al llegar al tercer DBO...
					if ($arrAba[$c]>=2) {
						$sumDBO+=$arrDbo[$c];
					}else{
						$noRepr++;
					}
					$denom = ($noRepr==1) ? 2 : $denom = ($noRepr==2) ? 1 : 3 ; //Establece el denominador del promedio según el numero de datos NO representativos.
					$arrProm[$c]=$sumDBO/$denom; //...calcula el promedio según el denominador establecido. 
					$sumDBO=0;
					$noRepr=0;
				}
                $i++;
			}
			
		  /*############################################################
		   * Finaliza método para calcular pendiente, DBO5 y promedio.
		   ############################################################*/

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
				// Numero de Analisis 
				echo "<tr bgcolor='$cf'>";
				echo "<td align='center' valign='middle'>".$i."</td>";
					
				// No.de muestra MCS 
				if (empty($cod_mues)) { 
					echo "<td align='center' valign='middle'>&nbsp;</td>\n";
				} else {
					if($cod_mues == 100000001) {
						echo "<td align='center' valign='middle'>CEPA</td>";
					}elseif ($cod_mues == 100000002) {
						echo "<td align='center' valign='middle'>BLANCO</td>";
					}elseif ($cod_mues == 100000003) {
						echo "<td align='center' valign='middle'>STD 10</td>";
					}elseif ($cod_mues == 100000004) {
						echo "<td align='center' valign='middle'>STD 200</td>";
					}elseif ($cod_mues == 100000005) {
						echo "<td align='center' valign='middle'>LCM</td>";
					}else {
						echo "<td align='center' valign='middle'>".$nro_mcs."</td>"; 
					}						
				}

				//Días de incuación.
				if ($cod_para == 477 || $cod_para == 52 || $cod_para == 15888 || $cod_para == 478 || $cod_para == 16356) {
					echo "<td align='center' valign='middle'>5</td>";
				}elseif ($cod_para == 501) {
					echo "<td align='center' valign='middle'>10</td>";
				}elseif ($cod_para == 605) {
					echo "<td align='center' valign='middle'>21</td>";
				}

				// ESTADO
				//suma_fechas($date, $dd=0, $mm=0, $yy=0, $hh=0, $mn=0, $ss=0)				
				$diaAler=0; #Calcula el día de alerta.
				if ($cod_para == 477 || $cod_para == 52 || $cod_para == 15888 || $cod_para == 478 || $cod_para == 16356) {
					$diaAler=5;
				}elseif ($cod_para == 501) {
					$diaAler=10;
				}elseif ($cod_para == 605) {
					$diaAler=21;
				}

				if(!empty($val_in06)){
					echo "<td align='center' valign='middle'><img src='./img/fin_01.png' width='16' height='16' border='0' title='An&#225;lisis terminado'/></td>";
				}elseif (date("Y-m-d H:i:s")<=suma_fechas($fec_rep1, $diaAler-1,0,0,17,59,59)) {
					echo "<td align='center' valign='middle'><img src='./img/icono_punto_verde.png' width='16' height='16' border='0' title='En incubaci&#243;n'/></td>\n";
				} else {
					if (date("Y-m-d H:i:s")>=suma_fechas($fec_rep1, $diaAler-1,0,0,18,0,0) && date("Y-m-d H:i:s")<=suma_fechas($fec_rep1, $diaAler,0,0,6,0,0)) {
						echo "<td align='center' valign='middle'><img src='./img/alerta_retrazo_01.gif' width='16' height='16' border='0' title='Alerta: Dentro de margen de tiempo de retiro'/></td>";
					}else{
						echo "<td align='center' valign='middle'><img src='./img/icono_rojo.png' width='16' height='16' border='0' title='Tiempo agotado'/></td>";
					}
				}					
				
			
				//Hora de incubacón 
				if (empty($val_st01)) { 
					echo "<td align='center' valign='middle'><input name='val_st01[".$id."]' type='text' id='val_st01[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_st01[".$id."]' type='text' id='val_st01[".$id."]' size='7' maxlength='7' value='".$val_st01."'/></td>";
				}

				//Volumen de la muestra
				if (empty($val_in01)) { 
					echo "<td align='center' valign='middle'><input name='val_in01[".$id."]' type='text' id='val_in01[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_in01[".$id."]' type='text' id='val_in01[".$id."]' size='7' maxlength='7' value='".$val_in01."'/></td>";
				}

				//Factor de dilución: muestra
				if (empty($val_in02)) { 
					echo "<td align='center' valign='middle'><input name='val_in02[".$id."]' type='text' id='val_in02[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_in02[".$id."]' type='text' id='val_in02[".$id."]' size='7' maxlength='7' value='".$val_in02."'/></td>";
				}

				//Factor de dilución: aforo
				if (empty($val_in03)) { 
					echo "<td align='center' valign='middle'><input name='val_in03[".$id."]' type='text' id='val_in03[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_in03[".$id."]' type='text' id='val_in03[".$id."]' size='7' maxlength='7' value='".$val_in03."'/></td>";
				}
				
				//Volumen de inoculo
				echo "<td align='center' valign='middle'><select name='val_in04[".$id."]' id='val_in04[".$id."]'>";
				echo "  <option value='0' selected='selected'>0</option>";
				if ($val_in04 == 1) {
					echo "  <option selected value='1'>1</option>";
				} else {
					echo "  <option value='1'>1</option>";
				}
				if ($val_in04 == 2) {
					echo "  <option selected value='2'>2</option>";
				} else {
					echo "  <option value='2'>2</option>";
				}
				if ($val_in04 == 2.5) {
					echo "  <option selected value='2.5'>2.5</option>";
				} else {
					echo "  <option value='2.5'>2.5</option>";
				}
				if ($val_in04 == 3) {
					echo "  <option selected value='3'>3</option>";
				} else {
					echo "  <option value='3'>3</option>";
				}
				echo "</select></td>";

				//OXIGENO DISUELTO INICIAL
				if (empty($val_in05)) { 
					echo "<td align='center' valign='middle'><input name='val_in05[".$id."]' type='text' id='val_in05[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_in05[".$id."]' type='text' id='val_in05[".$id."]' size='7' maxlength='7' value='".$val_in05."'/></td>";
				}

				//Fecha lectura final ideal (calculada)				
				if (empty($val_fe01)) {												
					$diaFinal=0;
					if ($cod_para == 477 || $cod_para == 52 || $cod_para == 15888 || $cod_para == 478 || $cod_para == 16356) {
						$diaFinal=5;
					}elseif ($cod_para == 501) {
						$diaFinal=10;
					}elseif ($cod_para == 605) {
						$diaFinal=21;
					}						
					echo "<td align='center' valign='middle'><input name='val_fe01[".$id."]' type='date' id='val_fe01[".$id."]' size='10' maxlength='10' value='".substr(suma_fechas($fecform,$diaFinal),0,10)."'/></td>\n";
				} else {
					echo "<td align='center' valign='middle'><input name='val_fe01[".$id."]' type='date' id='val_fe01[".$id."]' size='10' maxlength='10' value='".$val_fe01."'/></td>";
				}

				//Hora lectura final ideal (calculada)					
				if (empty($val_st01)) { 
					echo "<td align='center' valign='middle'><input name='val_st02[".$id."]' type='text' id='val_st02[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_st02[".$id."]' type='text' id='val_st02[".$id."]' size='7' maxlength='7' value='".$val_st01."'/></td>";
				}

				//OXIGENO DISUELTO FINAL
				if (empty($val_in06)) { 
					echo "<td align='center' valign='middle'><input name='val_in06[".$id."]' type='text' id='val_in06[".$id."]' size='7' maxlength='7' /></td>\n";
				} else { 
					echo "<td align='center' valign='middle'><input name='val_in06[".$id."]' type='text' id='val_in06[".$id."]' size='7' maxlength='7' value='".$val_in06."'/></td>";
				}

				//PENDIENTE o S, mg O2/mL CEPA
				if($valmuesrepl == $cod_mues && $i%3==1){
					if ($val_in06==0) {
						echo "<td align='center' valign='middle' rowspan='3'> </td>";
					}else{
						echo "<td align='center' valign='middle' rowspan='3'>".round($pendiente,2)."</td>";
					}						
				}elseif($valmuesrepl <> $cod_mues && $cod_mues==100000002) {
					echo "<td align='center' valign='middle' rowspan='3'>0</td>";
				}elseif($valmuesrepl <> $cod_mues && $val_in06==0){						
					echo "<td align='center' valign='middle' rowspan='3'> </td>";				
				}elseif($valmuesrepl <> $cod_mues){
					if($pendiente >= 0.6 && $pendiente<1){
						echo "<td align='center' valign='middle' rowspan='3'>".round($pendiente,2)."</td>";
					}else{
						echo "<td align='center' valign='middle' rowspan='3'>CEPA no aceptable</td>";
					}					
				}
				
				//$valres1 = ($val_in05 - $val_in06);
				// ABATIMIENTO
				if(!empty($val_in06)){
				echo "<td align='center' valign='middle'>".substr($abatimiento,0,5)."</td>";
				//echo "<td align='center' valign='middle'>abatimiento</td>";
				}else{
				echo "<td align='center' valign='middle' >0</td>\n";
				}
				

				// DBO
				if($val_in06==0){
					echo "<td align='center' valign='middle'> </td>";
				}elseif($cod_mues==100000001){ // VALIDACION SI ES CEPA
					echo "<td align='center' valign='middle bgcolor='blue'>CEPA</td>";
				}elseif($cod_mues==100000002){ // SI ES BLANCO
					if($abatimiento>0.2){
						echo "<td align='center' valign='middle'>Revisar agua</td>";
					}else{
						echo "<td align='center' valign='middle'>0</td>";
					}						
				}else{
					if($abatimiento<2){
						echo "<td align='center' valign='middle'>No representativa</td>";
					}else{
						echo "<td align='center' valign='middle'>".round($arrDbo[$i-1],3)."</td>";
					}						
				}
				
				//Determina valor de promedio a imprimir
				if($i%3==1){
					$promedio=$arrProm[$j+2];
				}elseif($i%3==2){
					$promedio=$arrProm[$j+1];
				}elseif($i%3==0){
					$promedio=$arrProm[$j];
				}
				
				//Promedio
				if($valmuesrepl == $cod_mues && $i%3==1){
					if ($val_in06==0) {
						echo "<td align='center' valign='middle' rowspan='3'> </td>";
					}else{
						if ($promedio<5) {
							echo "<td align='center' valign='middle' rowspan='3'><input name='prom[".$id."]' type='hidden' id='prom[".$id."]' value='<5'/><5</td>";
						}else{
							echo "<td align='center' valign='middle' rowspan='3'><input name='prom[".$id."]' type='hidden' id='prom[".$id."]' value='".round($promedio,2)."'/>".round($promedio,2)."</td>";
						}
					}						
				}elseif ($valmuesrepl <> $cod_mues && $cod_mues==100000001) {
					echo "<td align='center' valign='middle'  rowspan='3'><input name='prom[".$id."]' type='hidden' id='prom[".$id."]' value='CEPA'/>CEPA</td>";
				}elseif($valmuesrepl <> $cod_mues && $cod_mues==100000002){						
					echo "<td align='center' valign='middle'  rowspan='3'><input name='prom[".$id."]' type='hidden' id='prom[".$id."]' value='BLANCO'/>BLANCO</td>";	
				}elseif($valmuesrepl <> $cod_mues && $val_in06==0){						
					echo "<td align='center' valign='middle'  rowspan='3'> </td>";	
				}elseif($valmuesrepl <> $cod_mues){
					if ($promedio==0) {
						echo "<td align='center' valign='middle' rowspan='3'><input name='prom[".$id."]' type='hidden' id='prom[".$id."]' value='0'/>0</td>";
					}elseif($promedio<5){
						echo "<td align='center' valign='middle' rowspan='3'><input name='prom[".$id."]' type='hidden' id='prom[".$id."]' value='<5'/><5</td>";
					}else{
						echo "<td align='center' valign='middle' rowspan='3'><input name='prom[".$id."]' type='hidden' id='prom[".$id."]' value='".round($promedio,2)."'/>".round($promedio,2)."</td>";	
					}											
				}
				
				// DUPLICADO
				if (empty($mue_dupl)) { 
					echo "<td align='center' valign='middle'>&nbsp;</td>\n";
				} else { 
					echo "<td align='center' valign='middle'>X</td>"; 
				}
				
				// OBSERVACION 
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

				
				//suma_fechas($date, $dd=0, $mm=0, $yy=0, $hh=0, $mn=0, $ss=0)					 
				$diaAudi=0; #Calcula día de reporte de auditoría.
				
				if ($cod_para == 477 || $cod_para == 52 || $cod_para == 15888 || $cod_para == 478 || $cod_para == 16356) {
					$diaAudi=5;
				}elseif ($cod_para == 501) {
					$diaAudi=10;
				}elseif ($cod_para == 605) {
					$diaAudi=21;
				}

				// EMPLEADO QUE REPORTA 2
				if (empty($emp_rep2)) {
					if ($estAudi==0) {
						echo "<td align='center' valign='middle'><input type='checkbox' name='rep_regi[]' value=$id/></td>\n";
					}else{
						if (date("Y-m-d H:i:s")<suma_fechas($fec_rep1, $diaAudi,0,0,6,0,0)) {
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
						if (date("Y-m-d H:i:s")<suma_fechas($fec_rep1, $diaAudi,0,0,6,0,0)) {
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
						if (date("Y-m-d H:i:s")<suma_fechas($fec_rep1, $diaAudi,0,0,6,0,0)) {
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
		    <td colspan="20" align="right" valign="middle"><input type="hidden" name="idtipform" id="idtipform" value="<?php echo $idtipform; ?>"/>
            <input type="hidden" name="idf" id="idf" value="<?php echo $idf; ?>"/></td>
			<td align="center" valign="middle"><input type="submit" name="reportar" id="reportar" value="Reportar" /></td>
		    <td align="center" valign="middle"><input type="submit" name="verificar" id="verificar" value="Verificar" /></td>
		    <td align="center" valign="middle"><input type="submit" name="actualizar" id="actualizar" value="Actualizar" /></td>
	      </tr>
		
		</table>
	
	</form>

	<a href='dp_cons_mafor.php?idtipform=13&nomform=dp_def03.php' class='mnulink'>REGRESAR</a>

	<p><strong>Cantidad de Registros:</strong> <?php echo $nrwdeta;?></p>
<?php
}
?>
<?php
	require('../libs/footer.php');

} else {
	
	header('Location: ../index.php');
	
}
if($numVeri % 20==0 && $numVeri!=0){
	?>
	<script type="text/javascript">
		alert("Se han completado 20 muestras desde el \u00FAltimo est\u00E1ndar, favor de ingresar el duplicado, un blanco y un nuevo est\u00E1ndar para continuar.");
	</script>
	<?php
}

mysql_close($conn);

?>