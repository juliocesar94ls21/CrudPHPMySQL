<?php
require_once("database.php");

//parametro selecionar dodos.
$parametros = array(
	"configuracoes" => array("tabela" => 0, "operacao" => 4), // tabela é o indice da tabela por ordem alfabetica, operação é 1 para cadastro, 2 para atualização, 3 para remoção (tabela deve possuir campo adicional DISPLAY), 4 para seleção;
	"column" => "all", //coluna no caso de seleção.
	"clause" => "idclientes = 6" //condição, pode ser qualquer clause WHERE válida.
);

//parametros para inserir dados.
$parametros = array(
	"configuracoes" => array("tabela" => 0, "operacao" => 1), //tabela é o indice da tabela por ordem alfabetica, operação é 1 para cadastro, 2 para atualização, 3 para remoção (tabela deve possuir campo adicional DISPLAY), 4 para seleção;
	"valores" => $_POST //array associativo de dados a serem inseridos ou simplesmente o superglobal $_POST.
);

//parametros para atualizar dados.
$parametros = array(
	"configuracoes" => array("tabela" => 0, "operacao" => 2),  //tabela é o indice da tabela por ordem alfabetica, operação é 1 para cadastro, 2 para atualização, 3 para remoção (tabela deve possuir campo adicional DISPLAY), 4 para seleção;
	"valores" => $_POST,									   //array associativo de dados a serem inseridos ou simplesmente o superglobal $_POST.
	"condicao" => array("campo" => "idclientes", "valor" => 9) //condição, coluna e valor da linha a ser atualizada.
);

//parametros para remover dados.
$parametros = array(
	"configuracoes" => array("tabela" => 0, "operacao" => 3),  //tabela é o indice da tabela por ordem alfabetica, operação é 1 para cadastro, 2 para atualização, 3 para remoção (tabela deve possuir campo adicional DISPLAY), 4 para seleção;
	"condicao" => array("campo" => "idclientes", "valor" => 9) //condição, coluna e valor da linha a ser removida.
);

$db = new database($parametros);

//propriedade com o array de dados retornados no caso de consulta.
print_r($db->recebeRegistros);

//status propriedade erro
if($db->erro == null){
	print "operacao com sucesso";
}else{
	print "falha na operação ".$db->erro;
}

?>