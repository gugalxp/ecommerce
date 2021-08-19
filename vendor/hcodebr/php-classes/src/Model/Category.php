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

	Category::updateFile();

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

			Category::updateFile();

	}

	public static function updateFile()
	{
		$categories = Category::listAll();

		$html = [];

		foreach ($categories as $row) {
		array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));

	}
			
}

?>