<?php
// --- CORS (solo DEV; en PROD fija tu origen exacto) ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// --- Endpoints JSON: suprimir salida de errores al navegador ---
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('html_errors', 0);
ini_set('log_errors', 1);
if (!ini_get('error_log')) {
    ini_set('error_log', __DIR__ . '/error.log');
}

// --- Sesión antes de usar $_SESSION ---
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// --- Dependencias ---
require_once __DIR__ . '/../../core_functions/core_functions.php';

// --- Config OAuth Google ---
const GOOGLE_CLIENT_ID     = '755430828323-2l1p92jqk8uppk4evsnl1rc2egaa9c01.apps.googleusercontent.com';
const GOOGLE_CLIENT_SECRET = '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';
const GOOGLE_AUTH_EP       = 'https://accounts.google.com/o/oauth2/v2/auth';
const GOOGLE_TOKEN_EP      = 'https://oauth2.googleapis.com/token';

// Debe coincidir EXACTO con el registrado (#4 sin :80)
const GOOGLE_REDIRECT_URI  = 'http://localhost/EMRApp/login/function/ajax_functions.php?FUNC=oauth_google_callback';

// --- Router simple ---
try {
    $func = isset($_GET['FUNC']) ? $_GET['FUNC'] : null;

    switch ($func) {
        case 'guardarRegistro':
            json_out(guardarRegistro());
            break;

        case 'iniciarSesion':
            json_out(iniciarSesion());
            break;

        case 'oauth_google_init':
            oauth_google_init();     // redirect + exit
            break;

        case 'oauth_google_callback':
            oauth_google_callback(); // procesa tokens, crea usuario si falta, setea sesión y redirige
            break;

        default:
            http_response_code(400);
            json_out(['estado' => false, 'desc' => 'FUNC inválido']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    json_out(['estado' => false, 'desc' => $e->getMessage()]);
}


// ============== HANDLERS ==============

function guardarRegistro()
{
    try {
        $payload = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        $data = $payload['dataRegistro'] ?? [];

        if (empty($data['mail']) || empty($data['password']) || empty($data['nombre1']) || empty($data['apellido1'])) {
            throw new Exception("Campos obligatorios incompletos.");
        }

        $strExiste = "SELECT 1 FROM EMR.EMR_ACCE_USER WHERE USER_CORREO = :user_correo";
        $existe = _queryBind($strExiste, [':user_correo' => $data['mail']]);
        if (!empty($existe)) {
            throw new Exception("Ya existe un usuario con el correo ingresado.");
        }

        $sql =
        "INSERT INTO EMR.EMR_ACCE_USER
            (USER_NOMBRE1, USER_NOMBRE2, USER_APELLIDO1, USER_APELLIDO2,
             USER_CORREO, USER_PASS, ROL_ID, USER_FEC_CREA)
         VALUES
            (:user_nombre1, :user_nombre2, :user_apellido1, :user_apellido2,
             :user_correo, :user_pass, :rol_id, SYSDATE)";

        $params = [
            ':user_nombre1'   => $data['nombre1'],
            ':user_nombre2'   => $data['nombre2'] ?? null,
            ':user_apellido1' => $data['apellido1'],
            ':user_apellido2' => $data['apellido2'] ?? null,
            ':user_correo'    => $data['mail'],
            ':user_pass'      => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 10]),
            ':rol_id'         => 1,
        ];

        if (_queryBindCommit($params, $sql)) {
            return ['estado' => true, 'desc' => 'Usuario creado correctamente'];
        }
        throw new Exception("Error al crear usuario.");
    } catch (Throwable $e) {
        return ['estado' => false, 'desc' => $e->getMessage()];
    }
}

function iniciarSesion()
{
    try {
        $payload = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        $data = $payload['dataLogin'] ?? [];

        $mail = trim($data['mail'] ?? '');
        $pass = (string)($data['password'] ?? '');

        if ($mail === '') throw new Exception("Por favor llene el campo de correo electrónico.");
        if ($pass === '') throw new Exception("Por favor llene el campo de contraseña.");

        $sql = "SELECT USER_ID, USER_NOMBRE1, USER_NOMBRE2, USER_APELLIDO1, USER_APELLIDO2,
                       USER_CORREO, USER_PASS, ROL_ID
                FROM EMR.EMR_ACCE_USER
                WHERE USER_CORREO = :user_correo";
        $row = _queryBind($sql, [':user_correo' => $mail]);

        if (empty($row)) {
            throw new Exception("El correo ingresado no existe.");
        }

        $u = $row[0];
        if (!password_verify($pass, $u['USER_PASS'])) {
            throw new Exception("Contraseña inválida.");
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        set_user_session($u, 'local');
        return ['estado' => true, 'desc' => 'Bienvenido'];
    } catch (Throwable $e) {
        return ['estado' => false, 'desc' => $e->getMessage()];
    }
}

/**
 * Inicia flujo OAuth con Google (Authorization Code + PKCE)
 */
function oauth_google_init()
{
    $codeVerifier  = base64url(random_bytes(32));
    $codeChallenge = base64url(hash('sha256', $codeVerifier, true));
    $state         = bin2hex(random_bytes(16));

    $_SESSION['oauth2_state']         = $state;
    $_SESSION['oauth2_pkce_verifier'] = $codeVerifier;
    $_SESSION['oauth2_redirect']      = '/EMRApp'; // destino post-login

    $params = [
        'client_id'             => GOOGLE_CLIENT_ID,
        'redirect_uri'          => GOOGLE_REDIRECT_URI,
        'response_type'         => 'code',
        'scope'                 => 'openid email profile',
        'include_granted_scopes'=> 'true',
        'access_type'           => 'offline',
        'prompt'                => 'consent',
        'state'                 => $state,
        'code_challenge'        => $codeChallenge,
        'code_challenge_method' => 'S256',
    ];

    // Persistir la sesión antes del redirect (evita perder PKCE/state)
    session_write_close();

    header('Cache-Control: no-store');
    header('Pragma: no-cache');
    header('Location: ' . GOOGLE_AUTH_EP . '?' . http_build_query($params));
    exit;
}

/**
 * Callback: intercambia code por tokens, decodifica id_token (JWT) y,
 * si es necesario, consulta /userinfo. Crea usuario (si no existe) y abre sesión.
 */
function oauth_google_callback()
{
    // Validaciones iniciales
    if (!isset($_GET['code'], $_GET['state'])) {
        http_response_code(400);
        exit('Faltan parámetros de OAuth');
    }
    if (!isset($_SESSION['oauth2_state']) || $_GET['state'] !== $_SESSION['oauth2_state']) {
        http_response_code(400);
        exit('State inválido');
    }
    $code         = $_GET['code'];
    $codeVerifier = $_SESSION['oauth2_pkce_verifier'] ?? null;
    if (!$codeVerifier) {
        http_response_code(400);
        exit('PKCE verifier ausente (sesión perdida).');
    }

    // Intercambio de tokens (exponiendo diagnóstico si falla)
    $tokenRes = http_post_form(GOOGLE_TOKEN_EP, [
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'code'          => $code,
        'code_verifier' => $codeVerifier,
        'grant_type'    => 'authorization_code',
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
    ]);

    if (!$tokenRes || !isset($tokenRes['id_token'])) {
        $status = $tokenRes['__status'] ?? 0;
        $raw    = $tokenRes['__raw']    ?? json_encode($tokenRes);
        http_response_code(400);
        exit("No se pudo obtener tokens de Google (HTTP $status): $raw");
    }

    $idToken     = $tokenRes['id_token'];
    $accessToken = $tokenRes['access_token'] ?? null;

    // 1) Extraer datos desde el ID Token (JWT)
    $ti = decode_jwt_payload($idToken); // DEV: sin verificación de firma
    $email         = $ti['email'] ?? null;
    $emailVerified = ($ti['email_verified'] ?? false) ? true : false;
    $name          = $ti['name'] ?? '';
    $givenName     = $ti['given_name'] ?? '';
    $familyName    = $ti['family_name'] ?? '';
    $aud           = $ti['aud'] ?? null;

    if ($aud && $aud !== GOOGLE_CLIENT_ID) {
        http_response_code(400);
        exit('ID token no corresponde a este client_id');
    }

    // 2) Si faltan datos críticos, usar /userinfo con access_token
    if ((!$email || !$emailVerified) && $accessToken) {
        $ui = http_get_json('https://openidconnect.googleapis.com/v1/userinfo', [
            'Authorization: Bearer ' . $accessToken
        ]);
        if (is_array($ui)) {
            $email         = $email ?: ($ui['email'] ?? null);
            $emailVerified = $emailVerified ?: (($ui['email_verified'] ?? false) ? true : false);
            $name          = $name ?: ($ui['name'] ?? '');
            $givenName     = $givenName ?: ($ui['given_name'] ?? '');
            $familyName    = $familyName ?: ($ui['family_name'] ?? '');
        }
    }

    if (!$email || !$emailVerified) {
        http_response_code(400);
        exit('Email no disponible o no verificado');
    }

    // Buscar usuario por correo
    $sqlSel = "SELECT USER_ID, USER_NOMBRE1, USER_NOMBRE2, USER_APELLIDO1, USER_APELLIDO2,
                      USER_CORREO, USER_PASS, ROL_ID
               FROM EMR.EMR_ACCE_USER
               WHERE USER_CORREO = :user_correo";
    $row = _queryBind($sqlSel, [':user_correo' => $email]);

    if (empty($row)) {
        // Derivar nombre y apellido si hace falta
        if ($givenName === '' || $familyName === '') {
            $parts = preg_split('/\s+/', trim($name));
            $givenName  = $givenName  ?: ($parts[0] ?? '');
            $familyName = $familyName ?: ($parts[1] ?? '');
        }

        // Password aleatoria (no se usará para Google, pero evita nulos)
        $pwdHash = password_hash(random_password(16), PASSWORD_BCRYPT, ['cost' => 10]);

        $sqlIns =
          "INSERT INTO EMR.EMR_ACCE_USER
           (USER_NOMBRE1, USER_NOMBRE2, USER_APELLIDO1, USER_APELLIDO2,
            USER_CORREO, USER_PASS, ROL_ID, USER_FEC_CREA)
           VALUES
           (:n1, :n2, :a1, :a2, :correo, :pass, :rol, SYSDATE)";
        $paramsIns = [
            ':n1'     => $givenName ?: 'Google',
            ':n2'     => null,
            ':a1'     => $familyName ?: 'User',
            ':a2'     => null,
            ':correo' => $email,
            ':pass'   => $pwdHash,
            ':rol'    => 1,
        ];

        if (!_queryBindCommit($paramsIns, $sqlIns)) {
            http_response_code(500);
            exit('No se pudo crear el usuario');
        }

        // Releer el usuario recién creado
        $row = _queryBind($sqlSel, [':user_correo' => $email]);
        if (empty($row)) {
            http_response_code(500);
            exit('Usuario no disponible tras creación');
        }
    }

    $u = $row[0];

    // Abrir sesión
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    set_user_session($u, 'google');

    // Limpieza de artefactos OAuth
    unset($_SESSION['oauth2_state'], $_SESSION['oauth2_pkce_verifier']);

    // Redirigir a la app
    $dest = $_SESSION['oauth2_redirect'] ?? '/EMRApp';
    header('Location: ' . $dest);
    exit;
}


// ============== HELPERS ==============

function json_out(array $data): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function base64url($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function http_post_form(string $url, array $params): ?array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Enviar client_id/secret también por Basic Auth (aceptado por Google)
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Basic ' . base64_encode(GOOGLE_CLIENT_ID . ':' . GOOGLE_CLIENT_SECRET),
    ]);

    // Si tu PHP en Windows no tiene CA bundle, configura curl.cainfo en php.ini
    $out    = curl_exec($ch);
    $err    = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($out === false) {
        error_log("http_post_form cURL error: $err");
        return ['__status' => 0, '__error' => $err];
    }

    $json = json_decode($out, true);
    if ($status !== 200) {
        error_log("Token HTTP $status: $out");
        return ['__status' => $status, '__raw' => $out, '__json' => $json];
    }

    $json = is_array($json) ? $json : [];
    $json['__status'] = $status;
    return $json;
}

function http_get_json(string $url, array $headers = []): ?array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $out    = curl_exec($ch);
    $err    = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($out === false) {
        error_log("http_get_json cURL error: $err");
        return null;
    }
    if ($status !== 200) {
        error_log("GET $url HTTP $status: $out");
        return null;
    }
    return json_decode($out, true);
}

function decode_jwt_payload(string $jwt): array
{
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return [];
    $payload = $parts[1];
    $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4); // padding
    $json = base64_decode(strtr($payload, '-_', '+/'));
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}

function random_password(int $len = 16): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*()-_=+';
    $bytes = random_bytes($len);
    $out = '';
    for ($i = 0; $i < $len; $i++) {
        $out .= $alphabet[ord($bytes[$i]) % strlen($alphabet)];
    }
    return $out;
}

function set_user_session(array $u, string $provider): void
{
    $_SESSION['user_id']         = $u['USER_ID'];
    $_SESSION['nombre1']         = $u['USER_NOMBRE1'];
    $_SESSION['nombre2']         = $u['USER_NOMBRE2'];
    $_SESSION['apellido1']       = $u['USER_APELLIDO1'];
    $_SESSION['apellido2']       = $u['USER_APELLIDO2'];
    $_SESSION['correo']          = $u['USER_CORREO'];
    $_SESSION['rol_id']          = $u['ROL_ID'];
    $_SESSION['usuario_logeado'] = true;
    $_SESSION['auth_provider']   = $provider; // 'local' o 'google'
}
