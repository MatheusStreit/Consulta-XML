<?php
include('../includes.php');
define('DB_NAME',"db_matheus");

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	//cadastra novo usuário
	if ($_POST['acao']==='salva'){
		$login = $_POST['login'];

		//Verifico se já á um login igual
		$verifica_login ="SELECT usu_login FROM db_matheus.tb_usuarios 
							WHERE usu_login = '".$login."'";

		$db->AbreConexao();
		$res_user = $db->select($verifica_login);

		if(count($res_user)){
			echo "igual";
			die;
		}

		$nome = $_POST['nome'];
		$email = $_POST['email'];
		$tipo = implode(",",$_POST['tipo']);
		$senha = MD5($_POST['senha']);

		$campos = array(
			'usu_nome' => $nome,
			'usu_email' => $email,
			'usu_login' => $login,
			'usu_senha' => $senha,
			'usu_ativo' => 'SIM'
		);

		$res = $db->insert('tb_usuarios',$campos,'db_matheus');

		$tipo = explode(",",$tipo);
		foreach ($tipo as $id_perm){

			$campos2 = array(
				'rel_id_user' => $res,
				'rel_permissao' => $id_perm,
			);
			$inse = $db->insert('tb_relacao',$campos2,'db_matheus');
		}
		$db->FechaConexao();

		echo "<div id='alert-div' class='alert alert-success alert-dismissible fade show' role='alert'>
				Usuário cadastrado com sucesso!! (A pagina será recarregada em 3 segundos)
				<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
				<span aria-hidden='true' id='alert_close'>&times;</span>
				</button>
			</div>";
		die;

	};
	//altero usuário
	if($_POST['acao'] === 'altera'){

		$id = $_POST['id'];
		$login = $_POST['login'];
		$nome = $_POST['nome'];
		$email = $_POST['email'];
		$tipo =$_POST['tipo'];
		$senha = MD5($_POST['senha']);

		$set = array(
			'usu_login' => $login,
			'usu_nome' => $nome,
			'usu_email' => $email,
		);

		if(!empty($_POST['senha'])){
			$set['usu_senha'] = $senha;
		
		}
		$where = array(
			'usu_id' => $id
		);

		$db->AbreConexao();
		$db->update('db_matheus.tb_usuarios',$set,$where);
		$db->FechaConexao();
		
		$sql_delete = "DELETE FROM db_matheus.tb_relacao 
		WHERE rel_id_user = $id";

		$db->AbreConexao();
		$resut_del = $db->select($sql_delete);
		$db->FechaConexao();

		foreach($tipo as $id_perm){
			$permissao = array(
				'rel_id_user' => $id,
				'rel_permissao' => $id_perm,
			);
			$db->AbreConexao();
			$res = $db->insert('tb_relacao',$permissao,'db_matheus');
			$db->FechaConexao();

		}
		
		
		echo "<div id='alert-div' class='alert alert-success alert-dismissible fade show' role='alert'>
				Usuário alterado com sucesso!! (A pagina será recarregada em 3 segundos)
				<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
				<span aria-hidden='true' id='alert_close'>&times;</span>
				</button>
			</div>";
		die;

	};
	//visualizo usuário
	if($_POST['acao']==='visu'){
		$id = $_POST['id'];

		$sql_user = "SELECT usu_id,usu_login,usu_nome,usu_email FROM db_matheus.tb_usuarios 
						WHERE usu_id = $id";

		$sql_permissao = "SELECT rel_permissao FROM db_matheus.tb_relacao
							WHERE rel_id_user = $id ";

		$db->AbreConexao();
		$res_user = $db->select($sql_user);
		$res_permissao = $db->select($sql_permissao);
		$db->FechaConexao();

		if(count($res_user)){
			foreach($res_user as $result_user){

				$id = $result_user['usu_id'];
				$login = $result_user['usu_login'];
				$nome =$result_user['usu_nome'];
				$email =$result_user['usu_email'];
				$senha =$result_user['usu_senha'];

				if(count($res_user)){
					foreach($res_permissao as $result_permissao){
						if (!empty($result_permissao['rel_permissao'])){
							$tipo =($tipo.$result_permissao['rel_permissao'].',');
							
						}else{
							$tipo ='';
						}
					}
				};
				
			};

			$array = array(
				'usu_id' => $id,
				'usu_login'=> $login,
				'usu_nome'=>$nome,
				'usu_email' =>$email,
				'usu_senha'=>$senha,
				'tipo'=>$tipo,
			);
			
			header('Content-Type: application/json');
			echo json_encode($array); 
		};
	};

	die;
}elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){
	//metodo para deletar usuário
	$data = json_decode(file_get_contents('php://input'), true);
	$id = $data['id'];

	$set = array(
		'usu_ativo' => 'NÃO',
		'usu_del' =>'SIM',
	);

	$where = array(
		'usu_id' => $id
	);

	$db->AbreConexao();
	$db->update('db_matheus.tb_usuarios',$set,$where);
	$db->FechaConexao();

	echo "<div id='alert-div'class='alert alert-danger alert-dismissible fade show' role='alert'>
			Usuário excluido com sucesso!! (A pagina será recarregada em 3 segundos)
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			<span aria-hidden='true' id='alert_close'>&times;</span>
			</button>
		</div>";

	die;
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!-- Meta, title, CSS, favicons, etc. -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Gentelella Alela! | </title>

	<!-- Bootstrap -->
	<link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<!-- NProgress -->
	<link href="vendors/nprogress/nprogress.css" rel="stylesheet">

	<!-- Custom Theme Style -->
	<link href="build/css/custom.min.css" rel="stylesheet">

	<link href="production/css/select2.min.css" rel="stylesheet"/>

	<link href="build/css/style_pers.css" rel="stylesheet"/>

</head>

<body class="nav-md">
	<div class="container body">
		<div class="main_container">
			<div class="col-md-3 left_col">
				<div class="left_col scroll-view">
					<div class="navbar nav_title" style="border: 0;">
						<a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>Gentelella Alela!</span></a>
					</div>
					<div class="clearfix"></div>

					<!-- menu profile quick info -->
					<div class="profile clearfix">
						<div class="profile_pic">
							<img src="production/images/img.jpg" alt="..." class="img-circle profile_img">
						</div>
						<div class="profile_info">
							<span>Welcome,</span>
							<h2>John Doe</h2>
						</div>
					</div>
					<!-- /menu profile quick info -->
					<br />
					<!-- sidebar menu -->
					<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
						<div class="menu_section">
						  <h3>General</h3>
						  <ul class="nav side-menu">
							<li><a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
							  <ul class="nav child_menu">
								<li><a href="index.html">Plain Page</a></li>
							  </ul>
							</li>
							<li><a><i class="fa fa-user"></i> Cadastros <span class="fa fa-chevron-down"></span></a>
							  <ul class="nav child_menu">
								<li><a href="cadastrar.php">Usuário</a></li>
								<li><a href="grupo.html">Grupo</a></li>
								<li><a href="menu.php">Menus</a></li>
								<li><a href="permissoes.html">Permissões</a></li>
							  </ul>
							</li>
						  </ul>
						</div>
		  
					</div>
					<!-- /sidebar menu -->

					<!-- /menu footer buttons -->
					<div class="sidebar-footer hidden-small">
						<a data-toggle="tooltip" data-placement="top" title="Settings">
							<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
						</a>
						<a data-toggle="tooltip" data-placement="top" title="FullScreen">
							<span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
						</a>
						<a data-toggle="tooltip" data-placement="top" title="Lock">
							<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
						</a>
						<a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
							<span class="glyphicon glyphicon-off" aria-hidden="true"></span>
						</a>
					</div>
					<!-- /menu footer buttons -->
				</div>
			</div>

			<!-- top navigation -->
			<div class="top_nav">
				<div class="nav_menu">
					<div class="nav toggle">
						<a id="menu_toggle"><i class="fa fa-bars"></i></a>
					</div>
					<nav class="nav navbar-nav">
						<ul class=" navbar-right">
							<li class="nav-item dropdown open" style="padding-left: 15px;">
								<a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
									<img src="production/images/img.jpg" alt="">John Doe
								</a>
								<div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="javascript:;"> Profile</a>
									<a class="dropdown-item" href="javascript:;">
										<span class="badge bg-red pull-right">50%</span>
										<span>Settings</span>
									</a>
									<a class="dropdown-item" href="javascript:;">Help</a>
									<a class="dropdown-item" href="login.html"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
								</div>
							</li>

							<li role="presentation" class="nav-item dropdown open">
								<a href="javascript:;" class="dropdown-toggle info-number" id="navbarDropdown1" data-toggle="dropdown" aria-expanded="false">
									<i class="fa fa-envelope-o"></i>
									<span class="badge bg-green">6</span>
								</a>
								<ul class="dropdown-menu list-unstyled msg_list" role="menu" aria-labelledby="navbarDropdown1">
									<li class="nav-item">
										<a class="dropdown-item">
											<span class="image"><img src="production/images/img.jpg" alt="Profile Image" /></span>
											<span>
												<span>John Smith</span>
												<span class="time">3 mins ago</span>
											</span>
											<span class="message">
												Film festivals used to be do-or-die moments for movie makers. They were where...
											</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="dropdown-item">
											<span class="image"><img src="production/images/img.jpg" alt="Profile Image" /></span>
											<span>
												<span>John Smith</span>
												<span class="time">3 mins ago</span>
											</span>
											<span class="message">
												Film festivals used to be do-or-die moments for movie makers. They were where...
											</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="dropdown-item">
											<span class="image"><img src="production/images/img.jpg" alt="Profile Image" /></span>
											<span>
												<span>John Smith</span>
												<span class="time">3 mins ago</span>
											</span>
											<span class="message">
												Film festivals used to be do-or-die moments for movie makers. They were where...
											</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="dropdown-item">
											<span class="image"><img src="production/images/img.jpg" alt="Profile Image" /></span>
											<span>
												<span>John Smith</span>
												<span class="time">3 mins ago</span>
											</span>
											<span class="message">
												Film festivals used to be do-or-die moments for movie makers. They were where...
											</span>
										</a>
									</li>
									<li class="nav-item">
										<div class="text-center">
											<a class="dropdown-item">
												<strong>See All Alerts</strong>
												<i class="fa fa-angle-right"></i>
											</a>
										</div>
									</li>
								</ul>
							</li>
						</ul>
					</nav>
				</div>
			</div>
			<!-- /top navigation -->

			<!-- page content -->
			<div class="right_col" role="main">
          <div class="">
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
				<div class="x_title">
					<h2>Permissões Usuários</h2>
					<div class="row">
						<div class="col-md-3 offset-md-11" style="font-size: 20px;">
							<a class="fa fa-search search-icon" id="open_modal" name="open_modal" style="background-color:#93bf85 ;border-radius:20px;color:black;padding:10px "></a>
						</div>
					</div>
			
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
					<div>
						<span id='alert_result'>

						</span>
					</div>
                      <!-- start form for validation -->
					<div id='form'>
						<form id="demo-form" data-parsley-validate>

							<label for="id" style="display:none"></label>
							<input type="number" id="id" class="form-control" name="id" data-parsley-trigger="change" style="display:none"/>

							<label for="login">Login * :</label>
							<input type="text" id="login" class="form-control" name="login" data-parsley-trigger="change" required autocomplete="off" />

							<label for="nome">Nome Completo * :</label>
							<input type="text" id="nome" class="form-control" name="nome" required />

							<label for="email">Email * :</label>
							<input type="mail" id="email" class="form-control" name="email" data-parsley-trigger="change" required />

							<label for="senha">Senha * :</label>
							<input type="password" id="senha" class="form-control" name="senha" data-parsley-trigger="change" required />
							

							<label for="select2">Tipo de permissão * :</label>
							<br>
							<select class="select2 form-control" id='select2' name="states[]" multiple="multiple">
							<?php
								$num_registros = null;
								$user ="SELECT grp_id,grp_descricao, grp_ativo, grp_nome FROM tb_grupos
											WHERE grp_ativo <>'Não'
											ORDER by grp_descricao";
								$db->AbreConexao();
								$res = $db->select($user);
								$db->FechaConexao();
								if (count($res)) {
									foreach($res as $result){
										echo "<option value=".$result['grp_id'].">".$result['grp_descricao']."</option>";

									}
								};

							?>
							</select>
							<br>
							<br>							
							<span class="btn btn-success salvar alinha" id="salva"><i class='fa fa-floppy-o'></i> Salvar</span>
							<span class="btn btn-success altera alinha" id="altera" style='display:none'><i class='fa fa-floppy-o'></i> Salvar</span>
							<span class="btn btn-warning limpar alinha" id="limpar"><i class='fa fa-eraser'></i> Limpar</span>
							<span class="btn btn-danger excluir alinha" id="excluir" style='display:none'><i class='fa fa-trash'></i> Excluir</span>
						</form>
						<!-- end form for validations -->
					</div>
				</div>
			</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->
			<!-- footer content -->
			<footer>
				<div class="pull-right">
					Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
				</div>
				<div class="clearfix"></div>
			</footer>
			<!-- /footer content -->
		</div>
	</div>

</body></html>
<!--  INICIO Modal de usuários -->
<div class="modal" tabindex="-1" role="dialog" id="pesquisa">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Usuários</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <table id="usuarios" class="table hover stripe">
			<thead>
				<tr >
				<th>ID</th>
				<th>Login</th>
				<th>Nome</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$num_registros = null;
					$user ="SELECT usu_id,usu_login, usu_nome,usu_email FROM tb_usuarios 
								WHERE usu_ativo <>'NÃO'
								ORDER by usu_id";
					$db->AbreConexao();
					$res = $db->select($user);
					$db->FechaConexao();
					if (count($res)) {
						foreach($res as $result){
							$id_modal = $result['usu_id'];
							//retorno valores no data table
							echo "
							<tr class='tr_list'>
								<td class='clicker' data='$id_modal'>".$result["usu_id"]."</td>
								<td class='clicker' data=".$result["usu_id"].">".$result["usu_login"]."</td>
								<td class='clicker' data=".$result["usu_id"].">".$result["usu_nome"]."</td>
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
<!--  FIM Modal de usuários -->
<!--  INICIO Modal de exclusão -->
<div class="modal" tabindex="-1" role="dialog" id="exc">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmação</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

	  	<h4>Deseja realmente excluir o usuário?</h4>
	  
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" id='exc_confirm' class="btn btn-success">Sim</button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="vendors/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!-- FastClick -->
<script src="vendors/fastclick/lib/fastclick.js"></script>
<!-- NProgress -->
<script src="vendors/nprogress/nprogress.js"></script>

<!-- Custom Theme Scripts -->
<script src="build/js/custom.min.js"></script>

<script src="production/js/select2.min.js"></script>
<script type="text/javascript" src="production/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {

	//////////////////////
	// INCIO DE FUNÇÕES //
	//////////////////////
	//função para validar email
	function validaemail(){
		var sEmail	= $("#email").val();
		// filtros
		var emailFilter=/^.+@.+\..{2,}$/;
		var illegalChars= /[\(\)\<\>\,\;\:\\\/\"\[\]]/
		// condição
		if(!(emailFilter.test(sEmail))||sEmail.match(illegalChars)){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo com um email valido!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#email").focus();
		}
	}

	//função para recarregar pagina
	function recarregapag(){
		setTimeout(function() {
			location.reload(true);
		}, 3000);
	};
	// desativa o clicl de botões
	function desativaclick(){
		$("#excluir").off("click");
		$("#limpar").off("click");
		$("#salva").off("click");
		$("#altera").off("click");
		
	};

	//função limpar campos
	function limparCampos() {
    	document.getElementById("demo-form").reset();
  	};

	////////////////////
	// FIM DE FUNÇÕES //
	////////////////////

	//abre o select2
	$('.select2').select2({
		required:'true'
	});
	
	//Controle de modal
	$('#open_modal').click(function() {
		$('#pesquisa').modal('show'); // Mostrar o modal
		$('#usuarios').DataTable({
			language: {
				url: 'build/js/pt-BR.json',
			},
			searching: true,
			paging: true,
			destroy:true,
		});
	});

	//verifico caracteres do campo nome e login
	$('#nome').on('keypress', function(e)  {
    var key = String.fromCharCode(e.which);
    var regex = /^[a-zA-Z-' '-]+$/; 
    
		if (!regex.test(key)) {
		e.preventDefault();
		}
  	});
	  $('#login').on('keypress', function(e)  {
    var key = String.fromCharCode(e.which);
    var regex = /^[a-zA-Z-0-9-_]+$/; 
    
		if (!regex.test(key)) {
		e.preventDefault();
		}
  	});
	

	//defino função de botão para limpar campos
	$("#limpar").click(function(){
		limparCampos();
		$('#alert-div').hide();
		$('#altera').css('display', 'none');
		$('#salva').css('display', 'inline');
		$('#limpar').css('display', 'inline');
		$('#excluir').css('display', 'none');
		var select = $("#select2").select2();   
		$select.val(null).trigger("change");

	});

	//////////////////////////////
	// INICIO DE ENVIO DE DADOS //
	//////////////////////////////

	//envia o comando para visualizar informações de usuário
	
	$(document).on('click', 'td', function(e) {
		e.preventDefault;
		var id = $(this).closest('tr').find('td[data]').html();
		id = parseInt(id);
		$.ajax({
		url: "cadastrar.php", 
		method: "POST",
		data: {
			id:id,
			acao:'visu'
		}, 
		success: function(result) {
			$('#pesquisa').modal('hide');
			// traz os valores do arrya nos campos do formulario
			$('#id').val(result.usu_id);
			$('#login').val(result.usu_login);
			$('#nome').val(result.usu_nome);
			$('#email').val(result.usu_email);
			let tipo = result.tipo;
			console.log(result);
			console.log(tipo);
			if (tipo){
				$('#select2').val(tipo.split(',')).select2();
			}
			$('#senha').html('placeholder','**********');
			//altero os parametros dos botões do menu
			$('#salva').css('display', 'none');
			$('#limpar').css('display', 'inline');
			$('#excluir').css('display', 'inline');
			$('#altera').css('display', 'inline');
			
		},
		error: function(xhr, status, error) {
			
			console.log('Erro no envio dos dados:', error);
		}
		});	
	});
	
			
	//envia o comando para cadastrar novo usuário
	$("#salva").click(function(){
		var login = $("#login").val();
		var nome = $("#nome").val();
		var email = $("#email").val();
		var senha = $("#senha").val();
		var tipo = $("#select2").val();

		if (login === '' && nome === '' && email === '' && senha === ''){

			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher todos os campos para cadastro!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);

		}if(login === ''){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo de login!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#login").focus();

		}if(nome === ''){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo de nome!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#nome").focus();

		}if(email === ''){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo de email!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#email").focus();

		}if(senha === ''){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo de senha!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#senha").focus();

		}if($("#select2").val() == "" || $("#select2").val() == null){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo de permissões!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#select2") . select2 ( "open" );  
			

		}if(login != '' && nome != '' && email != '' && senha != '' && tipo != ''){
			var sEmail	= $("#email").val();
			// filtros
			var emailFilter=/^.+@.+\..{2,}$/;
			var illegalChars= /[\(\)\<\>\,\;\:\\\/\"\[\]]/
			// condição
			if(!(emailFilter.test(sEmail))||sEmail.match(illegalChars)){
				var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo com um email valido!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
				$('#alert_result').html(htmlresponse);
				$("#email").focus();
			}else{
				//envio os dados
				$.ajax({
					url: 'cadastrar.php', 
					method: 'POST',
					data: {
						login:login,
						nome:nome,
						email:email,
						senha:senha,
						tipo:tipo,
						acao:'salva'
					}, 
					success: function(response) {
						if(response =='igual'){
							var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Login já esta sendo utilizado!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
							$('#alert_result').html(htmlresponse);
							$("#login").focus();
						}else{
						limparCampos();
						$('#alert_result').html(response);
						desativaclick();
						recarregapag();
						};
					},
					error: function(xhr, status, error) {
						console.log('Erro no envio dos dados:', error);
					}
				});
			};	
		};
	});

	//envia o comando para alterar usuário
	$("#altera").click(function(){
		var id = $("#id").val();
		var login = $("#login").val();
		var nome = $("#nome").val();
		var email = $("#email").val();
		var senha = $("#senha").val();
		var tipo = $("#select2").val();

		if (login === '' && nome === '' && email === '' && senha === ''){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher todos os campos para cadastro!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);

		}if(login === ''){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo de login!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#login").focus();

		}if(nome === ''){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo de nome!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#nome").focus();

		}if(email === ''){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo de email!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#email").focus();

		}if(senha === ''){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo de senha!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#senha").focus();

		}if(login != '' && nome != '' && email != '' && senha != '' && tipo != ''){
			var sEmail	= $("#email").val();
			// filtros
			var emailFilter=/^.+@.+\..{2,}$/;
			var illegalChars= /[\(\)\<\>\,\;\:\\\/\"\[\]]/
			// condição
		}if(!(emailFilter.test(sEmail))||sEmail.match(illegalChars)){
			var htmlresponse = "<div id='alert-div' class='alert alert-warning alert-dismissible fade show' role='alert'>Favor preencher o campo com um email valido!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true' id='alert_close'>&times;</span></button></div>";
			$('#alert_result').html(htmlresponse);
			$("#email").focus();
		}else{

			$.ajax({
				url: 'cadastrar.php', 
				method: 'POST',
				data: {
					id:id,
					login:login,
					nome:nome,
					email:email,
					senha:senha,
					tipo:tipo,
					acao:'altera'
				}, 
				success: function(response) {
					limparCampos();
					console.log(response);
					$('#alert_result').html(response);
					desativaclick();
					recarregapag();
					
				},
				error: function(xhr, status, error) {
					console.log('Erro no envio dos dados:', error);
				}
			});	
		};
	});


	//envia o comando para cadastrar novo usuário
	$("#excluir").click(function(){
		var id = $("#id").val();
		$('#exc').modal('show'); // Mostrar o modal
		$('#exc_confirm').click(function(){
			$.ajax({
			url: 'cadastrar.php', 
			method: 'DELETE',
			data: JSON.stringify({ id: id }),
			success: function(response) {
				$('#pesquisa').modal('hide');
				$('#exc').modal('hide');
				//altero display dos botões
				$('#limpar').css('display', 'inline');
				$('#excluir').css('display', 'inline');
				$('#salva').css('display', 'inline');
				$('#altera').css('display', 'none');
				limparCampos();
				desativaclick();
				$('#alert_result').html(response);
				recarregapag();
			},
			error: function(xhr, status, error) {
				console.log('Erro no envio dos dados:', error);
			}
			});	
		});
	});
});
</script>
