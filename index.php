<?php 

session_start(); // inicia a sessão

require_once("vendor/autoload.php");

use \Slim\Slim;
use  \Hcode\Page;
use  \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {

$page = new Page();

$page->setTpl("index");

});


$app->get('/admin', function() {

User::verifyLogin();//verifica o login

$page = new PageAdmin();

$page->setTpl("index");

});

$app->get('/admin/login', function() {

$page = new PageAdmin([

    "header"=>false,
    "footer"=>false 

]);

$page->setTpl("login");

});

$app->post('/admin/login', function() {

User::login($_POST["login"], $_POST["password"]);

header("Location: /admin");
exit; 

});

$app->get('/admin/logout', function(){

User::logout();

header("Location: /admin/login");
exit;

}); 

$app->get("/admin/users/create", function() {

User::verifyLogin();//verifica o login

$page = new PageAdmin();

$page->setTpl("users-create");

});

//delete



$app->get("/admin/users/:iduser/delete", function($iduser) {

User::verifyLogin();//verifica o login

$user = new User;

$user->get((int)$iduser);

$user->delete();

header("Location: /admin/users");
exit;

});

$app->get("/admin/users/:iduser", function($iduser) {

User::verifyLogin();//verifica o login

$user = new User();

$user->get((int)$iduser);

$page = new PageAdmin();

$page->setTpl("users-update", array(

    "user"=>$user->getValues()
));

});

$app->get("/admin/users", function() {

User::verifyLogin();//verifica o login

$users = User::listAll();

$page = new PageAdmin();

$page->setTpl("users", array(
    "users"=>$users

));
});

//post

$app->post("/admin/users/create", function() { //recebe informação da criação de nova conta

User::verifyLogin(); //verifica o login

$user = new User();

$_POST["inadmin"] = (isset($_POST["inadimin"]))?1:0; // confimação se a conta cadastrada vai ter aceeso ao admin

$user->setData($_POST);

$user->save(); // usa o metodo save para fazer insert no banco de dados, sendo assim, salvando os dados requeridos

header("Location: /admin/users"); // após salvar, cria uma rota para o caminho determinado aqui
exit;

    

});

$app->post("/admin/users/:iduser", function($iduser) {

User::verifyLogin();//verifica o login

$user = new User();

$_POST["inadmin"] = (isset($_POST["inadimin"]))?1:0;

$user->get((int)$iduser); 

$user->setData($_POST);

$user->update();

header("Location: /admin/users");
exit;

});


$app->post("/admin/users/users", function() {

User::verifyLogin();//verifica o login

});



$app->run();

 ?>