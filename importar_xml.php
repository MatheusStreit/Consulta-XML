<div class="row">
  <div class="col-md-12 col-sm-12  ">
    <div class="x_panel">
      <div class="x_title">
        <h2>Importar XML</h2>
        <div class="clearfix"></div>
      </div>
        <div class="x_content">
          <h2>Selecione o XML</h2>
          <form method="POST" id='form' name='form' enctype="multipart/form-data">
            <div class="form-group">
              <div class="col-md-4">
                <input id="arquivo" name="arquivo" class="input-file" type="file">  
              </div>
        
            </div>
          </form>
        </div>
      <div  id ='table' style='display:none'>
        <div class="x_content">
          <div id='result_page'>
            
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<script src="vendors/jquery/dist/jquery.min.js"></script>
<script>
  $(document).ready(function(){

    const input= document.querySelector("#arquivo");
        input.addEventListener('change',function(){
          
          const arquivo = this.files[0];
          const leitor = new FileReader();

          leitor.addEventListener('load',function(){
            let xml = leitor.result;
            $.ajax({
            url: "envia.php", 
            method: "POST", 
            data: xml,
            success: function(data) {
              $("#result_page").html(data);
              $('#table').css('display','inline');
            }
            })
          })

          if(arquivo){
            leitor.readAsText(arquivo);
          }
        })
  });
</script>