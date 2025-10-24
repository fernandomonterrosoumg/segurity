<?php
session_start();

if (!isset($_SESSION['usuario_logeado'])) {
    // Redirigir al login si no hay un usuario logueado
    header('location:login/login.php');
    exit(); // Asegura que el script se detenga después de la redirección
}


if (isset($_GET['code']) && isset($_GET['state'])) {
  // 3.1 Validar state
  if (!isset($_SESSION['oauth2_state']) || $_GET['state'] !== $_SESSION['oauth2_state']) {
    http_response_code(400);
    exit('State inválido');
  }

  $clientId = '755430828323-2l1p92jqk8uppk4evsnl1rc2egaa9c01.apps.googleusercontent.com';
  $clientSecret = '****RgKz'; // guarda esto en un archivo seguro/ENV
  $redirectUri = 'http://localhost:80/index.php';
  $code = $_GET['code'];
  $codeVerifier = $_SESSION['oauth2_pkce_verifier'] ?? null;

  // 3.2 Intercambiar code por tokens
  $tokenResponse = http_post_json('https://oauth2.googleapis.com/token', [
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'code' => $code,
    'code_verifier' => $codeVerifier,
    'grant_type' => 'authorization_code',
    'redirect_uri' => $redirectUri,
  ]);

  if (!$tokenResponse || !isset($tokenResponse['id_token'])) {
    http_response_code(400);
    exit('No se pudo obtener tokens de Google');
  }

  // 3.3 Validar ID Token (sencillo vía tokeninfo para dev)
  $idToken = $tokenResponse['id_token'];
  $ti = json_decode(file_get_contents('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken)), true);
  if (!$ti || ($ti['aud'] ?? null) !== $clientId) {
    http_response_code(400);
    exit('ID token inválido');
  }

  $email = $ti['email'] ?? null;
  $emailVerified = ($ti['email_verified'] ?? 'false') === 'true';
  $name = $ti['name'] ?? '';
  $sub = $ti['sub'] ?? ''; // Google user id

  if (!$email || !$emailVerified) {
    http_response_code(400);
    exit('Email no verificado');
  }

  // 3.4 Vincular/crear usuario local y crear sesión
  require_once __DIR__ . '/EMRApp/core_functions/core_functions.php'; // si ahí está tu lógica DB
  $user = _findOrCreateUserFromGoogle($email, $name, $sub); // implementa esta función

  $_SESSION['auth_user_id'] = $user['id'];
  $_SESSION['auth_user_email'] = $email;
  $_SESSION['auth_provider'] = 'google';

  // 3.5 Redirigir a la app
  $dest = $_SESSION['oauth2_redirect'] ?? '/EMRApp';
  unset($_SESSION['oauth2_state'], $_SESSION['oauth2_pkce_verifier'], $_SESSION['oauth2_redirect']);
  header('Location: ' . $dest);
  exit;
}

// === helpers ===
function http_post_json($url, array $params) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
  $out = curl_exec($ch);
  if ($out === false) return null;
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($status !== 200) return null;
  return json_decode($out, true);
}


require_once "include/sidebar/sidebar.php";

?>
