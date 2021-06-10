<?php

echo consulta_dados();

function reload() {
    header('Location: geral.php');
}

function consulta_dados(){
    require "conexao.php";
    $sql = 'SELECT id, cep FROM enderecos_api_google WHERE uf = "erro via_cep"';
    // $sql = "SELECT id, link FROM enderecos_api_google WHERE id = 1";
    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $count++;

            if($count == 99){echo reload();}
            
            $id = $row['id'];
            $cep = $row['cep'];
            
            $via_cep = "https://viacep.com.br/ws/$cep/json/";
            $complemento_cep = json_decode(file_get_contents($via_cep), true);
            $bairro = $complemento_cep['bairro'];
            $cidade = $complemento_cep['localidade'];
            $uf = $complemento_cep['uf'];

            echo updateDados($id,$bairro,$cidade,$uf);
            
        }
    } else {
        echo "0 resultados";
    }
}

// function updateDB($id,$endereço_completo,$rua,$cep,$bairro,$cidade,$uf){
function updateDados($id,$bairro,$cidade,$uf){
    require("conexao.php");
    $sql="UPDATE enderecos_api_google 
    SET 
    bairro='$bairro', 
    cidade='$cidade', 
    uf='$uf' 
    WHERE id=$id";

    if(mysqli_query($link, $sql)){
        echo 'ok';
    } else{
        echo "Erro:  $sql. " . mysqli_error($link);

        // $sql="UPDATE enderecos_api_google SET cep='erro' WHERE id=$id";if(mysqli_query($link, $sql)){
        //     echo "E ";
        // } else{
        //     echo "  $sql. " . mysqli_error($link);}
    }}

?>