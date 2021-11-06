<?php
/**
 * Created by PhpStorm.
 * User: Hamilton
 * Date: 05/02/2019
 * Time: 14:53
 */

use \Hcode\PageAdmin;
use \Hcode\Model\User;



//rota para recuperar senha
$app->get("/admin/forgot", function () {

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);
    $page->setTpl('forgot');

});

//enviar email de recuperação
$app->post("/admin/forgot", function (){

    $user = User::getForgot($_POST["email"]);

    header("Location: /admin/forgot/sent");
    exit;
});

$app->get("/admin/forgot/sent", function (){

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);
    $page->setTpl("forgot-sent");
});

$app->get("/admin/forgot/reset", function (){

    $user = User::validForgotDecrypt($_GET["code"]);

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);
    $page->setTpl("forgot-reset", array(

            "name"=>$user["desperson"],
            "code"=>$_GET["code"]
        )
    );
});

$app->post("/admin/forgot/reset", function (){

    $forgot = User::validForgotDecrypt($_POST["code"]);

    User::setFogotUsed($forgot["idrecovery"]);

    $user = new User();

    $user->get((int)$forgot["iduser"]);

    $password = password_hash($_POST["password"],PASSWORD_DEFAULT,[
        "cost"=>12
    ]);

    $user->setPassword($password);

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);
    $page->setTpl("forgot-reset-success");
});

?>
