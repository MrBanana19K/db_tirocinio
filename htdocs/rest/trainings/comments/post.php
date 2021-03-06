<?php
/**
 * Created by Atom.
 * User: Enrico
 * Date: 28/02/18
 * Time: 11.30
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_STUDENT | \auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();
$return = [];
$comment_datum = $_POST['contenuto'];

if (empty($comment_datum))
    throw new RuntimeException("You have to supply a comment!", -1);

if (empty($_POST['tirocinio']))
    throw new RuntimeException("You have to supply a tirocinio ID!", -1);

if($user->is_authorized(\auth\LEVEL_GOOGLE_STUDENT))
{
    $autorizzato = $server->prepare("SELECT id FROM Tirocinio 
  WHERE studente = ?");
    $autorizzato->bind_param(
        "i",
        $user->get_database_id()
    );
}
elseif($user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER))
{
    $permissions = new \auth\PermissionManager($server, $user);
    $tutto_puo = $permissions->check("train.readall");

    $autorizzato = $server->prepare("SELECT id FROM Tirocinio 
  WHERE ? OR docenteTutore = ?");
    $autorizzato->bind_param(
        "ii",
        $tutto_puo,
        $user->get_database_id()
    );
}
else
    throw new RuntimeException("Invalid user", -1);

$autorizzato->execute();

if(!($autorizzato->fetch()))
    throw new RuntimeException("Non possiedi questo robo", -56);

$autorizzato->close();

$inser = $server->prepare("INSERT INTO Commento (tirocinio, autore, testo)
  VALUES (?,?,?)");

$inser->bind_param('iis', $_POST['tirocinio'], $user->get_database_id(), $comment_datum);
$inser->execute();

$return['tir']=$_POST['tirocinio'];
$return['user']=$user->get_database_id();
$return['datum']=$comment_datum;

echo json_encode($return, JSON_UNESCAPED_UNICODE);
