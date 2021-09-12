<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/admin/products", function(){ //lista os produtos

	User::verifyLogin();

	$products = Product::listAll();

	$page = new PageAdmin();

	$page->setTpl("products", [

		"products"=>$products

	]);
});

$app->get("/admin/products/create", function(){ //template para adicionar um novo produto

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("products-create");
});


$app->post("/admin/products/create", function(){ //cria um novo produto no banco de dados

	User::verifyLogin();

	$product = new Product();

	$product->setData($_POST);

	$product->save();

	header("Location: /admin/products");
	exit;
});

$app->get("/admin/products/:idproduct", function($idproduct){ //edita um produto

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->setData($_POST);

	$page = new PageAdmin();

	$page->setTpl("products-update", [

		'product'=>$product->getValues()

	]);
});


$app->post("/admin/products/:idproduct", function($idproduct){ //edita um produto

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->setData($_POST);

	$product->save();

if($_FILES["file"]["name"] !== "") $product->setPhoto($_FILES["file"]);

 	
	header('Location: /admin/products');
	exit;

});

	$app->get("/admin/products/:idproduct/delete", function($idproduct){ //edita um produto

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->delete();

	header('Location: /admin/products');
	exit;

});



?>