<?php
include("../includes.php");
?>
<!-- page content -->
<div class="row">
  <div class="col-md-12 col-sm-12  ">
    <div class="x_panel">
      <div class="x_title">
        <h2>Consulta XML</h2>
        <span id='retorno_alert'>

        </span>
        <div class="clearfix"></div>
      </div>
        <div class="x_content">
        <table id="xmls" class="table table-striped">
                <thead>
                  <tr >
                    <th style='display:none'>id</th>
                    <th>Nome do arquivo</th>
                    <th>Numero da nota</th>
                    <th>Adicionado</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  define('DB_NAME',"db_consulta");
                  $lista_xmls="SELECT * FROM tb_xml_info
                                ORDER BY xml_id ";
                  $db->AbreConexao();
                  $lista_xmls = $db->select($lista_xmls);
                  $db->FechaConexao();
                  if(count($lista_xmls)>0){
                    foreach($lista_xmls as $xml){
                      $xml_id = $xml["xml_id"];
                      $nome_xml = $xml["xml_nome_arquivo"];
                      $nr_nota = $xml["xml_nr_nota"];
                      $xml_data = $xml["xml_adicionado_dt"];

                      echo "<tr class='tr_list' data='$xml_id'>
                      <td  style ='display:none'class='clicker' data='$xml_id'>".$xml_id."</td>
                      <td class='clicker' data='$xml_id'>".$xml['xml_nome_arquivo']."</td>
                      <td class='clicker' data=".$xml_id.">".$xml['xml_nr_nota']."</td>
                      <td class='clicker' data=".$xml_id.">".$xml['xml_adicionado_dt']."</td>
                      </tr>";
                    }
                  }

                  ?>
                </tbody>
              </table>
        </div>
    </div>
  </div>
</div>
<!-- /page content -->

<!-- Modal -->
<div class="modal fade" id="result_modal" tabindex="-1" aria-labelledby="result_modal" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="result_modal">Consulta</h5>
        <button type="button" class="close"  data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        
      </div>
      <div class="modal-body">
        <div id='tela'>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id='excluir' style="display:none">Excluir</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exc" tabindex="-1" aria-labelledby="exc" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title">Confirmação</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

	  	<h4>Deseja realmente excluir o XML?</h4>
	  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" id='exc_confirm' class="btn btn-success">Sim</button>
      </div>
    </div>
  </div>
</div>

<script src="vendors/jquery/dist/jquery.min.js"></script>
<script src="production/js/jquery.dataTables.min.js"></script>
<script>
  $(document).ready(function(){

    $('#xmls').DataTable({
			language: {
				url: 'build/js/pt-BR.json',
			},
			searching: true,
			paging: true,
      order: [0, 'desc'],
			destroy:true,
		});

    $('tr').on('click', 'td', function(e) {
      e.preventDefault;
      var id = $(this).closest('tr').find('td[data]').html();
      id = parseInt(id);
      $.ajax({
        url: "resultado_xml.php", 
        method: "POST",
        data: {
          id:id,
        }, 
        success: function(data) {
          $.post('resultado_xml.php', function(){
            var modal = new bootstrap.Modal(document.getElementById('result_modal'));
            modal.show();
            $("#tela").html(data);
            var prop = $("#vazio").text();
            if(prop ==='Nenhuma nota encontrada para o número de NF informado.'){
              $("#excluir").css('display','block');
            }else{
              $("#excluir").css('display','none');
            }

          });
        },
        error: function(xhr, status, error) {
          console.log('Erro no envio dos dados:', error);
        }
      });	
    });
    //envio informçõesp ara exclusão do registro do xml e itens
    $("#excluir").click(function(){
      var exec_modal = new bootstrap.Modal(document.getElementById('exc'));
      exec_modal.show(); // Mostrar o modal
        $('#exc_confirm').click(function(){
        var id = $("#id_xml").val();
        var nota = $("#nr_nota").val();
        $.ajax({
          url: "remover.php", 
          method: "POST", 
          data: id,nota,
          success: function(data) {
            document.getElementById(".close").click();
            $("#retorno_alert").html(data);
          }
          })
        })
    });
    
  });
</script>

