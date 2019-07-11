<?php
// funcion para registrar la actualizacion de actividades
function actualizar_plan () {

  global $idplamu,$codclie,$numnite,$valglob,$nomcont,$carcont,$numtele,
         $dirmail,$numfaxe,$codplan,$lugmoni,$fecmoni,$numcoti,$feccont,
         $obsserv,$anexose,$nomclie,$codempl,$celinge,$maiinge,$estplan;

  if (!empty($idplamu) && !empty($codclie)){
    // info log actividades
    $nom_us = $_SESSION['nombre_usuario'];
    $dir_ip = $_SESSION['direc_ip'];
    $frm = "000";

    $nm = 0; // numero de modificaciones

    $sqlsel = "SELECT * FROM py_plamu WHERE id = ".$idplamu;
    $rstsel = mysql_query($sqlsel) or die ('Error seleccionando py_plamu : ' . mysql_error());
    $rowsel = mysql_fetch_row($rstsel);

    if ($valglob > 0) {
		if ($rowsel > 0){

		  $sql = "UPDATE py_plamu SET"; // sql modificacion del formato

		  if ($rowsel['cod_clie'] != $codclie && !empty($codclie)) {
			$sql.= " cod_clie = $codclie";
			$nm++;
		  }

		  if ($rowsel['num_nite'] != $numnite && !empty($numnite)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " num_nite = '$numnite'";
			$nm++;
		  }
		  
		  if ($rowsel['val_glob'] != $valglob && !empty($valglob)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " val_glob = '$valglob'";
			$nm++;
		  }

		  if ($rowsel['nom_cont'] != $nomcont && !empty($nomcont)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " nom_cont = '$nomcont'";
			$nm++;
		  }

		  if ($rowsel['car_cont'] != $carcont && !empty($carcont)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " car_cont = '$carcont'";
			$nm++;
		  }

		  if ($rowsel['num_tele'] != $numtele && !empty($numtele)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " num_tele = '$numtele'";
			$nm++;
		  }

		  if ($rowsel['dir_mail'] != $dirmail && !empty($dirmail)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " dir_mail = '$dirmail'";
			$nm++;
		  }

		  if ($rowsel['num_faxe'] != $numfaxe && !empty($numfaxe)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " num_faxe = '$numfaxe'";
			$nm++;
		  }

		  if ($rowsel['cod_plan'] != $codplan && !empty($codplan)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " cod_plan = $codplan";
			$nm++;
		  }

		  if ($rowsel['lug_moni'] != $lugmoni && !empty($lugmoni)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " lug_moni = '$lugmoni'";
			$nm++;
		  }

		  if ($rowsel['fec_moni'] != $fecmoni && !empty($fecmoni)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " fec_moni = '$fecmoni'";
			$nm++;
		  }

		  if ($rowsel['num_coti'] != $numcoti && !empty($numcoti)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " num_coti = '$numcoti'";
			$nm++;
		  }

		  if ($rowsel['fec_cont'] != $feccont && !empty($feccont)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " fec_cont = '$feccont'";
			$nm++;
		  }

		  if ($rowsel['obs_serv'] != $obsserv && !empty($obsserv)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " obs_serv = '$obsserv'";
			$nm++;
		  }

		  if ($rowsel['ane_xose'] != $anexose && !empty($anexose)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " ane_xose = '$anexose'";
			$nm++;
		  }

		  if ($rowsel['nom_clie'] != $nomclie && !empty($nomclie)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " nom_clie = '$nomclie'";
			$nm++;
		  }

		  if ($rowsel['cod_empl'] != $codempl && !empty($codempl)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " cod_empl = '$codempl'";
			$nm++;
		  }

		  if ($rowsel['cel_inge'] != $celinge && !empty($celinge)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " cel_inge = '$celinge'";
			$nm++;
		  }

		  if ($rowsel['mai_inge'] != $maiinge && !empty($maiinge)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " mai_inge = '$maiinge'";
			$nm++;
		  }

		  if ($rowsel['est_plan'] != $estplan && !empty($estplan)) {
			if ($nm > 0) $sql.= ",";
			$sql.= " est_plan = '$estplan'";

			if ($estplan == 'A') {

				$sqll = " update gn_activ set est_acti = 'CA' where pla_mues = ".$codplan;
				$rstl = mysql_query($sqll) or die ('Error actualizando gn_activ : ' . mysql_error());
			}

			$nm++;
		  }

		  $sql.= " WHERE id = '$idplamu'";
		  $rst = mysql_query($sql) or die ('Error actualizando py_plamu : ' . mysql_error());
		  
		  echo "Se actualizo la actividad $idupdate correctamente";

		} else {
		  echo "<p align='center'><font color='#FF0000'>No se pudo actualizar el plan, el numero de plan $idplamu no existe</font></p>";
		}
	} else {
	  echo "<p align='center'><font color='#FF0000'>No se pudo actualizar el plan, Verifique el valor glogal del PM - Recuerde que es obligatorio ingresar este valor</font></p>";
	}	
  } else {
	echo "<p align='center'><font color='#FF0000'>No se pudo actualizar el plan, Verifique el valor glogal del PM - Recuerde que es obligatorio ingresar este valor</font></p>";
  }
} // aqui termina la funcion para registrar la actualizacion de actividades

// funcion para registrar la entrada de muestras
function adicionar_plan () {

  global $idplamu,$codclie,$numnite,$valglob,$nomcont,$carcont,$numtele,
         $dirmail,$numfaxe,$codplan,$lugmoni,$fecmoni,$numcoti,$feccont,
         $obsserv,$anexose,$nomclie,$codempl,$celinge,$maiinge,$estplan,$annoh;

	if (!empty($codclie) && !empty($numnite) && !empty($lugmoni) && !empty($valglob)){
    // info log actividades
    $nom_us = $_SESSION['nombre_usuario'];
    $dir_ip = $_SESSION['direc_ip'];
    $frm = "000";

    $sqlsec = "SELECT anno, secuencial FROM lms_secuencial WHERE nomtabla = 'py_plamu' AND anno = ".$annoh;
    $rstsec = mysql_query($sqlsec) or die ('Error seleccion secuencial: '.mysql_error());
    $rwsec = mysql_fetch_array($rstsec);

    $secuencial = $rwsec['secuencial'];
	$anoplan = $rwsec['anno'];
    $codplan = $secuencial + 1;

    $sql = "INSERT INTO py_plamu (id,cod_plan, ano_plan, cod_clie, num_nite, val_glob, nom_cont, lug_moni, ";
    $sql.= " fec_moni, num_tele, dir_mail, num_faxe, num_coti, fec_cont, obs_serv, ane_xose, fec_plam, nom_clie, car_cont, cod_empl, cel_inge, mai_inge, est_plan) ";
    $sql.= " VALUES ('',$codplan, $anoplan, $codclie, '$numnite', '$valglob','$nomcont', '$lugmoni', '$fecmoni', ";
    $sql.= " '$numtele', '$dirmail', '$numfaxe', '$numcoti', '$feccont', '$obsserv', '$anexose', now(), '$nomclie', '$carcont', '$codempl', '$celinge', '$maiinge', '$estplan')";
    $rst = mysql_query($sql) or die ('Error insertando py_plamu : ' . mysql_error());

    $idupdate = mysql_insert_id();

    $sql = "UPDATE lms_secuencial SET secuencial = $codplan WHERE nomtabla = 'py_plamu' AND anno = ".$annoh;
    $rst = mysql_query($sql) or die ('Error actualizando secuencial : ' . mysql_error());

    echo "Se adiciono el plan de monitoreo correctamente";

  } else {
    echo "<p align='center'><font color='#FF0000'>No se pudo adicionar el plan, recuerde que toda la informacion debe ser diligenciada</font></p>";
	echo "<p align='center'><font color='#FF0000'>Verifique el valor glogal del PM - Recuerde que es obligatorio ingresar este valor</font></p>";
  }

  return $idupdate;

} // aqui termina la funcion de registro de la muestra

// funcion para registrar la actualizacion de actividades
function actualizar_dplam () {

  global $idupdate,$codplan,$codmatr,$tipmoni,$tipmode,$idemoni,$frealic,$freinsi,
         $numpunt,$despara,$obsserv;

  if (!empty($idupdate) && !empty($codplan)){
    // info log actividades
    $nom_us = $_SESSION['nombre_usuario'];
    $dir_ip = $_SESSION['direc_ip'];
    $frm = "000";

    $nm = 0; // numero de modificaciones

    $sqlsel = "SELECT * FROM py_dplam WHERE id = ".$idupdate;
    $rstsel = mysql_query($sqlsel) or die ('Error seleccionando py_dplam : ' . mysql_error());
    $rowsel = mysql_fetch_row($rstsel);

    if ($rowsel > 0){

      $sql = "UPDATE py_dplam SET"; // sql modificacion del formato

      if ($rowsel['cod_matr'] != $codmatr && !empty($codmatr)) {
        $sql.= " cod_matr = $codmatr";
        $nm++;
      }

      if ($rowsel['tip_moni'] != $tipmoni && !empty($tipmoni)) {
        if ($nm > 0) $sql.= ",";
        $sql.= " tip_moni = '$tipmoni'";
        $nm++;
      }

      if ($rowsel['ide_moni'] != $idemoni && !empty($idemoni)) {
        if ($nm > 0) $sql.= ",";
        $sql.= " ide_moni = '$idemoni'";
        $nm++;
      }

      if ($rowsel['fre_alic'] != $frealic && !empty($frealic)) {
        if ($nm > 0) $sql.= ",";
        $sql.= " fre_alic = '$frealic'";
        $nm++;
      }

      if ($rowsel['fre_insi'] != $freinsi && !empty($freinsi)) {
        if ($nm > 0) $sql.= ",";
        $sql.= " fre_insi = '$freinsi'";
        $nm++;
      }

      if ($rowsel['num_punt'] != $numpunt && !empty($numpunt)) {
        if ($nm > 0) $sql.= ",";
        $sql.= " num_punt = '$numpunt'";
        $nm++;
      }
	  
	  if ($rowsel['tip_mode'] != $tipmode) {
        if ($nm > 0) $sql.= ",";
        $sql.= " tip_mode = '$tipmode'";
        $nm++;
      }

      if ($rowsel['des_para'] != $despara && !empty($despara)) {
        if ($nm > 0) $sql.= ",";
        $sql.= " des_para = '$despara'";
        $nm++;
      }

      if ($rowsel['obs_serv'] != $obsserv && !empty($obsserv)) {
        if ($nm > 0) $sql.= ",";
        $sql.= " obs_serv = '$obsserv'";
        $nm++;
      }

      $sql.= " WHERE id = '$idupdate'";
      $rst = mysql_query($sql) or die ('Error actualizando py_dplam : ' . mysql_error());

      echo "Se actualizo la matriz $idupdate correctamente";

    } else {
      echo "No se pudo actualizar la matriz, el numero de matriz $idplamu no existe";
    }
  } else {
    echo "No se pudo actualizar la matriz, recuerde que toda la informacion debe ser diligenciada";
  }
} // aqui termina la funcion para registrar la actualizacion de actividades

// funcion para registrar la entrada de muestras
function adicionar_dplam () {

  global $idupdate,$codclie,$codplan,$anoplan,$codmatr,$tipmoni,$tipmode,$idemoni,$frealic,$freinsi,
         $numpunt,$despara,$obsserv;

	if (!empty($codplan) && !empty($codmatr) && !empty($tipmoni)){
    // info log actividades
    $nom_us = $_SESSION['nombre_usuario'];
    $dir_ip = $_SESSION['direc_ip'];
    $frm = "000";

    $sql = "INSERT INTO py_dplam (id,cod_plan,ano_plan,cod_clie,cod_matr,tip_moni,ide_moni,fre_alic,fre_insi,num_punt,tip_mode,des_para,obs_serv) ";
    $sql.= " VALUES ('',$codplan,$anoplan,$codclie,$codmatr,$tipmoni,'$idemoni','$frealic','$freinsi','$numpunt','$tipmode','$despara','$obsserv')";
    $rst = mysql_query($sql) or die ('Error insertando py_dplam : ' . mysql_error());

    $idupdate = mysql_insert_id();

    echo "Se adiciono la matriz de monitoreo correctamente";

  } else {
    echo "No se pudo adicionar la matriz, recuerde que toda la informacion debe ser diligenciada";
  }

  return $idupdate;

} // aqui termina la funcion de registro de la muestra

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

  //convierto segundos en días
  $dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

  //obtengo el valor absoulto de los días (quito el posible signo negativo)
  $dias_diferencia = abs($dias_diferencia);

  //quito los decimales a los días de diferencia
  $dias_diferencia = floor($dias_diferencia);

  //echo $dias_diferencia;
  return $dias_diferencia + 1;
}

//Funcion que suma un dia, una anno o un mes a una fecha
function suma_fechas($date, $dd=0, $mm=0, $yy=0, $hh=0, $mn=0, $ss=0){

  $date_r = getdate(strtotime($date));
  $date_result = date("Y-m-d h:i:s", mktime(($date_r["hours"]+$hh),($date_r["minutes"]+$mn),($date_r["seconds"]+$ss),($date_r["mon"]+$mm),($date_r["mday"]+$dd),($date_r["year"]+$yy)));
  return $date_result;

}


?>
