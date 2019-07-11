<?php
session_start();
require('../libs/conexionbd.lib.php');
require_once('../libs/auth.php');
require('../libs/header.php');

  $unixtime = time();
  $hoy = date('Y-m-d',$unixtime);
  $annoh = date("Y");
  $messh = date("m");
  $diaah = date("d");

if (!empty($_SESSION['idusuario']) && !empty($_SESSION['usuario'])) { // si la sesion no ha caducado

  $id_usuario = $_SESSION['idusuario'];

  $sql = "SELECT area,idcliente FROM auth_users WHERE id = ".$id_usuario;
  $rst = mysql_query($sql) or die ('Error: auth_users ' . mysql_error());
  $row = mysql_fetch_array($rst);

  $area = $row['area'];
  $idcliente = $row['idcliente'];

	echo "<h2>DATOS PRIMARIOS</h2>
	<table border='0'>";
	
	$sql = "SELECT * FROM dp_tipfo where esta= 1 order by sig_area, nom_tipo ";
	$rst = mysql_query($sql) or die ('Error: dp_tipfo ' . mysql_error());
	
	$sigarea = '';
	
	while ($row = mysql_fetch_array($rst)) {
	
		if ($sigarea != $row['sig_area']) {
			
			switch ($row ['sig_area']) {
				case 'fq':
					$nomarea = 'FISICOQUIMICO';
				break;
				case 'aa':
					$nomarea = 'ABSORCION ATOMICA';
				break;
				case 'cg':
					$nomarea = 'CROMATOGRAFIA';
				break;
				case 'mb':
					$nomarea = 'MICROBIOLOGIA';
				break;
			}
			echo "<tr><td><a href='#' class='mnulink'>".$nomarea."</a></td></tr>\n";
		}
	
		echo "<tr><td>&nbsp;&nbsp;</td><td><a href='dp_cons_mafor.php?idtipform=".$row ['id']."&nomform=".$row ['nom_form']."' class='mnulink'>".$row ['nom_tipo']."</a></td></tr>\n";
		
		$sigarea = $row['sig_area'];
	
	}
	echo "<tr><td>&nbsp;</td></tr>\n";
	

	echo "<tr><td><a href='/index_lims.php' class='mnulink'>REGRESAR</a></td></tr>
	</table>\n";

} else { // si la sesion ya caduco

	echo "<p class='txt01'>La sesi&oacute;n ha caducado.\n";
	echo "<p><a href='/index.php' class='link01'>Volver a ingresar</a>\n";
}

require('../libs/footer.php');

?>