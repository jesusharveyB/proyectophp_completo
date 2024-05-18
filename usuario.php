<?php
include_once ("./conexion.php");

class Usuario {
    private $user;
    private $pass;
    private $conn;

    private $correo;

function __construct($_user, $_pass, $_correo, $_conn)
{
    $this->user = $_user;
    $this->pass = $_pass;
    $this->correo = $_correo;
    $this->conn = $_conn;
}


    function iniciar_sesion()
{
    $sql = "SELECT * FROM usuarios WHERE nombre = '".$this->user."' AND contraseña = '".$this->pass."' AND correo = '".$this->correo."'";
    $res = $this->conn->query($sql);
    return $res->num_rows > 0;  
}

}

