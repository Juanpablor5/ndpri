<?php

require('conexionbd.lib.php');

if (isset($_SESSION['usuario'])) {

  $id_usuario = $_SESSION['idusuario'];

  $sqlclie = "SELECT idcliente FROM auth_users WHERE id = ".$id_usuario;
  $rstclie = mysql_query($sqlclie) or die ('Error: ' . mysql_error());
  $rowclie = mysql_fetch_array($rstclie);

  $idcliente = $rowclie['idcliente'];

	echo "<table width='100%' border='0'>\n";
	echo "<tr><td colspan='8'><hr size='0' noshade='noshade' /></td></tr>\n";
	echo "<tr><td><center><a href='/index_lims.php'>Principal</a></center></td>\n";

  if ($id_usuario == 74){
    echo "<td><center><a href='/clientes/index.php'>Clientes</a></center></td>\n";
  }

	if ($_SESSION['nivel'] < 2) {
		echo "<td><center><a href='/clientes/index.php'>Clientes</a></center></td>\n";
		echo "<td><center><a href='/parametros/index.php'>Par&aacute;metros</a></center></td>\n";
		echo "<td><center><a href='/normas/index.php'>Normatividad</a></center></td>\n";
	} else {
		echo "<td>&nbsp;</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "<td>&nbsp;</td>\n";
	}

	if ($idcliente > 0){
    echo "<td align='center'><a href='/formatos/index_buscador.php'>Formatos</a></td>\n";
  } else {
    echo "<td align='center'><a href='/formatos/index.php'>Formatos</a></td>\n";
  }

	echo "<td align='center'>\n";

  if ($idcliente > 0){
    echo "<a href='/muestras/index_buscador.php'>Muestras</a></td>";
  } else {
    echo "<a href='/muestras/index.php'>Muestras</a></td>";
  }

	echo "<td align='center'>\n";
	echo "<b>Ingres&oacute; como:</b> ".$_SESSION['usuario']."</td>\n";
	echo "<td align='right'>\n";
  echo "<a href='/logout.php'><img src='../img/salir_01.png' border='0' width='41' height='41' /></a></td></tr></table>";

	$tiempo = microtime();
	$tiempo = explode(" ",$tiempo);
	$tiempo = $tiempo[1] + $tiempo[0];
	$tiempoFin = $tiempo;
	$tiempoReal = ($tiempoFin - $tiempoInicio);
	$tiempoReal = round($tiempoReal, 4);

	echo "<p><p align='center'>Script generado en <b>". $tiempoReal . "</b> segundos.\n";
	echo "<p><p align='center'><b>NET ID : </b>". $_SESSION['direc_ip'].".\n";

	echo "</body>\n";

} else {

	echo "<table width='100%' border='0'>\n";
	echo "<tr><td><hr size='0' noshade='noshade' /></td></tr></table>\n";
	echo "<p align='right'><a href='mailto:sistemas@anteksa.com' class='link02'>Dise&ntilde;o y desarrollo</a>
	<p align='right' class='txtantek'>Antek S.A. 2010\n";
	echo "</body>\n";
}

?>