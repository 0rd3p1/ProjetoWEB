<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pega o valor do POST e atribui ás variáveis com segurança 
    $field = $_POST['field'] ?? '';
    $value = trim($_POST['value'] ?? '');
    $id = $_GET['id'];

    // Juntando o campo com o valor passando como parâmetro no validate para o Validation entender ('field' = 'value')
    $parsedPost = [$field => $value];

    if ($field == 'name' || $field == 'price' || $field == 'stock') {
        $validation = Validation::validate([
            $field => ['required']
        ], $parsedPost);
    }

    // Verifica se teve erro na validação
    if ($validation->notPass()) {
        $_SESSION['validation'] = $validation->validations;
        header("Location: /updateProduct?field=$fiel&id=$id");
        exit();
    }

    // Busca no banco o produto digitado
    $auth = $db->query(
        query: 'SELECT * FROM products WHERE name = :name',
        params: [
            'name' => $value
        ]
    )->fetch();

    // Verifica se existe o produto digitado já cadastrado
    if ($auth) {
        $_SESSION['error'] = "Produto já cadastrado!";
        header("Location: /updateProduct?field=$field&id=$id");
        exit();
    }

    $db->query(
        query: "UPDATE products SET $field = :value WHERE id = :id",
        params: [
            'value' => $value,
            'id' => $id
        ]
    );

    header("Location: /product");
    exit;
} else {
    view('updateProduct');
}