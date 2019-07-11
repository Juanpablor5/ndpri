<?php

if (isset($_SESSION['nombre_usuario'])) {
	echo "<head><title>MCS Consultoria y Monitoreo Ambiental S.A.S. - LIMS :: USUARIO : $_SESSION[nombre_usuario]</title>\n";
} else {
	echo "<head><title>MCS Consultoria y Monitoreo Ambiental S.A.S. - LIMS :: Ingrese con su numero de usuario</title>\n";
}

$tiempo = microtime();
$tiempo = explode(" ",$tiempo);
$tiempo = $tiempo[1] + $tiempo[0];
$tiempoInicio = $tiempo;

$id_usuario = $_SESSION['idusuario'];

echo "<link href='../css/css01.css' rel='stylesheet' type='text/css' />";
echo "<link type='text/css' rel='stylesheet' href='../css/src/css/jscal2.css' />";
echo "<link type='text/css' rel='stylesheet' href='../css/src/css/border-radius.css' /></head>\n";

echo "<script type=\"text/javascript\">

var currenttime = '".date("F d, Y H:i:s", time())."'

var montharray=new Array(\"Enero\",\"Febrero\",\"Marzo\",\"Abril\",\"Mayo\",\"Junio\",\"Julio\",\"Agosto\",\"Septiembre\",\"Octubre\",\"Noviembre\",\"Diciembre\")
var serverdate=new Date(currenttime)

function padlength(what){
	var output=(what.toString().length==1)? \"0\"+what : what
	return output
}

function displaytime(){
	serverdate.setSeconds(serverdate.getSeconds()+1)
	var datestring=montharray[serverdate.getMonth()]+\" \"+padlength(serverdate.getDate())+\", \"+serverdate.getFullYear()
	var timestring=padlength(serverdate.getHours())+\":\"+padlength(serverdate.getMinutes())+\":\"+padlength(serverdate.getSeconds())
	document.getElementById(\"servertime\").innerHTML=datestring+\" \"+timestring
}

window.onload=function(){
	setInterval(\"displaytime()\", 1000)
}

</script>

<script src=\"../css/src/js/jscal2.js\"></script>
<script src=\"../css/src/js/lang/es.js\"></script>

\n";

// echo "<body onload=\"updateClock(); setInterval('updateClock()', 1000 )\">\n";

echo "
<body>
<table width='100%' border='0'>
<tr width='100%'><td width='70%'><a href='../index_lims.php'><img src='../img/logo.jpg' alt='Antek S.A.' border='0'/></a></td>
<td width='30%' valign='middle' align='right'>
<a href='../login.php'><img src='../img/login_01.png' border='0' width='41' height='41' title='Login'/></a>
<a href='../index_lims.php'><img src='../img/log_03.png' border='0' width='41' height='41' /></a>
<a href='../clientes/directorio.php'><img src='../img/log_04.png' border='0' width='41' height='41' /></a>";
if ($id_usuario == 1 || $id_usuario == 8 || $id_usuario == 19){
  echo "<a href='../admin/index.php'><img src='../img/admin_01.png' border='0' width='41' height='41' /></a>";
}
if ($id_usuario == 1){
  echo "<a href='../clientes/adjuntos.php'><img src='../img/adjunto_01.png' border='0' width='41' height='41' /></a>
  <a href='../clientes/comentarios.php'><img src='../img/log_06.png' border='0' width='41' height='41' /></a>";
}
echo "
<img src='../img/log_05.png' border='0' width='41' height='41' />
</td>
</tr>
</table>\n";

//echo "<span id='servertime' class='clock' aling='right'></span><p>\n";
echo "<table width='100%' border='0'><tr><td align='right' bgcolor='#3C587D' class='clock2'><span id='servertime' class='clock' aling='right'></span><b>0</b></td></tr></table>\n";
//echo "<table width='100%' border='0'><tr><td><hr size='0' noshade='noshade' /></td></tr></table>\n";
echo "</body>";
?>