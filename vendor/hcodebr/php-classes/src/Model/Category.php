<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Category extends Model {

	public static function listAll()
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
	}

	public function save()
	{

		$sql = new Sql();


$results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array( //ACESSA o banco coluna por coluna, através das chaves descritan o próprio banco

		":idcategory"=>$this->getidcategory(), //acessa o nome no banco de dados para fazer update
		":descategory"=>$this->getdescategory() // acessa o login no banco de dados para fazer update
	));  
	
	$this->setData($results[0]);

	}

	public function get($idcategory)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [

			'idcategory'=>$idcategory
		]);

			$this->setData($results[0]);
	}

	public function delete()
	{

		$sql = new Sql();

		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [

			'idcategory'=>$this->getidcategory()
	]);

	}
			
}

?>