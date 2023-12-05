<head>
  <link href="production\css\style.css" rel="stylesheet">
</head>

<?php
include("../includes.php");
define('DB_NAME',"db_consulta");

$doc = file_get_contents("php://input");
$xml = simplexml_load_string($doc);
//Separo valores do XML em variaveis
$infNFe = $xml->NFe->infNFe['Id'];
$nNF = $xml->NFe->infNFe->ide->nNF;
$serie = $xml->NFe->infNFe->ide->serie;
$dhEmi = $xml->NFe->infNFe->ide->dhEmi;
$CNPJ = $xml->NFe->infNFe->emit->CNPJ;
$xNome = $xml->NFe->infNFe->emit->xNome;

//Formato a data para d/m/Y
$dhEmi=substr($dhEmi,0,10);
$dhEmi = date_create($dhEmi);
$dhEmi = date_format($dhEmi,'d/m/Y');

//verifico se o numero da Nota bate com da SD1010;
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
    //Verifico se já foi salvo em banco com o numero da nota
    $verifica = "SELECT xml_nr_nota FROM db_consulta.tb_xml_info
                  WHERE xml_nr_nota = $nNF";

    $db->AbreConexao();
    $verifica = $db->select($verifica);
    $db->FechaConexao();
    //Se ja á um registro no banco, retorno mensagem
    if (count($verifica)>0){ 
      echo "<div id='alert-div' class='alert alert-danger alert-dismissible fade show' role='alert'>
      XML já consta na lista!
      <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true' id='alert_close'>&times;</span>
      </button>
      </div>";

    }else{
      //Salvo informações no banco de dados
      $campos = array(
      'xml_nome_arquivo'=>$infNFe,
      'xml_nr_nota' => $nNF,
      'xml_serie' => $serie,
      'xml_dt_emissao' => $dhEmi,
      'xml_cnpj_cli' => $CNPJ,
      'xml_razao_cli' => $xNome,
      'xml_adicionado_dt' => date('d/m/Y'),
      );
      $db->AbreConexao();
      $res = $db->insert('tb_xml_info',$campos);
      $db->FechaConexao();

      foreach ($xml->NFe->infNFe->det as $item) {

        $nItem = $item['nItem'];
        $cProd=$item->prod->cProd;
        $xProd=$item->prod->xProd;
        $qCom=$item->prod->qCom;
        $vUnCom=$item->prod->vUnCom;
        $vProd=$item->prod->vProd;

        $campos = array(
        'xml_nr_nota' => $nNF,
        'xml_item' => $nItem,
        'xml_cod_prod' => $cProd,
        'xml_desc_prod' => $xProd,
        'xml_qtd_item' => $qCom,
        'xml_vl_un' => $vUnCom,
        'xml_vl_total_item' => $vProd,
        );
        $db->AbreConexao();
        $res = $db->insert('tb_xml_itens',$campos);
        $db->FechaConexao();
      }

      echo "<div id='alert-div' class='alert alert-success alert-dismissible fade show' role='alert'>
      Usuário cadastrado com sucesso!!
      <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true' id='alert_close'>&times;</span>
      </button>
      </div>";
    }
  }else{
    echo "<div id='alert-div' class='alert alert-danger alert-dismissible fade show' role='alert'>
    Numero da nota do XML não encontrado na SD1010!!
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true' id='alert_close'>&times;</span>
    </button>
    </div>";
  }
  


  




