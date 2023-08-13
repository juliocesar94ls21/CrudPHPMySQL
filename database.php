<?php

class database{
	private $HOST = "localhost";
	private $USER = "root";
	private $PASS = "";
	private $BANCO = "rspainel";
	private $operacao;
	public $tabela;
	public $erro = null;
	public $indicesTabelas = array();
	public $indicesColunas = array();
	private $valoresPost = array();
	private $valoresInsercao = array();
	public $recebeRegistros = array();
	private $campoChave;
	private $valorChave;
	private $column;
	private $clause = "";
	public $idInsert;

	function __construct($argumentos){
		$this->obterTabelas();
		$this->operacao = $argumentos["configuracoes"]["operacao"];
		$this->tabela = $this->indicesTabelas[$argumentos["configuracoes"]["tabela"]]["Tables_in_rspainel"];
		$this->valoresPost = array_key_exists("valores",$argumentos) ? $argumentos["valores"] : null;
		$this->obtemColunasDb();
		if(array_key_exists("condicao",$argumentos)){
			$this->campoChave = $argumentos["condicao"]["campo"];
			$this->valorChave = $argumentos["condicao"]["valor"];
		}
		if(array_key_exists("clause",$argumentos)){
			$this->clause = $argumentos["clause"];
		}
		if(array_key_exists("column",$argumentos)){
			$this->column = $argumentos["column"] == "all" ? "*" : $argumentos["column"];
		}
		switch($this->operacao){
			case 1:
				$this->insert();
			break;
			case 2:
				$this->update();
			break;
			case 3:
				$this->delete();
			break;
			case 4:
				$this->select();
			break;
			case 5:
				$this->deleteDefinitivo();
			break;
			case 6:
				$this->update();
			break;
		}
	}

	public function insert(){
		$chaves = array_keys($this->valoresPost);
		foreach($chaves as $chave){
		$this->valoresInsercao[] = $this->valoresPost[$chave];}
		$this->valoresInsercao["display"] = 0;
		$valoresString = implode("','",$this->valoresInsercao);
		$valoresInsercao = "'".$valoresString."'";
		$query = "insert into $this->tabela values(0,$valoresInsercao)";
		try{
			$db = $this->connect();
			$db->query($query);
			$this->idInsert = $db->lastInsertId();
		}
		catch(PDOException $e) {
			$this->erro = $e->getMessage();
		}
	}
	public function update(){
		$chaves = array_keys($this->valoresPost);
		foreach($chaves as $chave){
		$this->valoresInsercao[] = $this->valoresPost[$chave];}
		$obtemCols = "SHOW COLUMNS FROM ".$this->tabela;
		try{
			$db = $this->connect();
			$obtemColunas = $db->query($obtemCols);
			$colunas = array();
			foreach($obtemColunas as $col){
				$colunas[] = $col["Field"];
			}
			$query  = "UPDATE $this->tabela SET ";
			$campos = "";
			foreach($chaves as $chave){
				if(!in_array($chave,$colunas)){
					continue;
				}
				$valor = $this->valoresPost[$chave] == "" ? ' ' : $this->valoresPost[$chave];
				$campos.= $chave." = "."'".$valor."',";
			}
			$size = strlen($campos) - 1;
			$campos = substr($campos,0,$size);
			$query.= $campos;
			$query.= " WHERE $this->campoChave = $this->valorChave";
			$db->query($query);
		}
		catch(PDOException $e) {
			$this->erro = $e->getMessage();
		}
	}
	public function delete(){
		$campoDeleta["display"] = 1;
		$db = $this->connect();
		if($db->AutoExecute($this->tabela, $campoDeleta, 'UPDATE', "$this->campoChave = $this->valorChave") === false){
			$this->erro = $db->ErrorMsg();
		}
	}
	public function deleteDefinitivo(){
		$db = $this->connect();
		$query = "delete from $this->tabela where $this->campoChave = $this->valorChave";
		try{
			$db->query($query);
		}
		catch(PDOException $e) {
			$this->erro = $e->getMessage();
		}
	}
	public function select(){
		$coluna = $this->column;
		$query = "SELECT $coluna FROM $this->tabela WHERE display = 0";
		if($this->clause != ""){
			$query.= " AND $this->clause";
		}
		try {
			$db = $this->connect();
			$consulta = $db->query($query);

			foreach($consulta as $registros){
				$this->recebeRegistros[] = $registros;
			}
		}catch(PDOException $e) {
			$this->erro = $e->getMessage();
		}
	}
	private function connect(){
		$conn = new PDO('mysql:host='.$this->HOST.';dbname='.$this->BANCO, $this->USER, $this->PASS);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->exec("SET CHARACTER SET utf8");

		return $conn;
	}
	private function obterTabelas(){
		$conexao = $this->connect();
		$indicesTabelas = array();
		$queryTabelas = "SHOW TABLES FROM ".$this->BANCO;
		$rs = $conexao->query($queryTabelas);
		foreach($rs as $obtemRegistros){
			array_push($indicesTabelas, $obtemRegistros);
		}
		$this->indicesTabelas = $indicesTabelas;
	}
	public function obtemColunasDb(){
		$indicesColunas = array();
		$obtemColunas = "SHOW COLUMNS FROM ".$this->tabela;
		$db = $this->connect();
		$rs = $db->query($obtemColunas);
		foreach($rs as $obtemRegistros){
			array_push($indicesColunas, $obtemRegistros["Field"]);
		}
		$this->indicesColunas = $indicesColunas;
	}
}
?>
