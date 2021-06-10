<?
function reload() {
setcookie('nome_cookie', null, -1, '/');
header('Location: geral.php');
}

?>