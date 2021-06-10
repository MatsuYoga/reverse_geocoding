<?php

echo consulta_dados();

function reload() {
    setcookie('nome_cookie', null, -1, '/');
    header('Location: geral.php');
}

function consulta_dados(){
    require "conexao.php";
    $sql = "SELECT id, link FROM enderecos_api_google WHERE cep is null";
    // $sql = "SELECT id, link FROM enderecos_api_google WHERE id = 1";
    $result = $link->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $count++;

            if($count == 99){echo reload();}
            $link = $row["link"];
            // $link = "https://maps.googleapis.com/maps/api/geocode/json?latlng=-23.5121507,-46.763413&key=AIzaSyBUVYIMe0gFUokwhIoM6dKsf8cf-kXdxGw";
            $id = $row['id'];

            $dados = json_decode(file_get_contents($link), true);

            $endereço_completo = $dados['results']['0']['formatted_address'];
            $numero = $dados['results']['0']['address_components']['0']['long_name'];
            $rua = $dados['results']['0']['address_components']['1']['long_name'];

            if ($dados['results']['0']['address_components']['0']['types']['route']){
                $rua = $dados['results']['0']['address_components']['0']['long_name'];
            } else {
                $rrua = $dados['results']['0']['address_components']['1']['long_name'];
            }

            $check_cep_4 = $dados['results']['0']['address_components']['4']['long_name'];
            $check_cep_5 = $dados['results']['0']['address_components']['5']['long_name'];
            $check_cep_6 = $dados['results']['0']['address_components']['6']['long_name'];
            $cep_type_4 = $dados['results']['0']['address_components']['4']['types']['0'];
            $cep_type_5 = $dados['results']['0']['address_components']['5']['types']['0'];
            $cep_type_6 = $dados['results']['0']['address_components']['6']['types']['0'];

            if ($cep_type_4 == "postal_code"){
                $cep = $check_cep_4;
            }else if ($cep_type_5 == "postal_code"){
                $cep = $check_cep_5;
            }else if ($cep_type_6 == "postal_code"){
                $cep = $check_cep_6;
            }else {
                $cep = "verificar";
            }

            if ($rua == "") {$rua = "Verificar Manualmente";}
            if ($endereço_completo == "") {$endereço_completo = "Verificar Manualmente";}
            
            if ($cep == "") {$cep = "Verificar Manualmente";
            }
            else {
                // $via_cep = "https://viacep.com.br/ws/$cep/json/";
                // $complemento_cep = json_decode(file_get_contents($via_cep), true);
                // $bairro = $complemento_cep['bairro'];
                // $cidade = $complemento_cep['localidade'];
                // $uf = $complemento_cep['uf'];

                // echo ($id,$endereço_completo,$rua,$cep);

                echo updateDB($id, $endereço_completo, $rua, $cep,$uf='erro via_cep');
            }
        }
    } else {
        echo "0 resultados";
    }
}

// function updateDB($id,$endereço_completo,$rua,$cep,$bairro,$cidade,$uf){
function updateDB($id, $endereço_completo, $rua, $cep,$uf){
    require("conexao.php");
    $sql="UPDATE enderecos_api_google 
    SET 
    endereco_completo='$endereço_completo', 
    rua='$rua', 
    cep='$cep', 
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