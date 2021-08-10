<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model {

	const SESSION = "User";
	const SECRET = "HcodePhp7_Secret";
	const  SECRET_IV = "HcodePhp7_Secret_IV" ;
	const  ERROR = "UserError" ;
	const  ERROR_REGISTER = "UserErrorRegister" ;
	const  SUCCESS = "UserSucesss" ;

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

	$this->setData($results[0]); 
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

public static function getForgot($email, $inadmin = true)
{
     $sql = new Sql();
     $results = $sql->select("
         SELECT *
         FROM tb_persons a
         INNER JOIN tb_users b USING(idperson)
         WHERE a.desemail = :email;
     ", array(
         ":email"=>$email
     ));
     if (count($results) === 0)
     {
         throw new \Exception("Não foi possível recuperar a senha.");
     }
     else
     {
         $data = $results[0];
         $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
             ":iduser"=>$data['iduser'],
             ":desip"=>$_SERVER['REMOTE_ADDR']
         ));
         if (count($results2) === 0)
         {
             throw new \Exception("Não foi possível recuperar a senha.");
         }
         else
         {
             $dataRecovery = $results2[0];
           $code = openssl_encrypt ($dataRecovery ['idrecovery'], 'AES-128-CBC' , pack ( "a16" , User::SECRET ), 0 , pack ( "a16" , 
           	User::SECRET_IV ));

				$code = base64_encode($code);

				if ( $inadmin === true ) {

					$link = "http://www.projetoecommerceg.com.br/admin/forgot/reset?code=$code" ;

				} else {

					$link = "http://www.projetoecommerceg.com.br/forgot/reset?code=$code" ;
					
				}				
             $mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
                 "name"=>$data['desperson'],
                 "link"=>$link
             )); 
             $mailer->send();
             return $link;
         }
     }
 }

	public static function validForgotDecrypt($code) 
{
        $code = base64_decode($code);

		$idrecovery = openssl_decrypt( $code , 'AES-128-CBC' , pack ( "a16" , User::SECRET ), 0 , pack ( "a16" , User::SECRET_IV ));

        $sql = new Sql();

        $results = $sql->select(" 
         SELECT *
         FROM tb_userspasswordsrecoveries a
         INNER JOIN tb_users b USING(iduser)
         INNER JOIN tb_persons c USING(idperson)
         WHERE
         a.idrecovery = :idrecovery
         AND
         a.dtrecovery IS NULL
         AND
         DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
     ", array(
            
           ":idrecovery"=>$idrecovery
        ));

        if (count($results) === 0) 
        {
            throw new \Exception("Não foi possível recuperar a senha.");
        }
        else{
            return $results[0];
        }
       
    }

    	public static function setForgotUsed ($idrecovery)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery" , array(
			":idrecovery"=>$idrecovery
		));

	}

	public function setPassword($password)
	{

		$sql = new Sql();

		$sql->query( "UPDATE tb_users SET despassword = :password WHERE iduser = :iduser" , array(
			":password"=>$password,
			":iduser"=>$this->getiduser()
		));

	}
}

?>