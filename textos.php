<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize variables with empty values
$nome = $email = $telefone = $endereco = $cidade = $estado = $titulo = $descricao = "";
$nome_err = $email_err = $telefone_err = $endereco_err = $cidade_err = $estado_err = $titulo_err = $descricao_err = "";

// Define a function to sanitize form input
function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate Nome
  if (empty($_POST["nome"])) {
    $nome_err = "O campo Nome é obrigatório";
  } else {
    $nome = test_input($_POST["nome"]);
    // Check if nome only contains letters and whitespace
    if (!preg_match("/^[a-zA-ZÀ-ú\s]+$/", $nome)) {
      $nome_err = "O campo Nome só pode conter letras e espaços em branco";
    }
  }

  // Validate Email
  if (empty($_POST["email"])) {
    $email_err = "O campo Email é obrigatório";
  } else {
    $email = test_input($_POST["email"]);
    // Check if email address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email_err = "O campo Email não é válido";
    }
  }

  // Validate Telefone
  if (empty($_POST["telefone"])) {
    $telefone_err = "O campo Telefone é obrigatório";
  } else {
    $telefone = test_input($_POST["telefone"]);
    // Check if telefone is well-formed
    if (!preg_match("/^[0-9]{10,11}$/", $telefone)) {
      $telefone_err = "O campo Telefone deve ter 10 ou 11 dígitos numéricos";
    }
  }

  // Validate Endereço
  if (empty($_POST["endereco"])) {
    $endereco_err = "O campo Endereço é obrigatório";
  } else {
    $endereco = test_input($_POST["endereco"]);
  }

  // Validate Cidade
  if (empty($_POST["cidade"])) {
    $cidade_err = "O campo Cidade é obrigatório";
  } else {
    $cidade = test_input($_POST["cidade"]);
  }

  // Validate Estado
  if (empty($_POST["estado"])) {
    $estado_err = "O campo Estado é obrigatório";
  } else {
    $estado = test_input($_POST["estado"]);
  }

  // Validate Título
  if (empty($_POST["titulo"])) {
    $titulo_err = "O campo Título da obra é obrigatório";
  } else {
    $titulo = test_input($_POST["titulo"]);
  }

  // Validate Descrição
  if (empty($_POST["descricao"])) {
    $descricao_err = "O campo Descrição da obra é obrigatório";
  } else {
    $descricao = test_input($_POST["descricao"]);
  }

  // Validate file input

  // Validate rules acceptance
  if (empty($_POST["aceite"])) {
    $$aceite_err = '';
    if (!isset($_POST['aceite'])) {
      $aceite_err = 'Você precisa aceitar o regulamento para enviar o formulário.';
    } else {
      $aceite = test_input($_POST["aceite"]);
    }
  }

  // If there are no errors, send form data to database
  if (empty($nome_err) && empty($email_err) && empty($telefone_err) && empty($endereco_err) && empty($cidade_err) && empty($estado_err) && empty($titulo_err) && empty($descricao_err)) {
    // Create connection
    $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind parameters to insert into database
    $stmt = $conn->prepare("INSERT INTO inscricoes (nome, email, telefone, endereco, cidade, estado, titulo, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nome, $email, $telefone, $endereco, $cidade, $estado, $titulo, $descricao);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Redirect to success page
    header("Location: success.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Regulamento - Coletânea A Força</title>
  <link rel="stylesheet" href="https://unpkg.com/tailwindcss@latest/dist/tailwind.min.css">
</head>

<body>
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-4 sm:p-6 md:p-8">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div class="sm:flex sm:items-center">
          <div class="text-xl leading-7 font-bold text-gray-900 sm:text-2xl sm:truncate">
            Inscrição - Coletânea A Força
          </div>
        </div>
      </div>
      <form class="mt-8 space-y-6" action="send-to-db.php" method="POST">
        <input type="hidden" name="remember" value="true">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="nome" class="sr-only">Nome</label>
            <input id="nome" name="nome" type="text" value="<?php echo htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES); ?>" autocomplete="name" required class="input <?php echo $nome_err ? 'input--error' : ''; ?>" placeholder="Nome completo">
            <?php if ($nome_err) : ?>
              <p class="text-red-500 mt-1"><?php echo $nome_err; ?></p>
            <?php endif; ?>
          </div>
          <div>
            <label for="email" class="sr-only">E-mail</label>
            <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" autocomplete="email" required class="input <?php echo $email_err ? 'input--error' : ''; ?>" placeholder="E-mail">
            <?php if ($email_err) : ?>
              <p class="text-red-500 mt-1"><?php echo $email_err; ?></p>
            <?php endif; ?>
          </div>
          <div>
            <label for="telefone" class="sr-only">Telefone</label>
            <input id="telefone" name="telefone" type="tel" value="<?php echo htmlspecialchars($_POST['telefone'] ?? '', ENT_QUOTES); ?>" autocomplete="tel" required class="input <?php echo $telefone_err ? 'input--error' : ''; ?>" placeholder="Telefone">
            <?php if ($telefone_err) : ?>
              <p class="text-red-500 mt-1"><?php echo $telefone_err; ?></p>
            <?php endif; ?>
          </div>
          <div>
            <label for="endereco" class="sr-only">Endereço</label>
            <input id="endereco" name="endereco" type="text" value="<?php echo htmlspecialchars($_POST['endereco'] ?? '', ENT_QUOTES); ?>" autocomplete="address-line1" required class="input <?php echo $endereco_err ? 'input--error' : ''; ?>" placeholder="Endereço completo">
            <?php if ($endereco_err) : ?>
              <p class="text-red-500 mt-1"><?php echo $endereco_err; ?></p>
            <?php endif; ?>
          </div>
          <div>
            <label for="cidade" class="sr-only">Cidade</label>
            <input id="cidade" name="cidade" type="text" value="<?php echo htmlspecialchars($_POST['cidade'] ?? '', ENT_QUOTES); ?>" autocomplete="address-level2" required class="input <?php echo $cidade_err ? 'input--error' : ''; ?>" placeholder="Cidade">
            <?php if ($cidade_err) : ?>
              <p class="text-red-500 mt-1"><?php echo $cidade_err; ?></p>
            <?php endif; ?>
          </div>
          <div>
            <label for="estado" class="sr-only">Estado</label>
            <select id="estado" name="estado" required class="input <?php echo $estado_err ? 'input--error'    : ''; ?>">
              <option value="">Selecione o estado</option>
              <?php foreach ($estados as $estado) : ?>
                <option value="<?php echo $estado; ?>" <?php echo isset($_POST['estado']) && $_POST['estado'] === $estado ? 'selected' : ''; ?>><?php echo $estado; ?></option>
              <?php endforeach; ?>
            </select>
            <?php if ($estado_err) : ?>
              <p class="text-red-500 mt-1"><?php echo $estado_err; ?></p>
            <?php endif; ?>
          </div>
          <div>
            <label for="mensagem" class="sr-only">Mensagem</label>
            <textarea id="mensagem" name="mensagem" rows="4" required class="input <?php echo $mensagem_err ? 'input--error' : ''; ?>" placeholder="Mensagem"><?php echo htmlspecialchars($_POST['mensagem'] ?? '', ENT_QUOTES); ?></textarea>
            <?php if ($mensagem_err) : ?>
              <p class="text-red-500 mt-1"><?php echo $mensagem_err; ?></p>
            <?php endif; ?>
          </div>
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input id="aceite" name="aceite" type="checkbox" class="checkbox" required>
            <label for="aceite" class="ml-2">
              Li e concordo com o <a href="#" class="link">regulamento</a>
            </label>
            <?php if ($aceite_err) : ?>
              <p class="text-red-500 mt-1"><?php echo $aceite_err; ?></p>
            <?php endif; ?>
          </div>
          <button type="submit" class="button">
            Enviar
          </button>
        </div>
      </form>


    </div>
  </div>
</body>

</html>
