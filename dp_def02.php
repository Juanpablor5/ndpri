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
		$idf = $_POST['idf'];
    } else {
		$idtipform = $_GET['idtipform'];
		$idf = $_GET['idf'];
    }
	
	//echo $idtipform." ".$idf;
	
	if ($idf > 0) {
	
		$sqlmafor = "SELECT * FROM dp_mafor WHERE id = ".$idf;
		$rstmafor = mysql_query($sqlmafor) or die ('Error: dp_mafor' . mysql_error());
		$rowmafor = mysql_fetch_array($rstmafor);
		//echo $sqlmafor;
		$tipform = $rowmafor['tip_form'];
		$fecform = $rowmafor['fec_form'];
		$valst01 = $rowmafor['val_st01'];
		$valin01 = $rowmafor['val_in01'];
		$valin02 = $rowmafor['val_in02'];
		$valst02 = $rowmafor['val_st02'];
		$valfc01 = $rowmafor['val_fc01'];
		$fecreal = $rowmafor['fec_real'];
		$codempl = $rowmafor['cod_empl'];
	
	}

	echo "<script type='text/javascript' src='../js/valid.js'></script>\n"; // script de validacion js
?>	

<form id="form1" name="form1" method="post" action="fn_def02.php">
  <table width="70%" border="1" align="center" cellpadding="0" cellspacing="1">
    <tr>
      <td><table width="98%" border="0" align="center" cellpadding="2" cellspacing="2">
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center" valign="middle"><strong> DATOS PRIMARIOS SUELOS<br />
            <br />
          </strong></td>
        </tr>
        <tr>
          <td align="center" valign="middle"><strong> FECHA DE ANALISIS
            (A&Ntilde;O-MES-DIA):
			<?php
			
				if (!empty($fecform)) {
					echo "<strong>".$fecform."</strong>";
				} else {
					echo '<input name="fec_form" type="text" id="fec_form" size="15"/>';
				}
			
			?>
            <br />
            <br />
          </strong></td>
        </tr>
        <tr>
          <td align="right" valign="middle"><br />
            <?php 
				if ($idf > 0) {
					echo '<input type="submit" name="modifform" id="modifform" value="Modificar Registro Tecnico" />';
				} else {
					echo '<input type="submit" name="crearform" id="crearform" value="Crear Registro Tecnico" />';
				}
			?>
            <br /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="idtipform" id="idtipform" value="<?php echo $idtipform; ?>"/>
							<input type="hidden" name="idf" id="idf" value="<?php echo $idf; ?>"/></td>
        </tr>
      </table></td>
    </tr>
  </table>
</form>
<?php
if ($idf > 0) {
?>
	<form id="form2" name="form2" method="post" action="fn_def02.php">
	  <table width="70%" border="1" align="center" cellpadding="0" cellspacing="1">
		<tr>
		  <td><table width="98%" border="0" align="center" cellpadding="2" cellspacing="2">
			<tr>
			  <td colspan="4" align="center" valign="middle"><strong>INGRESO DE RESULTADOS<br />
				<br />
			  </strong></td>
			</tr>
			<tr>
			  <td><strong>PARAMETRO:</strong></td>
			  <td><strong>
				<select name="cod_para" id="cod_para" style="width:300px">
				<?php
				
					$sqlmatr = "SELECT a.cod_tipf, a.cod_para, b.parametro, b.unidades, b.tecnica, b.metodo
								FROM dp_patip a, lms_lstparam b
								WHERE a.cod_para = b.id and a.cod_tipf = ".$idtipform."
								ORDER BY a.id";			
					$rstmatr = mysql_query($sqlmatr) or die ('Error Select dp_patip: ' . mysql_error());
					
					while ($rowmatr = mysql_fetch_array($rstmatr)) {
						echo "<option value='$rowmatr[cod_para]' \>$rowmatr[parametro]::$rowmatr[unidades]::$rowmatr[tecnica]::$rowmatr[metodo]</option>\n";	
					}
				
				?>
				</select>
			  </strong></td>
			  <td><strong>No MUESTRA</strong></td>
			  <td><select name="cod_mues" id="cod_mues">
			  <?php
				
					$sqlmatr = "select id, nroantek, fecrecep from lms_muestras where fecrecep >= date_sub(CURDATE(),interval 15 day) order by nroantek desc";			
					$rstmatr = mysql_query($sqlmatr) or die ('Error Select dp_patip: ' . mysql_error());
					
					echo "<option value='945719'>CALIBRACION</option>\n";
					
					while ($rowmatr = mysql_fetch_array($rstmatr)) {
						echo "<option value='$rowmatr[id]' \>$rowmatr[nroantek]</option>\n";	
					}
				
				?>
			  </select>
			    <strong>Duplicado:</strong>
			    <input name="mue_dupl" type="checkbox" id="mue_dupl" value="1" />
			  <label for="mue_dupl"></label></td>
			</tr>
			<tr>
			  <td><strong>PESO DE MUESTRA (g):</strong></td>
			  <td><strong>
				<input type="text" name="val_in01" id="val_in01" />
			  </strong></td>
			  <td><strong>PORCENTAJE DE HUMEDAD:</strong></td>
			  <td><label for="val_in02"></label>
			  <input type="text" name="val_in02" id="val_in02" /></td>
			</tr>
			<tr>
			  <td><strong>PESO CORREGIDO (g):</strong></td>
			  <td><label for="val_in03"></label>
			  <input type="text" name="val_in03" id="val_in03" /></td>
			  <td><strong>AFORO:</strong></td>
			  <td><label for="val_st01"></label>
			  <input type="text" name="val_st01" id="val_st01" /></td>
			</tr>
			<tr>
			  <td><strong>DILUCION:</strong></td>
			  <td><input type="text" name="val_in04" id="val_in04" /></td>
			  <td colspan="2">&nbsp;</td>
		    </tr>
			<tr>
			  <td colspan="4" align="center"><strong>OBSERVACIONES:<br />
				<label for="obs_erva"></label>
				<textarea name="obs_erva" id="obs_erva" cols="120" rows="2"></textarea>
			  </strong></td>
			</tr>
			<tr>
			  <td colspan="4" align="right" valign="middle">
			  <input type="hidden" name="idtipform" id="idtipform" value="<?php echo $idtipform; ?>"/>
			  <input type="hidden" name="idf" id="idf" value="<?php echo $idf; ?>"/>
			  <input type="submit" name="insertregi" id="insertregi" value="Ingresar Registro" /></td>
			</tr>
		  </table></td>
		</tr>
	  </table>
	</form>
	
	<?php
		$sqldeta = "select a.*, b.parametro as nom_para, c.nroantek as num_mues, concat(d.nom_empl,' ',d.pri_apel,' ',d.seg_apel) as nom_repo
					from dp_def02 a, lms_lstparam b, lms_muestras c, rh_emple d
					where a.cod_para = b.id and a.cod_mues = c.id and a.cod_emp1 = d.cod_empl and cod_maes = ".$idf."
					order by id desc";			
		$rstdeta = mysql_query($sqldeta) or die ('Error Select dp_def02 a, lms_lstparam b, lms_muestras c, rh_emple d: ' . mysql_error());
		$nrwdeta = mysql_num_rows($rstdeta);
	?>
	<p><strong>Cantidad de Registros: </strong><?php echo $nrwdeta;?></p>
	
	<form id="form3" name="form3" method="post" action="fn_def02.php">
	
		<table width="100%" border="1" cellspacing="0" cellpadding="2">
		  <tr>
			<td align="center" valign="middle"><strong>No Analisis</strong></td>
			<td align="center" valign="middle"><strong>No Muestra</strong></td>
			<td align="center" valign="middle"><strong>Duplicado</strong></td>
			<td align="center" valign="middle"><strong>Peso de Muestra (g)</strong></td>
			<td align="center" valign="middle"><strong>% de Humedad</strong></td>
			<td align="center" valign="middle"><strong>Peso Corregido (g)</strong></td>
			<td align="center" valign="middle"><strong>Aforo</strong></td>
			<td align="center" valign="middle"><strong>Dilucion</strong></td>
			<td align="center" valign="middle"><strong>Observaciones</strong></td>
			<td align="center" valign="middle"><strong>Analista</strong></td>
			<td align="center" valign="middle"><strong>Verifico</strong></td>
		  </tr>
		  <?php
			
			$i = 1;
			
			while ($rowdeta = mysql_fetch_array($rstdeta)) {
				extract($rowdeta);
				$frm = $id;

				$swc = $i % 2; // switch para alternar color de las filas

				if ($swc == 1) {
				  $cf = '#EAEAEA';
				} else {
				  $cf = '#FFFFFF';
				}

				echo "<tr bgcolor='$cf'>";
				
				if (empty($num_con2)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$num_con2</td>\n"; }
				if ($num_mues == 0) {
					echo "<td>$nom_para</td>\n";
				} else {
					if (empty($num_mues)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$num_mues</td>\n"; }
				}
				if (empty($mue_dupl)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$mue_dupl</td>\n"; }
				if (empty($val_in01)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$val_in01</td>\n"; }
				if (empty($val_in02)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$val_in02</td>\n"; }
				if (empty($val_in03)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$val_in03</td>\n"; }
				if (empty($val_st01)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$val_st01</td>\n"; }
				if (empty($val_in04)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$val_in04</td>\n"; }
				if (empty($obs_erva)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$obs_erva</td>\n"; }
				if (empty($nom_repo)) { echo "<td>&nbsp;</td>\n";} else { echo "<td>$nom_repo</td>\n"; }
				if (empty($cod_emp2)) { echo "<td align='center'><input type='checkbox' name='campo[]' value=$id/></td>\n";} else { echo "<td>$cod_emp2</td>\n"; }
				
				echo "</tr>";
				
				$i++;
			}
		  
		  ?>
		  
		  <tr>
		    <td colspan="10" align="right" valign="middle"><input type="hidden" name="idtipform" id="idtipform" value="<?php echo $idtipform; ?>"/>
            <input type="hidden" name="idf" id="idf" value="<?php echo $idf; ?>"/></td>
		    <td align="center" valign="middle"><input type="submit" name="verificar" id="verificar" value="Verificar" /></td>
	      </tr>
		
		</table>
	
	</form>
	
	<p><strong>Cantidad de Registros:</strong> <?php echo $nrwdeta;?></p>
<?php
}  
?>
<?php
	require('../libs/footer.php');

} else {
	
	header('Location: ../index.php');
	
}

mysql_close($conn);

?>