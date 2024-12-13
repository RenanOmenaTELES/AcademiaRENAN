<?php
$host = "localhost";
$dbname = "academia";
$username = "root"; // Altere para o seu usuário do MySQL
$password = ""; // Altere para sua senha do MySQL

// Conectando ao banco de dados
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consultar planos disponíveis
$planos_result = $conn->query("SELECT * FROM planos");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('academia.jpg'); /* Adicione a imagem da academia no fundo */
            background-size: cover;
            background-position: center;
            color: white;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.5); /* Fundo transparente */
            padding: 30px;
            border-radius: 10px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Cadastro de Aluno</h2>
    <form action="cadastro.php" method="POST">
        <div class="form-group">
            <label for="nome">Nome Completo:</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>

        <div class="form-group">
            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="telefone">Telefone:</label>
            <input type="text" class="form-control" id="telefone" name="telefone" required>
        </div>

        <div class="form-group">
            <label for="endereco">Endereço:</label>
            <textarea class="form-control" id="endereco" name="endereco"></textarea>
        </div>

        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <select class="form-control" id="sexo" name="sexo" required>
                <option value="masculino">Masculino</option>
                <option value="feminino">Feminino</option>
                <option value="outro">Outro</option>
            </select>
        </div>

        <div class="form-group">
            <label for="plano">Escolha o Plano:</label>
            <select class="form-control" id="plano" name="plano" required>
                <?php while ($plano = $planos_result->fetch_assoc()) { ?>
                    <option value="<?php echo $plano['id']; ?>"><?php echo $plano['nome']; ?> - R$ <?php echo $plano['preco']; ?></option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Processando o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $sexo = $_POST['sexo'];
    $plano = $_POST['plano'];

    // Validações
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("E-mail inválido.");
    }

    if (!preg_match('/^\d{10,11}$/', $telefone)) {
        die("Telefone inválido.");
    }

    // Verificando idade
    $data_atual = new DateTime();
    $data_nasc = new DateTime($data_nascimento);
    $idade = $data_atual->diff($data_nasc)->y;
    if ($idade < 18) {
        die("O aluno deve ser maior de idade.");
    }

    // Inserindo no banco de dados
    $stmt = $conn->prepare("INSERT INTO alunos (nome, data_nascimento, email, telefone, endereco, sexo, plano_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $nome, $data_nascimento, $email, $telefone, $endereco, $sexo, $plano);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success mt-3'>Cadastro realizado com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Erro ao cadastrar aluno.</div>";
    }

    $stmt->close();
    $conn->close();
}
?>
