<?php
define("DB_NAME","db_consulta");
$id = $_POST['id'];
$nota = $_POST['nota'];


$delete = "DELETE FROM tb_xml_info 
            WHERE xml_id = $id";

$delete_itens ="DELETE FROM tb_xml_itens
                WHERE xml_nr_nota = $nota";

$db->AbreConexao();
$db->delete($delete);
$db->delete($delete_itens);
$db->FechaConexao();

echo "<div id='alert-div' class='alert alert-success alert-dismissible fade show' role='alert'>
        Xml excluido com sucesso!!
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
        <span aria-hidden='true' id='alert_close'>&times;</span>
        </button>
        </div>";





