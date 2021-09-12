<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;


$app->get("/admin/categories", function(){

    User::verifyLogin();//verifica o login

    $categories = Category::listAll();

    $page = new PageAdmin();
 
    $page->setTpl("categories", [

        'categories'=>$categories

    ]); //parte que relaciona o banco de dados com o template

});

$app->get("/admin/categories/create", function(){

    User::verifyLogin();//verifica o login

   $page = new PageAdmin();
 
    $page->setTpl("categories-create"); //parte que relaciona o banco de dados com o template

});


$app->post("/admin/categories/create", function(){

    User::verifyLogin();//verifica o login
  
    $category = new Category();

    $category->setData($_POST);

    $category->save();

    header('Location: /admin/categories');  
    exit;

    $page = new PageAdmin();
 
    $page->setTpl("categories-create"); //parte que relaciona o banco de dados com o template

});


$app->get("/admin/categories/:idcategory/delete", function($idcategory)
{
    User::verifyLogin();//verifica o login

    $category = new Category();

    $category->get((int)$idcategory);

    $category->delete();

     header('Location: /admin/categories');  
    exit;


});


$app->get("/admin/categories/:idcategory", function($idcategory)
{
    User::verifyLogin();//verifica o login

    $category = new Category();

    $category->get((int)$idcategory);

    $page = new PageAdmin();
 
    $page->setTpl("categories-update", [

        'category'=>$category->getValues()

    ]); //parte que relaciona o banco de dados com o template


});

$app->post("/admin/categories/:idcategory", function($idcategory)
{

    $category = new Category();

    $category->get((int)$idcategory);

    $category->setData($_POST);

    $category->save();

    header('Location: /admin/categories');  
    exit;


});


$app->get("/categories/:idcategory", function($idcategory)
{

    $category = new Category();

    $category->get((int)$idcategory);

    $page = new Page();

    $page->setTpl("category", [

        'category'=>$category->getValues()

    ]);

});

 ?>