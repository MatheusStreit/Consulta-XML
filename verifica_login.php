<?php
define('DB_NAME',"db_portal");
include("../includes.php");

    if($_SERVER['REQUEST_METHOD'] === "POST"){
        $cod_repre = trim($_POST['cod']);
        $senhaMD = MD5($_POST['senha']);

        $sql_user =("SELECT cod_repre,senha,nome from db_portal.tb_usuario
                        WHERE cod_repre = '".$cod_repre."'
                        AND senha = '".$senhaMD."'
                        AND bloqueado <> 'Sim'
                        LIMIT 1");

        $db->AbreConexao('hydra');
        $result = $db->select($sql_user);
        $db->FechaConexao('hydra');

        if(count($result)>0){
            foreach($result as $user){
                session_start();
                $_SESSION['cod_repre']= $user['cod_repre'];
                $_SESSION['nome']=$user['nome'];
                header("Location:index.php");
                exit;
            }
        }else{
            header("Location:login.php");
            exit;
        }
    }
?>