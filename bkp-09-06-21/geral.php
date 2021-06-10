<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="refresh" content="1" >  -->
    <title>GEOCODING</title>
</head>
<body>

<?php

//varre banco e traz o link das lat e lng que precisa de cep, rua e bairro

echo enviando();
// echo "teste";

function enviando(){
    require("conexao.php");
    $sql1 = "SELECT id, lat, lng, link FROM enderecos WHERE cep is null";
    $result = $link->query($sql1);
    if ($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
            $link = $row["link"];
            $lat = $row['lat'];
            $lng = $row['lng'];
            $id = $row['id'];
            $dados = json_decode(file_get_contents($link),true);

            //alguns lat lng está pegando estabelecimentos, preciso splitar e pensar sobre

            $rua = $dados['items']['0']['address']['street'];

            if ($rua == "") {
                //tratar label depois                
                $label = $dados['items']['0']['address']['label'];
                $cep = $dados['items']['0']['address']['postalCode'];
                $cidade = $dados['items']['0']['address']['city'];
                $rua = "ajustar";
                
                echo envia($tipo,$rua,$cep,$cidade,$id,$label,$titulo);

            }else {
                $titulo = $dados['items']['0']['title'];
                $rua = $dados['items']['0']['address']['street'];
                $cep = $dados['items']['0']['address']['postalCode'];
                $cidade = $dados['items']['0']['address']['city'];
                $label = "N/A";
                
                echo envia($tipo,$rua,$cep,$cidade,$id,$label,$titulo);
            }
            $contador = $contador + 1;
            if ($contador >= 40){
                include ('update.php');
                echo reload();
            }
        }
    } else {
        echo "0 resultados";
    }
}

function envia($tipo,$rua,$cep,$cidade,$id,$label){
    require("conexao.php");
        $sql="UPDATE enderecos SET rua='$rua', cep='$cep', cidade='$cidade', label='$label' WHERE id=$id";
        
        if(mysqli_query($link, $sql)){
            echo "boa";
        } else{
            echo "BAD :/  $sql. " . mysqli_error($link);

            $sql="UPDATE enderecos SET cep='0' WHERE id=$id";if(mysqli_query($link, $sql)){
                echo "boa, ";
            } else{
                echo "  $sql. " . mysqli_error($link);}
        }
}
?>

</body>
</html>