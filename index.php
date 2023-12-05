<?php
//Desenvolvido por MS 10/07/2023
session_start();
    if(isset($_SESSION['nome']) && ($_SESSION['cod_repre'])){
      
        $nome = $_SESSION['nome'];

    }else{
        header("location:login.php");
        exit;
    }
  include('scripts.php');
?>

<body class="nav-md">
	<div class="container body">
		<div class="main_container">
			<?php
        include('includes/left.php');
        include('includes/header.php');
      ?>
      <!-- page content -->
      <div class="right_col" role="main">
        <div class="">
          <div class="page-title">
            <div class="title_left">
              
            </div>
          </div>

          <div class="clearfix"></div>

          <div id="loader">

          </div>

        </div>
      </div>
      <!-- /page content -->

      <!-- footer content -->
      <?php
          include('includes/footer.php');
          include('includes/fim.php');
        ?>
      <!-- /footer content -->
    </div>
  </div>
</body>

<script>
    $(document).ready(function(){
      $("#loader").load("consulta.php");
      $("#consulta").click(function(){
        $("#loader").load("consulta.php");
      })
      $("#importa").click(function(){
        $("#loader").load("importar_xml.php");
      })
    });
</script>