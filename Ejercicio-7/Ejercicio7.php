<?php
session_start();
class BaseDatos{
    protected $datos;
    protected $form;
    public function __construct(){
        $this->form="";
    }

    public function inicializar(){
        $this->crearBaseDatos();
        $this->crearTabla();
        $this->cargarDatos();
    }

    public function crearBaseDatos(){
        $this->datos=new mysqli("localhost","DBUSER2022","DBPSWD2022","test");
        if($this->datos->connect_error)
            echo "<h2>".$this->datos->connect_error."</h2>";
        $crearB="CREATE DATABASE IF NOT EXISTS EJERCICIO7 COLLATE utf8_spanish_ci";
        if($this->datos->query($crearB)===FALSE){
            echo "<h2>Error al crear Base de datos</h2>";
        }
    }

    public function crearTabla(){
        $this->conexion();
        $crearT="CREATE TABLE IF NOT EXISTS Biblioteca(
            idBiblioteca VARCHAR(255),
            Nombre VARCHAR(255),
            PRIMARY KEY(idBiblioteca)
            );";
        $this->datos->real_query($crearT);

        $this->conexion();
        $crearT="CREATE TABLE IF NOT EXISTS Escritor(
            idEscritor VARCHAR(255),
            Nombre VARCHAR(255),
            Apellidos VARCHAR(255),
            Edad int,
            PRIMARY KEY(idEscritor)
            );";
        $this->datos->real_query($crearT);

        $this->conexion();
        $crearT="CREATE TABLE IF NOT EXISTS Genero(
            idGenero VARCHAR(255),
            Nombre VARCHAR(255),
            PRIMARY KEY(idGenero)
            );";
        $this->datos->real_query($crearT);
        
        $this->conexion();
        $crearT="CREATE TABLE IF NOT EXISTS Editorial(
            idEditorial VARCHAR(255),
            Nombre VARCHAR(255),
            PRIMARY KEY(idEditorial)
            );";
        $this->datos->real_query($crearT);

        $this->conexion();
        $crearT="CREATE TABLE IF NOT EXISTS Libro(
            idLibro VARCHAR(255),
            Nombre VARCHAR(255),
            nPaginas int,
            idEscritor VARCHAR(255),
            idGenero VARCHAR(255),
            idEditorial VARCHAR(255),
            PRIMARY KEY(idLibro),
            FOREIGN KEY(idEscritor) references Escritor(idEscritor),
            FOREIGN KEY(idGenero) references Genero(idGenero),
            FOREIGN KEY(idEditorial) references Editorial(idEditorial)
            );";
        $this->datos->real_query($crearT);

        $this->conexion();
        $crearT="CREATE TABLE IF NOT EXISTS Bibliotecalibro(
            id VARCHAR(255),
            biblioteca_id VARCHAR(255),
            libro_id VARCHAR(255),
            PRIMARY KEY(id),
            FOREIGN KEY(biblioteca_id) references Biblioteca(idBiblioteca),
            FOREIGN KEY(libro_id) references Libro(idLibro)
            );";
        $this->datos->real_query($crearT);
    }

    public function cargardatos(){
        $result = array();
		$csv = file("bibliotecas.csv");
		foreach($csv as $line) {
			$result[] =  str_getcsv($line);  
		}
       for($i=0;$i<count($result);$i++) {
            $this->añadirBiblioteca($result[$i][0],$result[$i][1]);
		}
        $result = array();
        $csv = file("escritores.csv");
		foreach($csv as $line) {
			$result[] =  str_getcsv($line);  
		}
        for($i=0;$i<count($result);$i++) {
            $this->añadirescritor($result[$i][0],$result[$i][1],$result[$i][2],$result[$i][3]);
		}
          $result = array();
         $csv = file("editoriales.csv");
		foreach($csv as $line) {
			$result[] =  str_getcsv($line);  
		}
        for($i=0;$i<count($result);$i++) {
            $this->añadirEditorial($result[$i][0],$result[$i][1]);
		}
        $result = array();
      $csv = file("generos.csv");
		foreach($csv as $line) {
			$result[] =  str_getcsv($line);  
		}
        for($i=0;$i<count($result);$i++) {
            $this->añadirGenero($result[$i][0],$result[$i][1]);
		}
        $result = array();
        $csv = file("libros.csv");
		foreach($csv as $line) {
			$result[] =  str_getcsv($line);  
		}
        for($i=0;$i<count($result);$i++) {
            $this->añadirLibro($result[$i][0],$result[$i][1],$result[$i][2],$result[$i][3],$result[$i][4],$result[$i][5]);
		}
        $result = array();
        $csv = file("bibliotecas_libros.csv");
		foreach($csv as $line) {
			$result[] =  str_getcsv($line);  
		}
        for($i=0;$i<count($result);$i++) {
            $this->añadirBibliotecaLibro($result[$i][0],$result[$i][1],$result[$i][2]);
		}
    }

    public function añadirBibliotecaLibro($id,$libro_id,$biblioteca_id){
        $this->form="";
        $this->conexion();
        $add=$this->datos->prepare("
            insert into Bibliotecalibro (id,biblioteca_id,libro_id)
            values (?,?,?)
            ");
        $add->bind_param("sss",$id,$biblioteca_id,$libro_id);
        $add->execute();
    }

    public function añadirLibro($id,$nombre,$nPaginas,$idEscritor,$idGenero,$idEditorial){
        $this->form="";
        $this->conexion();
        $add=$this->datos->prepare("
            insert into Libro (idlibro,nombre,nPaginas,idEscritor,idGenero,idEditorial)
            values (?,?,?,?,?,?)
            ");
        $add->bind_param("ssssss",$id,$nombre,$nPaginas,$idEscritor,$idGenero,$idEditorial);
        $add->execute();
    }

    public function añadirBiblioteca($id,$nombre){
        $this->form="";  
        $this->conexion();
        $add=$this->datos->prepare('INSERT INTO Biblioteca(idBiblioteca,nombre)
            values (?,?) ;
            ');
        $add->bind_param("ss",$id,$nombre);
        $add->execute();
    }

    public function añadirGenero($id,$nombre){
        $this->form="";
        $this->conexion();
        $add=$this->datos->prepare("
            insert into Genero (idGenero,nombre)
            values (?,?)
            ");
        $add->bind_param("ss",$id,$nombre);
        $add->execute();
    }

    public function añadirEditorial($id,$nombre){
        $this->form="";
        $this->conexion();
        $add=$this->datos->prepare("
            insert into Editorial (idEditorial,nombre)
            values (?,?)
            ");
        $add->bind_param("ss",$id,$nombre);
        $add->execute();
    }

    public function añadirescritor($id,$nombre,$apellidos,$edad){
        $this->form="";
        $this->conexion();
        $add=$this->datos->prepare("
            insert into Escritor (idEscritor,nombre,apellidos,edad)
            values (?,?,?,?)
            ");
        $add->bind_param("ssss",$id,$nombre,$apellidos,$edad);
        $add->execute();
    }

     

    public function conexion(){
        $this->datos=new mysqli("localhost","DBUSER2022","DBPSWD2022","ejercicio7");
        if($this->datos->connect_error)
            echo "<h2>".$this->datos->connect_error."</h2>";
    }
    public function getFormulario(){
        return $this->form;
    }

    public function formularioBiblioteca(){
        $this->form="
            <h2>Añadir Biblioteca</h2>
            <label for='id1'>ID: </label>
            <input type='text' id='id1' name='id'/>
            <label for='nameB'>Nombre: </label>
            <input type='text' id='nameB' name='idB'/>
            <input type='submit' value='Añadir' name='añadirB'/>
        ";
    }

    public function formularioGenero(){
        $this->form="
            <h2>Añadir Genero</h2>
            <label for='id1'>ID: </label>
            <input type='text' id='id1' name='id'/>
            <label for='nameG'>Nombre: </label>
            <input type='text' id='nameG' name='idG'/>
            <input type='submit' value='Añadir' name='añadirG'/>
        ";
    }

    public function formularioEditorial(){
        $this->form="
            <h2>Añadir Editorial</h2>
            <label for='id1'>ID: </label>
            <input type='text' id='id1' name='id'/>
            <label for='nameE'>Nombre: </label>
            <input type='text' id='nameE' name='idEd'/>
            <input type='submit' value='Añadir' name='añadirEd'/>
        ";
    }

    public function formularioEscritor(){
        $this->form="
            <h2>Añadir Escritor</h2>
            <label for='id1'>ID: </label>
            <input type='text' id='id1' name='id'/>
            <label for='nameEs'>Nombre: </label>
            <input type='text' id='nameEs' name='idEs'/>
            <label for='apeEs'>Apellido: </label>
            <input type='text' id='apeEs' name='apellido'/>
            <label for='edad1'>Edad: </label>
            <input type='text' id='edad1' name='edad'/>
            <input type='submit' value='Añadir' name='añadirEs'/>
        ";
    }

    public function formularioLibro(){
        $this->form="
            <h2>Añadir Libro</h2>
            <label for='id1'>ID: </label>
            <input type='text' id='id1' name='id'/>
            <label for='nameL'>Nombre: </label>
            <input type='text' id='nameL' name='idL'/>
            <label for='pag'>Nº paginas: </label>
            <input type='text' id='pag' name='idP'/>
            <label for='esc'>Id del Escritor: </label>
            <input type='text' id='esc' name='idE'/>
            <label for='gen'>Id del Genero: </label>
            <input type='text' id='gen' name='idG'/>
            <label for='edi'>Id del Editorial: </label>
            <input type='text' id='edi' name='idEd'/>
            <input type='submit' value='Añadir' name='añadirL'/>
        ";
    }

    public function formularioBibliotecaLibro(){
        $this->form="
            <h2>Añadir Libro a Biblioteca</h2>
            <label for='id1'>ID: </label>
            <input type='text' id='id1' name='id'/>
            <label for='lib'>Id del Libro: </label>
            <input type='text' id='lib' name='idL'/>
            <label for='biblio'>Id de la biblioteca: </label>
            <input type='text' id='biblio' name='idB'/>
            <input type='submit' value='Añadir' name='añadirBL'/>
        ";
    }

    public function formularioAñadir(){
        $this->form="
            <input type='submit' value='Añadir Biblioteca' name='addB'/>
            <input type='submit' value='Añadir Genero' name='addG'/>
            <input type='submit' value='Añadir Escritor' name='addEs'/>
            <input type='submit' value='Añadir Editorial' name='addEd'/>
            <input type='submit' value='Añadir Libro' name='addL'/>
            <input type='submit' value='Añadir Libro a Biblioteca' name='addBL'/>
        ";
    }

    public function formularioBuscar(){
        $this->form="
            <input type='submit' value='Buscar Biblioteca' name='searchB'/>
            <input type='submit' value='Buscar Genero' name='searchG'/>
            <input type='submit' value='Buscar Escritor' name='searchE'/>
            <input type='submit' value='Buscar Editorial' name='searchEd'/>
            <input type='submit' value='Buscar Libro' name='searchL'/>
        ";
    }

    public function formularioBuscarEscritor(){
        $this->form="
            <h2>Buscar Escritor</h2>
            <label for='id1'>ID Escritor: </label>
            <input type='text' id='id1' name='idE'/>
            <input type='submit' value='Buscar' name='buscarE'/>
        ";
    }

    public function formularioBuscarLibro(){
        $this->form="
            <h2>Buscar Libro</h2>
            <label for='id1'>ID Libro: </label>
            <input type='text' id='id1' name='idL'/>
            <input type='submit' value='Buscar' name='buscarL'/>
        ";
    }

    public function buscarEscritor($idescritor){
        $this->conexion();
        $select=$this->datos->prepare('SELECT * From Escritor 
            where idEscritor=?
            ');
        $select->bind_param("s",$idescritor);
        $select->execute();
        $select->bind_result($id,$nombre,$apellidos,$edad);
        $select->fetch();
        $this->form="
            <h2>Datos del Escritor ".$id."</h2>
            <ul>
                <li>Nombre: ".$nombre."</li>
                <li>Apellidos: ".$apellidos."</li>
                <li>Edad: ".$edad."</li>
            </ul>
            <h3>Libros Escritos</h3>
            <ul>
            ";
        $this->conexion();
        $select=$this->datos->prepare('
            SELECT l.nombre From Libro l
            where l.idEscritor=? 
            ');
        $select->bind_param("s",$idescritor);
        $select->execute();
        $result=$select->get_result();
        while($fila=$result->fetch_array()){
            $this->form.="<li>Titulo:".$fila[0]." </li>";
        }
        $this->form.="</ul>";
    }
    public function formularioBuscarGenero(){
        $this->form="
            <h2>Buscar Genero</h2>
            <label for='id1'>ID Genero: </label>
            <input type='text' id='id1' name='idG'/>
            <input type='submit' value='Buscar' name='buscarG'/>
        ";
    }

    public function buscarGenero($idgenero){
        $this->conexion();
        $select=$this->datos->prepare('SELECT * From Genero 
            where idGenero=?
            ');
        $select->bind_param("s",$idgenero);
        $select->execute();
        $select->bind_result($id,$nombre);
        $select->fetch();
        $this->form="
            <h2>Datos del Genero ".$id."</h2>
            <ul>
                <li>Nombre: ".$nombre."</li>
            </ul>
            <h3>Libros de este genero</h3>
            <ul>
            ";
        $this->conexion();
        $select=$this->datos->prepare('
            SELECT l.nombre From Libro l
            where l.idGenero=? 
            ');
        $select->bind_param("s",$idgenero);
        $select->execute();
        $result=$select->get_result();
        while($fila=$result->fetch_array()){
            $this->form.="<li>Titulo:".$fila[0]." </li>";
        }
        $this->form.="</ul>";
    }
    public function formularioBuscarEditorial(){
        $this->form="
            <h2>Buscar Editorial</h2>
            <label for='id1'>ID Editorial: </label>
            <input type='text' id='id1' name='idEd'/>
            <input type='submit' value='Buscar' name='buscarEd'/>
        ";
    }

    public function buscarEditorial($ideditorial){
        $this->conexion();
        $select=$this->datos->prepare('SELECT * From Editorial 
            where idEditorial=?
            ');
        $select->bind_param("s",$ideditorial);
        $select->execute();
        $select->bind_result($id,$nombre);
        $select->fetch();
        $this->form="
            <h2>Datos de la Editorial ".$id."</h2>
            <ul>
                <li>Nombre: ".$nombre."</li>
            </ul>
            <h3>Libros de esta editorial</h3>
            <ul>
            ";
        $this->conexion();
        $select=$this->datos->prepare('
            SELECT l.nombre From Libro l
            where l.idEditorial=? 
            ');
        $select->bind_param("s",$ideditorial);
        $select->execute();
        $result=$select->get_result();
        while($fila=$result->fetch_array()){
            $this->form.="<li>Titulo:".$fila[0]." </li>";
        }
        $this->form.="</ul>";
    }

    public function buscarBiblioteca($idB){
        $this->conexion();
        $select=$this->datos->prepare('SELECT * From Biblioteca 
            where idBiblioteca=?
            ');
        $select->bind_param("s",$idB);
        $select->execute();
        $select->bind_result($id,$nombre);
        $select->fetch();
        $this->form="
            <h2>Datos de la Biblioteca ".$id."</h2>
            <ul>
                <li>Nombre: ".$nombre."</li>
            </ul>
            <h3>Libros que hay</h3>
            <ul>
            ";
        $this->conexion();
        $select=$this->datos->prepare('
            SELECT l.nombre From Libro l, BibliotecaLibro b
            where l.idLibro=b.libro_id and b.biblioteca_id=? 
            ');
        $select->bind_param("s",$idB);
        $select->execute();
        $result=$select->get_result();
        while($fila=$result->fetch_array()){
            $this->form.="<li>Titulo:".$fila[0]." </li>";
        }
        $this->form.="</ul>";
    }
    public function formularioBuscarBiblioteca(){
        $this->form="
            <h2>Buscar Biblioteca</h2>
            <label for='id1'>ID Biblioteca: </label>
            <input type='text' id='id1' name='idB'/>
            <input type='submit' value='Buscar' name='buscarB'/>
        ";
    }

    public function buscarLibro($id){
        $this->conexion();
        $select=$this->datos->prepare('SELECT * From Libro 
            where idLibro=?
            ');
        $select->bind_param("s",$id);
        $select->execute();
        $select->bind_result($id,$nombre,$npag,$escr,$gen,$edi);
        $select->fetch();
        $this->form="
            <h2>Datos del Libro ".$id."</h2>
            <ul>
                <li>Nombre: ".$nombre."</li>
                <li>Nº paginas: ".$npag."</li>
                <li>Escritor: ".$escr."</li>
                <li>Genero: ".$gen."</li>
                <li>Editorial: ".$edi."</li>
            </ul>
            <h3>Bibliotecas en las que se encuentra</h3>
            <ul>
            ";
        $this->conexion();
        $select=$this->datos->prepare('
            SELECT b.nombre From Biblioteca b, BibliotecaLibro l
            where l.libro_id=? and b.idbiblioteca=l.biblioteca_id 
            ');
        $select->bind_param("s",$id);
        $select->execute();
        $result=$select->get_result();
        while($fila=$result->fetch_array()){
            $this->form.="<li>Nombre:".$fila[0]." </li>";
        }
        $this->form.="</ul>";
    }
    
}
if(isset($_SESSION['baseDatos'])){
    
}else{
    $_SESSION['baseDatos']=new BaseDatos();
}
$form="";
if(count($_POST)>0){
    if(isset($_POST['inicializar']))
        $_SESSION['baseDatos']->inicializar();
    if(isset($_POST['añadirB'])){
        $_SESSION['baseDatos']->añadirBiblioteca($_POST['id'],$_POST['idB']);
    }
    if(isset($_POST['añadirG'])){
        $_SESSION['baseDatos']->añadirGenero($_POST['id'],$_POST['idG']);
    }
    if(isset($_POST['añadirEd'])){
        $_SESSION['baseDatos']->añadirEditorial($_POST['id'],$_POST['idEd']);
    }
    if(isset($_POST['añadirEs'])){
        $_SESSION['baseDatos']->añadirEscritor($_POST['id'],$_POST['idEs'],$_POST['apellido'],$_POST['edad']);
    }
    if(isset($_POST['añadirL'])){
        $_SESSION['baseDatos']->añadirLibro($_POST['id'],$_POST['idL']
                        ,$_POST['idP'],$_POST['idE'],$_POST['idG'],$_POST['idEd']);
    }
    if(isset($_POST['añadirBL'])){
        $_SESSION['baseDatos']->añadirBibliotecaLibro($_POST['id'],$_POST['idL'],$_POST['idB']);
    }
    if(isset($_POST['addB'])){
        $_SESSION['baseDatos']->formularioBiblioteca();
    }
    if(isset($_POST['addG'])){
        $_SESSION['baseDatos']->formularioGenero();
    }
    if(isset($_POST['addEd'])){
        $_SESSION['baseDatos']->formularioEditorial();
    }
    if(isset($_POST['addEs'])){
        $_SESSION['baseDatos']->formularioEscritor();
    }
    if(isset($_POST['addL'])){
        $_SESSION['baseDatos']->formularioLibro();
    }
    if(isset($_POST['addBL'])){
        $_SESSION['baseDatos']->formularioBibliotecaLibro();
    }
    if(isset($_POST['add'])){
        $_SESSION['baseDatos']->formularioAñadir();
    }
    if(isset($_POST['search'])){
        $_SESSION['baseDatos']->formularioBuscar();
    }
    if(isset($_POST['searchE'])){
        $_SESSION['baseDatos']->formularioBuscarEscritor();
    }
    if(isset($_POST['buscarE'])){
        $_SESSION['baseDatos']->buscarEscritor($_POST['idE']);
    }
    if(isset($_POST['searchB'])){
        $_SESSION['baseDatos']->formularioBuscarBiblioteca();
    }
    if(isset($_POST['buscarB'])){
        $_SESSION['baseDatos']->buscarBiblioteca($_POST['idB']);
    }
    if(isset($_POST['searchG'])){
        $_SESSION['baseDatos']->formularioBuscarGenero();
    }
    if(isset($_POST['buscarG'])){
        $_SESSION['baseDatos']->buscarGenero($_POST['idG']);
    }
    if(isset($_POST['searchEd'])){
        $_SESSION['baseDatos']->formularioBuscarEditorial();
    }
    if(isset($_POST['buscarEd'])){
        $_SESSION['baseDatos']->buscarEditorial($_POST['idEd']);
    }
    if(isset($_POST['searchL'])){
        $_SESSION['baseDatos']->formularioBuscarLibro();
    }
    if(isset($_POST['buscarL'])){
        $_SESSION['baseDatos']->buscarLibro($_POST['idL']);
    }
    if(isset($_POST['formE'])){
        $_SESSION['baseDatos']->formularioBuscarEscritor();
    }
    $form=$_SESSION['baseDatos']->getFormulario();
}

echo "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8' />
    <title>SEW</title>
    <link rel='stylesheet'type='text/css' href='Ejercicio7.css' />
    <meta name ='author' content ='Aaron' />
    <meta name ='description' content ='Ejercicio 7' />
    <meta name ='keywords' content ='Base de datos,Ejercicio 7' />
    <meta name ='viewport' content='width=device-width,initial scale=1.0'/>

</head>
<body>
    <header>
        <h1>Base de Datos</h1> 
    </header>
   <form action='#' method='post' >
        $form
    </form>
    <form action='#' method='post' >
        <input type='submit' value='Inicializar' name='inicializar'/>
        <input type='submit' value='Añadir Datos' name='add'/>
        <input type='submit' value='Buscar Datos' name='search'/>
    </form>
    
</body>
</html>
";

?>