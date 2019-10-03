<?php
date_default_timezone_set('America/Lima');
set_error_handler('exceptions_error_handler');

	function exceptions_error_handler($severity, $message, $filename, $lineno) {
		if (error_reporting() == 0) {
		//return;
		}
		if (error_reporting() & $severity) {
		//throw new Exception($message, 0, $severity, $filename, $lineno);
		}
	}
class portada extends App{
    public function index(){
      if(!isset($_SESSION["usuario"])){
          $this->vista->reenviar("index", "acceso");
      }
      $this->vista->setTitle("Inicio");
    }
    public function cerrar(){
      unset($_SESSION);
      session_destroy();
      $this->vista->reenviar("index");
    }
    public function list_modulos()
    {
      $modelo = new modeloPortada();
      //$nivel = $_SESSION["usuario"][0]["kpi_roles_id"];
      //$bd = isset($_SESSION["usuario"][0]["database_name"]) ? $_SESSION["usuario"][0]["database_name"] : 'portal-kpi' ;
      $usuario = "SELECT kpi_roles_id FROM kpi_usuarios WHERE id=".$_SESSION['usuario'][0]['id']." and estado = 1 limit 1";
      $usuario = $modelo->executeQuery( $usuario );
      $_SESSION["nivelusuario"] = $usuario[0]['kpi_roles_id'];
      $modulos = "SELECT * FROM kpi_modulos WHERE estado = 1";
      $modulos = $modelo->executeQuery( $modulos );
      $tree = $this->buildTree($modulos);
      $treeHtml = $this->buildTreeHtml($tree);
      print_r($treeHtml);
    }
    public function buildTree($elements, $parentId = 0) {
      $branch = array();
      foreach ($elements as $element) {
          if ($element['parent_id'] == $parentId) {
              $children = $this->buildTree($elements, $element['id']);
              if ($children) {
                  $element['children'] = $children;
              }
              $branch[] = $element;
          }
      }
      return $branch;
  }
  public function buildTreeHtml($elements,$opt="")
  {
    $branch = array();
    $li = '';
    foreach ($elements as $element)
    {
      if (in_array($_SESSION["nivelusuario"],(explode(',',$element["niveles"])))) {
        $li = $li  . (isset($element['children']) ? ('
                      <li class="menu-item-has-children dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true">
                            <i class="menu-icon ' . $element["icon"] . '"></i>' . $element['nombre'] .'
                          </a>
                          <ul class="sub-menu children dropdown-menu">
                          '. $this->buildTreeHtml($element['children'],'childs').'
                          </ul>
                          </li>
                        ') :
                          ( in_array($_SESSION["nivelusuario"],(explode(',',$element["niveles"]))) ? ('<li data-url="'.$element['url'].'">
                            <i class="'.$element["icon"].'"></i>
                            <a style="font-size:1em;" href="#'.$element['url'].'" class="menu_direct"> '.$element['nombre'].'</a>
                          </li>') : '' ) ) ;
      }


    }
    return $li;
  }
    public function enviar(){
        $this->vista->reenviar("index", "portal");
    }

    public function cargar_datos(){

      if( $_POST['tabla'] && $_POST['where'] ){

        $_POST['where']["estado"] = 1;
        $modelo = new modeloPortada();
        $res = $modelo->selectData( $_POST['tabla'], $_POST['where'] );
        if ($res) {
          echo json_encode(array( "atributos"=>$res )) ;
        }else{
          return false;
        }
      }else{
        return false;
      }

    } 
     
    /*public function cargar_datos_sqlserver(){

        $modelo = new modeloPortada();
        $mes = date('n');

        $semestre = $mes>6 ? 2 :1 ;
        $where = array("Estado"=>array("Estado","Activo"));
        $campos = array('Todo'=>'count(*) as total_cantidad');
        $fecha = date('Y-m-d H:i:s');

        $groupby='';
        $tickets_totales = $modelo->selectRowDataAll( 'kpi_asistencia_personal', $campos, $where, $groupby );
        if ($tickets_totales) {
          $result = $tickets_totales[0]["total_cantidad"];
          $campos_insert = array("total_personal"=>$result,"fecha_modificacion"=>$fecha);
          $modelo->updateDataAll('kpi_indicador_ref19',$campos_insert,array("anio"=>array('anio',date('Y',strtotime($fecha) ) ),'semestre'=>array('semestre',$semestre) ) );
        }else{
          return false;
        }
    }*/
    public function cargar_excel(){
      if ($_SESSION["usuario"][0]["kpi_roles_id"] == '2') {
        $modelo = new modeloPortada();
      $res = json_decode(archivoExcel(),true);$response=true;
      if ($res["Error"]=="") {
        $anio = ($_POST["anio"]);
        $mes = ($_POST["mes"]);
        $exist_delete = $modelo->deleteData("kpi_indicador_ref104",array("anio"=>$anio,"mes"=>$mes));
        $Cabeceras = ["llamadas_accesadas_ivr","llamadas_recibidas","llamadas_atendidas","llamadas_atendidas_seg","llamadas_abandonadas","nivel_servicio","nivel_atencion","TMO","tiempo_total_ivr"];
        $x=0;
        $fecha = date('Y-m-d H:i:s');
        $usuario = $_SESSION["usuario"][0]["id"];
        $campos_auditoria = array("dia"=>"dia","anio"=>$anio,"mes"=>$mes,"fecha_creacion"=>$fecha,"fecha_modificacion"=>$fecha,"kpi_usuario_registro"=>$usuario,"kpi_usuario_modificacion"=>$usuario);
        $data = $res["Success"];
        $values=[];$Cabeceras_new=[];$values_excel=[];
        foreach ($data as $key_row => $row) {
          $campos_insert=[];$campos_insert_data=[];
          foreach ($row as $key_column => $column) {
            if ($Cabeceras[$key_column]=="nivel_servicio" || $Cabeceras[$key_column]=="nivel_atencion") {
              $column[1] = $column[1] * 100;
            }
            if ($key_row==0) {
              $Cabeceras_new[] = $Cabeceras[$key_column];
            }

            $campos_insert[]=round($column[1],1);
            $campos_insert_data[]=round($column[1],1);
            if ((count($row)-1)==$key_column) {
              foreach ($campos_auditoria as $key => $value) {
                if ($value=="dia") {
                  $value =$key_row+1;
                }
                $campos_insert[] ='"'.$value.'"';
                $campos_insert_data[] =$value;
              }
            }

          }
          if ($key_row==0) {
            foreach (array_keys($campos_auditoria) as $key => $value) {
              $Cabeceras_new[] = $value;
            }
          }
          $values_excel[]=$campos_insert_data;
          $values[]='('.implode(',',$campos_insert).')';
        }
        //campos de auditoria
        $insert = $modelo->insertDataMasivo("kpi_indicador_ref104",$Cabeceras_new,$values);
        if (!$insert) {
          $response = false;
        }else{
          $response = true;
        }
        //$campos_insert = array("llamadas_recibidas"=>$response,"fecha"=>$fecha,"fecha_creacion"=>$fecha);

      }else{
        $response=false;
      }

      echo json_encode(array("resultado"=>$response));
      }else{
        echo json_encode(array("resultado"=>false, "mensaje"=>"No posee permisos para cargar este excel" ));
      }

    }
     
    
public function borrar_registro(){
	$modelo = new modeloPortada();
	if( $_POST['tabla'] && $_POST['where'] ){
	$res = $modelo->deleteData( $_POST['tabla'], $_POST['where']);
		if ($res) {
		echo json_encode(array("resultado"=>true )) ;
		}else{
		return false;
		}
	}
}
 public function is_array_($array){
	try {
		foreach (array_keys($array) as $value) {
			if (!is_numeric($value)) {
				return false;
			}
		}
		return true;
	} catch (\Throwable $th) {
		return false;
	}
 }
 	
 	public function cargar_archivo(){
		
		$upload_folder  = APP."/cargas/";
		$destino_folder  = APP."/cargasleidos/";
		/*$handle = fopen($upload_folder, "r");
		$contents = fread($handle, filesize($upload_folder));*/

		$directorio = opendir($upload_folder); //ruta actual
		while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
		{
			if (!is_dir($archivo))//verificamos si es o no un directorio
			{
				$nombre_archivo = basename($archivo);
				//$tipo_archivo   = $_FILES['archivo']['type'];
				//$tamano_archivo = $_FILES['archivo']['size'];
				//$tmp_archivo    = $_FILES['archivo']['tmp_name'];
				//$extension		= pathinfo($nombre_archivo, PATHINFO_EXTENSION);
				//echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
			
				/*$handle = fopen("/home/rasmus/file.gif", "wb");
				$handle = fopen("http://www.example.com/", "r");
				$handle = fopen("ftp://user:password@example.com/somefile.txt", "w");*/
				/*$nombre_archivo = $_FILES['archivo']['name'];
				$tipo_archivo   = $_FILES['archivo']['type'];
				$tamano_archivo = $_FILES['archivo']['size'];
				$tmp_archivo    = $_FILES['archivo']['tmp_name'];
				$extension		= pathinfo($nombre_archivo, PATHINFO_EXTENSION);*/
				$result=[];
					//$fichero_subido = $upload_folder . basename($nombre_archivo);
				
				//if (move_uploaded_file($tmp_archivo, $fichero_subido)) 
				if ($handle = fopen(($upload_folder.$archivo), "r")) 
				{
					//echo "subido";

					//$contents = fread($handle, filesize($upload_folder.$archivo));
					//print_r($contents);
					$modelo = new modeloPortada();
					$cabeceras=[];
					while (($line = fgets($handle)) !== false) {
						
						if($i==0){
							$cabeceras = explode(",",$line.",Estado,act");
							$i++;
							continue;
						}
						$explFila =explode(",",$line);
						$explFila[]="0";
						$explFila[]=$nombre_archivo;
						$explFila = array_combine($cabeceras,$explFila);
						
						$explFila["Estado"] = "0";
						$explFila["act"] = $nombre_archivo; 
						$fecha = explode("/",$explFila["TransactionDate"]);
						$explFila["TransactionDate"] = $fecha[2]."-".$fecha[1]."-".$fecha[0]; 
						$fecha = explode("/",$explFila["UploadDate"]);
						$explFila["UploadDate"] =  $fecha[2]."-".$fecha[1]."-".$fecha[0]; 
						$result[] ="('". implode("','",$explFila) ."')";
					}
					//echo $upload_folder.$archivo;
					//echo $destino_folder.$archivo;
					fclose($handle);
					//rename (($upload_folder.$archivo),($destino_folder.$archivo."bk"));
					$modelo->insertDataMasivo("csvtabla",$cabeceras, $result);
					/*$modelo->insertDataMasivo("pnr_pax",array("pnr_cod","numdoc","apellido","nombre","pax","tipodoc","boleto"), $pasajeros_reserva);
					$modelo->insertDataMasivo("pnr_vuelo",array("pnr_cod","trama","vuelo"), $arr_pnr_vuelo);*/
					
				}
			}
		}
	}
} 
