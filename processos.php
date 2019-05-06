<?php
	if (isset($_POST['key'])) {
        // Conectando ao servidor
		$banco = new mysqli('localhost', 'root', '', 'crud');
        
        //"Key" variavel usada para identificar qual processo será feito
		if ($_POST['key'] == 'pegarRow') { //pega um produto para edita-lo
			$listaID = $banco->real_escape_string($_POST['listaID']);
			$sql = $banco->query("SELECT nome, marca, quantidade FROM produtos WHERE id='$listaID'");
			$dados = $sql->fetch_array();
			$jsonArray = array(
				'nome' => $dados['nome'],
				'marca' => $dados['marca'],
				'quantidade' => $dados['quantidade'],
			);

			exit(json_encode($jsonArray));
 		}

		if ($_POST['key'] == 'pegarDados') { // Exibe os Dados do Banco de Dados
			$start = $banco->real_escape_string($_POST['start']);
			$limit = $banco->real_escape_string($_POST['limit']);

			$sql = $banco->query("SELECT id, nome,marca, quantidade FROM produtos LIMIT $start, $limit");
			if ($sql->num_rows > 0) {
				$response = "";
				while($dados = $sql->fetch_array()) { //Colunas exibindo os seus respectivos Dados // id nesse trecho sao para ajudar a atualizar o ajax
					$response .= '
						<tr class="warning">
							<td>'.$dados["id"].'</td>
							<td id="nome_'.$dados["id"].'" >'.$dados["nome"].'</td> 
                            <td id="marca_'.$dados["id"].'" >'.$dados["marca"].'</td>
                            <td id="quantidade_'.$dados["id"].'" >'.$dados["quantidade"].'</td>
							<td class = >
                                <input type="button" onclick="editar('.$dados["id"].')" value="Editar" class="btn btn-warning">
                                <input type="button" onclick="deleteRow('.$dados["id"].')" value="Deletar" class="btn btn-danger">
                            </td>
						</tr>
					';
				}
				exit($response);
			} else
				exit('reachedMax');
		}

		$listaID = $banco->real_escape_string($_POST['listaID']);

		if ($_POST['key'] == 'deleteRow') { //deleta um produto do banco de dados
			$banco->query("DELETE FROM produtos WHERE id='$listaID'");
			exit('success');
		}

		$nome = $banco->real_escape_string($_POST['nome']);
		$marca = $banco->real_escape_string($_POST['marca']);
		$quantidade = $banco->real_escape_string($_POST['quantidade']);

		if ($_POST['key'] == 'update') { //Atualiza um produto no servidor
			$banco->query("UPDATE produtos SET nome='$nome', marca='$marca', quantidade='$quantidade' WHERE id='$listaID'");
			exit('success');
		}
        
		if ($_POST['key'] == 'inserir') { // Adiciona ao banco de dados um produto se já nao existir esse mesmo produto com essa mesma marca
			$sql = $banco->query("SELECT id FROM produtos WHERE nome = '$nome'");
			if ($sql->num_rows > 0){
                for($i = 0;$i<$sql->num_rows;$i++){
                    $sql2 = $banco->query("SELECT id FROM produtos WHERE marca = '$marca'");
                    for($j=0;$j<$sql2->num_rows;$j++){
                        $aux = $sql->fetch_assoc();
                        $aux2 = $sql2->fetch_assoc();
                        
                        if($aux["id"] == $aux2["id"] ){
                                exit("Produto dessa marca ja existe");
                            }
                    }
                }
            }
            $banco->query("INSERT INTO produtos (nome, marca, quantidade) 
							VALUES ('$nome', '$marca', '$quantidade')");
                exit('success');
			}
	}
?>