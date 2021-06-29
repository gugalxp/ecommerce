<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {

	const SESSION = "User";

public static function login ($login, $password)
{

	$sql = new Sql();
	
	$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
		":LOGIN"=>$login 

	));

	if(count ($results) === 0)
	{

		throw new Exception("Usuário inexistente ou senha invalida");
	}

	$data = $results[0];
		
if (password_verify($password, $data["despassword"]) === true)
{

	$user = new User();

	$user->setData($data); 

	$_SESSION[User::SESSION] = $user->getValues();

	return $user;

} else {

			throw new \Exception("Usuário inexistente ou senha invalida");

}

}

//verificação de login
public static function verifyLogin($inadmin = true) { //

	if (
		!isset($_SESSION[User::SESSION]) // verifica se ela não for foi definida
		||
		!$_SESSION[User::SESSION] // verifica se ela for falsa
		||
		!(int)$_SESSION[User::SESSION]["iduser"] > 0 // verifica o id
		||
		(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
) {

	 	header("Location: /admin/login");
	 	exit;
	}

}

//desloga da conta admin

public static function logout () 
{
	$_SESSION[User::SESSION] = NULL;
}

//lista os usuarios do banco de dados
public static function listAll() {

	$sql = new Sql();

	return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b  USING (idperson) ORDER BY b.desperson");
}



public function save()  //função para salvar o novo usuario cadastrado no banco de dados através do select 
{

	$sql = new Sql();



$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array( //ACESSA o banco coluna por coluna, através das chaves descritan o próprio banco

		":desperson"=>$this->getdesperson(), //acessa o nome no banco de dados para fazer update
		":deslogin"=>$this->getdeslogin(), // acessa o login no banco de dados para fazer update
		":despassword"=>$this->getdespassword(), //acessa a senha no banco de dados para fazer update
		":desemail"=>$this->getdesemail(), // acessa o email no banco de dados para fazer update
		":nrphone"=>$this->getnrphone(), //acessa o numero de telefone no banco de dados para fazer update
		":inadmin"=>$this->getinadmin() // acessa o bando para saber se o usuario vai ser permitido ou nao a acessar a conta como admin
	));  
	
	$this->setData($results[0]);
}

public function get($iduser)
{

	$sql = new Sql();

	$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser" , array(

		":iduser"=>$iduser
		
	));

	$this->setData($results[0]); //primeiro registro
}



public function update() //metodo para fazer update  
{

		$sql = new Sql();
 		
		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array( //atualiza todas as informações das chaves inseridas aqui dentro

		":iduser"=>$this->getiduser(),
		":desperson"=>$this->getdesperson(), //acessa o nome no banco de dados para fazer update
		":deslogin"=>$this->getdeslogin(), // acessa o login no banco de dados para fazer update
		":despassword"=>$this->getdespassword(),  //acessa a senha no banco de dados para fazer update
		":desemail"=>$this->getdesemail(), // acessa o email no banco de dados para fazer update
		":nrphone"=>$this->getnrphone(),  //acessa o numero de telefone no banco de dados para fazer update
		":inadmin"=>$this->getinadmin() // acessa o bando para saber se o usuario vai ser permitido ou nao a acessar a conta como admin
	));  
	
	$this->setData($results[0]);

}

public function delete() {

$sql = new Sql();

$sql->query("CALL sp_users_delete(:iduser)", array(

	":iduser"=>$this->getiduser()
));
}


}

?>