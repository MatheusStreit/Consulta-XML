<?php
include("../includes.php");
$id_nota = $_POST['id'];
define('DB_NAME',"db_consulta");
$select_nota="SELECT * FROM db_consulta.tb_xml_info
                WHERE xml_id = '".$id_nota."'";
$db->AbreConexao();
$select_nota = $db->select($select_nota);
$db->FechaConexao();

if(count($select_nota)>0){
    foreach ($select_nota as $nota){
        $nNF = $nota['xml_nr_nota'];
        $serie = $nota['xml_serie'];
        $dhEmi = $nota['xml_dt_emissao'];
        $xNome = $nota ['xml_razao_cli'];
        $CNPJ = $nota['xml_cnpj_cli'];
    }
}

?>
<div class="row">
<div class="col-md-12 col-sm-12  ">
    <div class="x_panel">
    <div class="x_title">
        <h2>Consulta</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" >
        <div>

        <div class="row">
        <label class="col-md-2" for="nr_nota">Nr. NOTA DE ORIGEM</label>
        <label class="col-md-2" for="serie_nota">SERIE</label>
        <label class="col-md-2" for="dt_emi">Dt. EMISSÃO</label>

        </div>
        <div class="row">
        <input type="text" id='id_xml'style ='display:none' value='<?php echo $id_nota ;?>'>
        <input class='input-form col-md-2' type="text" id='nr_nota' value='<?php echo $nNF ;?>'>
        <input class='input-form col-md-2' type="text" id='serie_nota' value='<?php echo $serie;?>'>
        <input class='input-form col-md-2' type="text" id='dt_emi' value='<?php echo $dhEmi;?>'>
        </div>
        <br>
        <div class="row">
        <label class="col-md-3" for="nome_cli">RAZÃO SOCIAL</label>
        <label class="col-md-2" for="cnpj_cli">CNPJ DO CLIENTE</label>
        </div>
        <div class="row">
        <input class='input-form col-md-3' type="text" id="nome_cli" value='<?php echo $xNome;?>'>
        <input class='input-form col-md-2' type="text" id='cnpj_cli' value='<?php echo $CNPJ;?>'>
        </div>
        <br>

        <h2>Consulta nota origem</h2>
        <table id="tabela" class="table table-striped">
            <thead>
                <tr >
                <th>Item</th>
                <th>Codigo</th>
                <th>Descricão</th>
                <th>Qtd</th>
                <th>R$ Valor UN </th>
                <th>R$ Valor Total</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $select_nota = "SELECT D1_NFORI,D1_SERIORI,D1_ITEM,D1_COD,D1_DESCRI,D1_QUANT,D1_VUNIT,D1_TOTAL
                                FROM SD1010 (NOLOCK)
                                WHERE D_E_L_E_T_=''
                                AND D1_FILIAL='01'
                                AND D1_TIPO='D'
                                AND D1_DOC= $nNF
                                AND D1_SERIE='4'";

                $db->AbreConexao('totvs');
                $select_nota = $db->select($select_nota,'totvs');
                $db->FechaConexao('totvs');

                if (count($select_nota)>0){
                foreach($select_nota as $nota){
                    
                    $d1_item = $nota['D1_ITEM'];
                    $d1_cod = $nota['D1_COD'];
                    $d1_descri = $nota['D1_DESCRI'];
                    $d1_quant = $nota['D1_QUANT'];
                    $d1_vunit = $nota['D1_VUNIT'];
                    $d1_total = $nota['D1_TOTAL'];
                    
                    echo "<tr>";
                    echo '<td>' . $d1_item . '</td>';
                    echo '<td>' . $d1_cod . '</td>';
                    echo '<td>' . $d1_descri . '</td>';
                    echo '<td>' . $d1_quant . '</td>';
                    echo '<td>'. number_format($d1_vunit,2,',','.') . '</td>';
                    echo '<td>'. number_format($d1_total,2,',','.') . '</td>';
                    echo "</tr>";

                    $total_nota +=$d1_total;
                
                    }
                    echo "<tr>";
                    echo '<td colspan="5">' . 'TOTAL' . '</td>';
                    echo '<td>'. number_format($total_nota,2) . '</td>';
                    echo "</tr>";
                }else{
                    echo "<tr>";
                    echo '<td colspan="6" style="text-align:center" id="vazio" name="vazio" value="1">'."<h4>Nenhuma nota encontrada para o número de NF informado.</h4>" . '</td>';
                    echo "</tr>";
                  
                }
                ?>
            </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>



