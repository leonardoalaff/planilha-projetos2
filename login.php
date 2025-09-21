<!-- login.php -->

<?php
session_start();

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha   = $_POST['senha'] ?? '';

    $usuarios = json_decode(file_get_contents("usuarios.json"), true);

    foreach ($usuarios as $u) {
        if ($u['usuario'] === $usuario && $u['senha'] === $senha) {
            $_SESSION['logado'] = true;
            $_SESSION['usuario'] = $usuario;
            $_SESSION['darkmode'] = $u['darkmode'] ?? false;
            header("Location: index.php");
            exit;
        }
    }
    $erro = "Usuário ou senha incorretos!";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f3f3f3;
      display: flex;
      height: 100vh;
      justify-content: center;
      align-items: center;
    }
    .login-box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      width: 300px;
      text-align: center;
    }
    .login-box h2 { margin-bottom: 20px; }
    .login-box input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .login-box button {
      width: 100%;
      padding: 10px;
      background: #0077cc;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .erro { color: red; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Acesso Restrito</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'sucesso'): ?>
  <p style="color: green;">Conta criada com sucesso! Faça login.</p>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'usuario_existe'): ?>
  <p style="color: red;">Usuário já existe. Escolha outro.</p>
<?php endif; ?>

    <form method="POST">
      <input type="text" name="usuario" placeholder="Usuário" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <button type="submit">Entrar</button>
    </form>
    <?php if (!empty($erro)): ?>
      <p class="erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
  </div>
</body>
</html>
