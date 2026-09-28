<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "loja";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Função para cadastrar um novo produto
function cadastrarProduto($conn, $codigo, $produto, $preco) {
    $sql = "INSERT INTO produtos (codigo, produto, preco, quantidade) VALUES ('$codigo', '$produto',' $preco', 0)";

    if ($conn->query($sql) === TRUE) {
        echo "Produto foi cadastrado!";
        header('location: registrar.html');
    } else {
        echo "Erro ao cadastrar produto: " . $conn->error;
    }
}

// Função para registrar entrada ou saída de produtos
function registrar($conn, $codigo, $quantidade, $tipo_mov) {
    $sql = "SELECT quantidade FROM produtos WHERE codigo = '$codigo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $quantidade_atual = $row['quantidade'];

        if ($tipo_mov == 'entrada') {
            $quantidade_atual += $quantidade;
        } elseif ($tipo_mov == 'saida') {
            if ($quantidade_atual >= $quantidade) {
                $quantidade_atual -= $quantidade;
            } else {
                echo "Erro: Quantidade em estoque insuficiente.";
                return;
            }
        }

        $sql_update = "UPDATE produtos SET quantidade = $quantidade_atual WHERE codigo = '$codigo'";

        if ($conn->query($sql_update) === TRUE) {
            echo "Alteração inserida!";
        } else {
            echo "Erro ao registrar alteração: " . $conn->error;
        }
    } else {
        echo "Erro: Produto não encontrado no estoque.";
    }
}

// Verificar qual formulário foi submetido e chamar a função correspondente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["codigo"]) && isset($_POST["nome"]) && isset($_POST["preco"])) {
        cadastrarProduto($conn, $_POST["codigo"], $_POST["nome"], $_POST["preco"]);
    } elseif (isset($_POST["codigo_mov"]) && isset($_POST["quantidade"]) && isset($_POST["tipo_mov"])) {
        registrarMovimentacao($conn, $_POST["codigo_mov"], $_POST["quantidade"], $_POST["tipo_mov"]);
    } else {
        echo "Erro: Parâmetros incompletos.";
    }
}

// Fechar a conexão com o banco de dados
$conn->close();
?>
