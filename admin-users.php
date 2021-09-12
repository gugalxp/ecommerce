<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get("/admin/users/create", function() { //rota para criar um novo usuario

    User::verifyLogin();//verifica o login

    $page = new PageAdmin();

    $page->setTpl("users-create");

    });

    //delete

    $app->get("/admin/users/:iduser/delete", function($iduser) { // rota para excluir um usuario

    User::verifyLogin();//verifica o login

    $user = new User;

    $user->get((int)$iduser);

    $user->delete();

    header("Location: /admin/users");
    exit;

    });

    $app->get("/admin/users/:iduser", function($iduser) { // rota para alterar um dado do usuario 
    User::verifyLogin();//verifica o login

    $user = new User();

    $user->get((int)$iduser);

    $page = new PageAdmin();

    $page->setTpl("users-update", array(

        "user"=>$user->getValues()
    ));

    });

    $app->get("/admin/users", function() { // rota para a pagina principal da lista de usuarios1

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

    $_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; // recebe através do admin o check vai ser false e true para definir se tera ou nao acesso ao admin e tmb tem a confimação se a conta cadastrada vai ter aceeso ao admin

    $user->setData($_POST);

    $user->save(); // usa o metodo save para fazer insert no banco de dados, sendo assim, salvando os dados requeridos

    header("Location: /admin/users"); // após salvar, cria uma rota para o caminho determinado aqui
    exit;

        

    });

    $app->post("/admin/users/create", function () { // pega o novo usuario admin e permite com que ele tenha acesso ao administrador

        User::verifyLogin();

        $user = new User();

        $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

        $_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

            "cost"=>12

        ]);

        $user->setData($_POST);

        $user->save();

        header("Location: /admin/users");
        exit;

    });



    $app->post("/admin/users/:iduser", function($iduser) { //rota para o update do inadmin

    User::verifyLogin();//verifica o login

    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

    $user->get((int)$iduser); 

    $user->setData($_POST);

    $user->update();

    header("Location: /admin/users");
    exit;

    });



 ?>