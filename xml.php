<head>
  <link href="production\css\style.css" rel="stylesheet">
</head>

<?php
include("../includes.php");
define('DB_NAME',"netuno");

$doc = file_get_contents("php://input");
$xml = simplexml_load_string($doc);

$nNF = $xml->NFe->infNFe->ide->nNF;
$serie = $xml->NFe->infNFe->ide->serie;
$dhEmi = $xml->NFe->infNFe->ide->dhEmi;
$CNPJ = $xml->NFe->infNFe->emit->CNPJ;
$xNome = $xml->NFe->infNFe->emit->xNome;


//Formato a data para d/m/Y
$dhEmi=substr($dhEmi,0,10);
$dhEmi = date_create($dhEmi);
$dhEmi = date_format($dhEmi,'d/m/Y');

//Verifico se já foi salvo em banco com o numero da nota

$verifica = "SELECT xml_nr_nota FROM db_consulta.tb_xml_info
            WHERE xml_nr_nota = $nNF";

$db->AbreConexao('netuno');
$verifica = $db->select($verifica);
$db->FechaConexao('netuno');

if (count($verifica)>0){ ?>

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
            <label class="col-md-1" for="nr_nota">Nr. NOTA</label>
            <label class="col-md-2" for="serie_nota">SERIE</label>
            <label class="col-md-2" for="dt_emi">Dt. EMISSÃO</label>

          </div>
          <div class="row">
            <input class='input-form col-md-1' type="text" id='nr_nota' value='<?php echo $nNF ;?>'>
            <input class='input-form col-md-2' type="text" id='serie_nota' value='<?php echo $serie;?>'>
            <input class='input-form col-md-2' type="text" id='dt_emi' value='<?php echo $dhEmi;?>'>
          </div>
          <br>
          <div class="row">
            <label class="col-md-2" for="nome_cli">RAZÃO SOCIAL</label>
            <label class="col-md-2" for="cnpj_cli">CNPJ DO CLIENTE</label>
          </div>
          <div class="row">
            <input class='input-form col-md-2' type="text" id="nome_cli" value='<?php echo $xNome;?>'>
            <input class='input-form col-md-2' type="text" id='cnpj_cli' value='<?php echo $CNPJ;?>'>
          </div>
          <br>
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
                      $D1_NFORI = $nota['D1_NFORI'];
                      $D1_SERIORI = $nota['D1_SERIORI'];
                      $D1_ITEM = $nota['D1_ITEM'];
                      $D1_COD = $nota['D1_COD'];
                      $D1_DESCRI = $nota['D1_DESCRI'];
                      $D1_QUANT = $nota['D1_QUANT'];
                      $D1_VUNIT = $nota['D1_VUNIT'];
                      $D1_TOTAL = $nota['D1_TOTAL'];

                      echo "<tr>";
                      echo '<td>' . $D1_ITEM . '</td>';
                      echo '<td>' . $D1_COD . '</td>';
                      echo '<td>' . $D1_DESCRI . '</td>';
                      echo '<td>' . $D1_QUANT . '</td>';
                      echo '<td>'. number_format($D1_VUNIT,2) . '</td>';
                      echo '<td>'. number_format($D1_TOTAL,2) . '</td>';
                      echo "</tr>";
                  
                    }
                  }
                  ?>
                </tbody>
              </table>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

<?php
}else{

//Salvo informações no banco de dados
// $campos = array(
//     'xml_nr_nota' => $nNF,
//     'xml_serie' => $serie,
//     'xml_dt_emissao' => $dhEmi,
//     'xml_cnpj_cli' => $CNPJ,
//     'xml_razao_cli' => $xNome,
// );
// $db->AbreConexao();
// $res = $db->insert('tb_xml_info',$campos,'db_consulta');
// $db->FechaConexao();

// foreach ($xml->NFe->infNFe->det as $item) {

//     $nItem = $item['nItem'];
//     $cProd=$item->prod->cProd;
//     $xProd=$item->prod->xProd;
//     $qCom=$item->prod->qCom;
//     $vUnCom=$item->prod->vUnCom;
//     $vProd=$item->prod->vProd;

// 		$campos = array(
// 			'xml_nr_nota' => $nNF,
// 			'xml_item' => $nItem,
// 			'xml_cod_prod' => $cProd,
// 			'xml_desc_prod' => $xProd,
// 			'xml_qtd_item' => $qCom,
//             'xml_vl_un' => $vUnCom,
//             'xml_vl_total_item' => $vProd,
// 		);
//         $db->AbreConexao();
// 		$res = $db->insert('tb_xml_itens',$campos,'db_consulta');
// 		$db->FechaConexao();
// }

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
          <label class="col-md-1" for="nr_nota">Nr. NOTA</label>
          <label class="col-md-2" for="serie_nota">SERIE</label>
          <label class="col-md-2" for="dt_emi">Dt. EMISSÃO</label>

        </div>
        <div class="row">
          <input class='input-form col-md-1' type="text" id='nr_nota' value='<?php echo $nNF ;?>'>
          <input class='input-form col-md-2' type="text" id='serie_nota' value='<?php echo $serie;?>'>
          <input class='input-form col-md-2' type="text" id='dt_emi' value='<?php echo $dhEmi;?>'>
        </div>
        <br>
        <div class="row">
          <label class="col-md-2" for="nome_cli">RAZÃO SOCIAL</label>
          <label class="col-md-2" for="cnpj_cli">CNPJ DO CLIENTE</label>
        </div>
        <div class="row">
          <input class='input-form col-md-2' type="text" id="nome_cli" value='<?php echo $xNome;?>'>
          <input class='input-form col-md-2' type="text" id='cnpj_cli' value='<?php echo $CNPJ;?>'>
        </div>
        <br>
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
                    $D1_NFORI = $nota['D1_NFORI'];
                    $D1_SERIORI = $nota['D1_SERIORI'];
                    $D1_ITEM = $nota['D1_ITEM'];
                    $D1_COD = $nota['D1_COD'];
                    $D1_DESCRI = $nota['D1_DESCRI'];
                    $D1_QUANT = $nota['D1_QUANT'];
                    $D1_VUNIT = $nota['D1_VUNIT'];
                    $D1_TOTAL = $nota['D1_TOTAL'];

                    echo "<tr>";
                    echo '<td>' . $D1_ITEM . '</td>';
                    echo '<td>' . $D1_COD . '</td>';
                    echo '<td>' . $D1_DESCRI . '</td>';
                    echo '<td>' . $D1_QUANT . '</td>';
                    echo '<td>'. number_format($D1_VUNIT,2) . '</td>';
                    echo '<td>'. number_format($D1_TOTAL,2) . '</td>';
                    echo "</tr>";
                
                  }
                }
                ?>
              </tbody>
            </table>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>

<?php
}
  




