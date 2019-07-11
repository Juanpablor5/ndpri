<?php
session_start();
require('../libs/conexionbd.lib.php');
require_once('../libs/auth.php');

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
  $fecinicio = ($anno - 1).'-10-01';//la idea es que se pueda parametrizar la fecha de inicio
  
  if (isset($_POST['idtipform'])) {
	$idtipform = $_POST['idtipform'];
	$nomform = $_POST['nomform'];
  } else {
	$idtipform = $_GET['idtipform'];
	$nomform = $_GET['nomform'];
  }	

  echo "<script type='text/javascript' src='../js/valid.js'></script>\n"; // script de validacion js

  echo"
  <br>
  <form id='form1' name='form1' method='post' action=''>
    <table width='40%' border='0' align='center' cellpadding='0' cellspacing='0'>
      <tr>
        <td>Filtrar por A&ntilde;o</td>
        <td>Filtrar por Mes</td>
        <td rowspan='2' align='center' valign='middle'><label>
          <button name='buscar' type='submit' id='buscar' style='background-color:#FFF;border:#FFF'><img src='./img/search_f2.png' width='29' height='29' title='Buscar'></button>
        </label></td>";
        echo "<td rowspan='2' align='center' valign='middle'><a href='".$nomform."?idtipform=".$idtipform."' target='_parent'><img src='./img/new_01.png' width='29' height='29' border='0' title='Nuevo registro'/></a></td>";
        echo "<td rowspan='2' align='center' valign='middle'><a href='#' target='_parent'><img src='./img/excel_01.png' width='29' height='29' border='0' title='Exportar a Excel'/></a></td>
        <td rowspan='2' align='center' valign='middle'><a href='#' target='_parent'><img src='./img/pdf_01.png' width='29' height='29' border='0' title='Exportar a PDF'/></a></td>
        <td rowspan='2' align='center' valign='middle'><a href='#' target='_parent'><img src='./img/help_01.png' width='29' height='29' border='0' title='Ayuda'/></a></td>
      </tr>
      <tr>";
        echo "<td><label>
          <select name='anno_actu' id='anno_actu'>
          <option value=''>Seleccione ...</option>\n";
          for ($i=2018;$i<=$anno+1;$i++) {
            echo "<option value='".$i."'>".$i."</option>";
          }
        echo "</select>
        </label></td>";
        echo "<td><label>
          <select name='mes_moni' id='mes_moni'>
          <option value='' selected='selected'>.:SELECCIONE MES:.</option>
          <option value='01'>ENERO</option>
          <option value='02'>FEBRERO</option>
          <option value='03'>MARZO</option>
          <option value='04'>ABRIL</option>
          <option value='05'>MAYO</option>
          <option value='06'>JUNIO</option>
          <option value='07'>JULIO</option>
          <option value='08'>AGOSTO</option>
          <option value='09'>SEPTIEMBRE</option>
          <option value='10'>OCTUBRE</option>
          <option value='11'>NOVIEMBRE</option>
          <option value='12'>DICIEMBRE</option>
          </select>
        </label></td>
      </tr>
    </table>
  </form>
  ";

  $mes_moni = $_POST['mes_moni'];
  $anno_actu = $_POST['anno_actu'];
  //echo $cod_plan;

  $qrywhere = ' and tip_form = '.$idtipform;

  //Inicio validaciones basicas

  if (!empty($mes_moni)) {
      $qrywhere.= " and month(fec_form) = $mes_moni";
    }

  if (!empty($anno_actu)) {
    $qrywhere.= " and year(fec_form) = $anno_actu";
  } else {
    $qrywhere.= " and year(fec_form) = $anno";
  }

      $sql = " 	SELECT a.*, b.nom_tipo, concat(c.nombre,' ',c.apellido) as nom_empl
			FROM dp_mafor a, dp_tipfo b, adm_terc c
			WHERE a.tip_form = b.id and a.cod_empl = c.cod_terc".$qrywhere."
      ORDER BY id DESC";


  $_pagi_sql = $sql;
  $_pagi_conteo_alternativo = true;
  $_pagi_cuantos = 200;



  include("../libs/paginator.inc.php");

  echo "<table width='70%' border='0' align='center' cellpadding='0' cellspacing='0'>";
  echo "<tr>";
  echo "<td><b>TOTAL DE RESULTADOS : </b>".$_pagi_totalReg."</td>";
  echo "</tr>";
  echo "</table>";
  echo "<table width='70%' align='center' border='0' cellpadding='4' cellspacing='0'>\n";
  echo "<tr class='tit_tabla01' bgcolor='#74869C'>";
  echo "<td align='center'><b>ID</b></td>";
  echo "<td align='center'><b>Nombre de Formato</b></td>";
  echo "<td align='center'><b>Fecha de Formato</b></td>";
  echo "<td align='center'><b>Nombre Empleado Crea</b></td>";
  if ($idtipform==13) {
    echo "<td align='center'><b>Estado</b></td>";
  }
  

  $i = 1;

  while ($row = mysql_fetch_array($_pagi_result)) {
    extract($row);
    // $frm = $id;

    $swc = $i % 2; // switch para alternar color de las filas

    if ($swc == 1) {
      $cf = '#EAEAEA';
    } else {
      $cf = '#FFFFFF';
    }

    echo "<tr bgcolor='$cf'>";

    if (empty($id)) {
      echo "<td align='center'>&nbsp;</td>\n";
    } else {
      echo "<td align='center'><a href='".$nomform."?idtipform=$idtipform&idf=$id'><b>$id</b></a></td>\n";
    }
	
	if (empty($tip_form)) {
      echo "<td align='center'>&nbsp;</td>\n";
    } else {
      echo "<td align='center'><a href='".$nomform."?idtipform=$idtipform&idf=$id'><b>$nom_tipo</b></a></td>\n";
    }

    if (empty($fec_form)) {
      echo "<td align='center'>&nbsp;</td>\n";
    } else {
      echo "<td align='center'><a href='".$nomform."?idtipform=$idtipform&idf=$id'><b>$fec_form</b></a></td>\n";
    }

    if (empty($cod_empl)) {
      echo "<td align='center'>&nbsp;</td>\n";
    } else {
      echo "<td align='center'><a href='".$nomform."?idtipform=$idtipform&idf=$id'><b>$nom_empl</b></a></td>\n";
    }

    $sqldeta = "SELECT emp_ver1 from dp_def03 where cod_maes=".$id;
    $rstdeta = mysql_query($sqldeta) or die ('Error en consulta de alertas: ' . mysql_error());

    $nrwdeta = mysql_num_rows($rstdeta);

    $imagen="";
    $mensaje="";
    $cont=0;
    $terminar=false;
    while (($alrow = mysql_fetch_array($rstdeta)) && !$terminar && $nrwdeta>0) {
      extract($alrow);
      if($emp_ver1==0){
        $cont++;
        $terminar=true;
      }
    }
    
    if ($cont==0 && $nrwdeta==0) {
      $imagen="./img/icono_punto_azul.png"; 
      $mensaje="No se han ingresado datos.";
    }elseif($cont==0 ){
      $imagen="./img/fin_01.png";
      $mensaje="Se han verificado todos los datos.";
    }else{
      $imagen="./img/alerta_retrazo_01.gif";
      $mensaje="Faltan datos por verificar.";
    }

    if ($idtipform==13) {
      echo "<td align='center' valign='middle'><img src='".$imagen."' width='16' height='16' border='0' title='".$mensaje."'/></td>";
    }
    

    echo "</tr>\n";

    $i++;

  }
  
  echo "</table>\n";
  echo "<table width='70%' border='0' align='center' cellpadding='0' cellspacing='0'>";
  echo "<tr>";
    echo "<td><b>TOTAL DE RESULTADOS : </b>".$_pagi_totalReg."</td>";
  echo "</tr>";
  echo "</table>";
  echo "<p>&nbsp;</p>";
  echo "<table width='70%' border='0' align='center' cellpadding='0' cellspacing='0'>";
  echo "<tr>";
    echo "<td align='center'>".$_pagi_navegacion."</td>";
  echo "</tr>";
  
  echo "</table>";
  echo" <a href='index.php?idtipform=13&nomform=dp_def03.php' class='mnulink'>REGRESAR</a>";
  echo "<p>&nbsp;</p>";
  echo "<p>&nbsp;</p>";

  require('../libs/footer.php');

} else {
  header('Location: ../index.php');
}
mysql_close($conn);
?>