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
public static function logout () 
{
	$_SESSION[User::SESSION] = NULL;
}
}

?>