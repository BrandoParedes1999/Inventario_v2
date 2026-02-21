<?php
/**
 * config/session.php
 * Gestión unificada de sesiones y autenticación
 *
 * Reemplaza: sesion.php, Validacion.php, config/auth.php
 *
 * USO en páginas protegidas:
 *   require_once __DIR__ . '/../config/session.php';
 *   Session::check();                        // Solo verifica login
 *   Session::checkAdmin();                   // Solo para admins
 *   Session::check([ROL_ADMIN, ROL_USUARIO]);// Roles permitidos
 */

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/constants.php';

class Session
{
    // ─── Iniciar sesión segura ────────────────────────────────────
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_set_cookie_params([
            'lifetime' => 0,                        // Expira al cerrar navegador
            'path'     => '/',
            'domain'   => '',
            'secure'   => !IS_LOCAL,                // HTTPS en producción
            'httponly' => true,                     // No accesible por JS
            'samesite' => 'Lax',
        ]);

        session_start();

        // Regenerar ID para prevenir session fixation
        if (empty($_SESSION['_initiated'])) {
            session_regenerate_id(true);
            $_SESSION['_initiated'] = true;
        }
    }

    // ─── Verificar sesión activa + token en BD ────────────────────
    public static function check(array $roles = []): void
    {
        self::start();

        // Sin sesión → login
        if (empty($_SESSION['usuario'])) {
            self::destroy();
            self::redirectToLogin('sin_sesion');
        }

        // Verificar token contra BD
        self::verifyToken();

        // Verificar roles si se especificaron
        if (!empty($roles)) {
            self::checkRole($roles);
        }

        // Headers anti-caché
        self::noCacheHeaders();
    }

    // ─── Verificar que el rol sea Administrador ───────────────────
    public static function checkAdmin(): void
    {
        self::check([ROL_ADMIN]);
    }

    // ─── Verificar token único en BD (anti session hijacking) ─────
    private static function verifyToken(): void
    {
        global $conexion;

        if (empty($_SESSION['usuario_id']) || empty($_SESSION['token_sesion'])) {
            self::destroy();
            self::redirectToLogin('no_token');
        }

        $sql       = "SELECT id FROM usuarios WHERE id = ? AND session_token = ? AND estatus = ?";
        $stmt      = $conexion->prepare($sql);
        $userId    = (int) $_SESSION['usuario_id'];
        $token     = (string) $_SESSION['token_sesion'];
        $estActivo = USR_ACTIVO; // bind_param necesita variables, no constantes directas
        $stmt->bind_param("isi", $userId, $token, $estActivo);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            self::destroy();
            self::redirectToLogin('session_expirada');
        }
    }

    // ─── Verificar rol ────────────────────────────────────────────
    private static function checkRole(array $rolesPermitidos): void
    {
        $rolActual = $_SESSION['rol'] ?? '';

        if (!in_array($rolActual, $rolesPermitidos, true)) {
            // Guardar flag para mostrar modal de acceso denegado
            $_SESSION['acceso_denegado'] = true;
            header('Location: ' . BASE_URL . 'dashboard.php');
            exit;
        }
    }

    // ─── Helpers de sesión ────────────────────────────────────────
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function isAdmin(): bool
    {
        return self::get('rol') === ROL_ADMIN;
    }

    public static function isLoggedIn(): bool
    {
        self::start();
        return !empty($_SESSION['usuario']);
    }

    public static function userId(): ?int
    {
        return self::get('usuario_id') ? (int) self::get('usuario_id') : null;
    }

    public static function userName(): string
    {
        return self::get('nombre') ?? self::get('usuario') ?? 'Invitado';
    }

    public static function userRol(): string
    {
        return self::get('rol') ?? '';
    }

    // ─── Destruir sesión completamente ────────────────────────────
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    // ─── Headers anti-caché ───────────────────────────────────────
    public static function noCacheHeaders(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    // ─── Redirigir al login ───────────────────────────────────────
    private static function redirectToLogin(string $error = ''): void
    {
        $url = BASE_URL . 'index.php';
        if ($error) {
            $url .= '?error=' . urlencode($error);
        }
        header('Location: ' . $url);
        exit;
    }
}

// Iniciar sesión automáticamente al incluir este archivo
Session::start();