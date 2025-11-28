<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Adldap\Adldap;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them after login. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    
    /**
     * Get the post login redirect path based on user role.
     *
     * @return string
     */
    public function redirectTo()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            
            // Admin e técnico vão para dashboard
            if (in_array($role, ['admin', 'technician'])) {
                return '/dashboard';
            }
            
            // Cliente vai para seus chamados
            if ($role === 'customer') {
                return '/tickets';
            }
        }
        
        return $this->redirectTo;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        $login = $request->input('login');
        
        // Sempre usar email
        return [
            'email' => $login,
            'password' => $request->input('password'),
        ];
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');
        
        // Buscar usuário por email OU username (case-insensitive)
        $user = User::where(function($query) use ($login) {
                        $query->whereRaw('LOWER(email) = ?', [strtolower($login)])
                              ->orWhereRaw('LOWER(username) = ?', [strtolower($login)]);
                    })
                    ->first();
        
        if (!$user) {
            // Tentar autenticação direta se não encontrou
            return $this->guard()->attempt(['email' => $login, 'password' => $password], $request->filled('remember'))
                || $this->guard()->attempt(['username' => $login, 'password' => $password], $request->filled('remember'));
        }
        
        // Se o usuário usa LDAP e LDAP está habilitado, tentar autenticação LDAP
        if ($user->auth_via_ldap && config('ldap_custom.authentication.enabled', false)) {
            return $this->attemptLdapLogin($user, $password, $request->filled('remember'));
        }
        
        // Tentar autenticação local com email primeiro
        if ($user->email && $this->guard()->attempt(['email' => $user->email, 'password' => $password], $request->filled('remember'))) {
            return true;
        }
        
        // Se falhou com email, tentar com username
        if ($user->username) {
            return $this->guard()->attempt(['username' => $user->username, 'password' => $password], $request->filled('remember'));
        }
        
        return false;
    }
    
    /**
     * Attempt LDAP authentication
     *
     * @param  User  $user
     * @param  string  $password
     * @param  bool  $remember
     * @return bool
     */
    protected function attemptLdapLogin($user, $password, $remember = false)
    {
        try {
            // Configurar conexão LDAP
            $config = [
                'hosts'    => [env('LDAP_HOSTS', '127.0.0.1')],
                'base_dn'  => env('LDAP_BASE_DN', ''),
                'username' => $user->username . '@' . $this->getDomainFromBaseDn(env('LDAP_BASE_DN', '')),
                'password' => $password,
                'port'     => env('LDAP_PORT', 389),
                'use_ssl'  => env('LDAP_USE_SSL', false),
                'use_tls'  => env('LDAP_USE_TLS', false),
                'timeout'  => 5,
            ];
            
            $ad = new Adldap();
            $ad->addProvider($config, 'default');
            
            try {
                $provider = $ad->connect('default');
                
                // Se conectou com sucesso, o login é válido
                if ($provider) {
                    // Login manual do usuário
                    Auth::login($user, $remember);
                    return true;
                }
            } catch (\Exception $e) {
                Log::warning('Falha na autenticação LDAP: ' . $e->getMessage(), [
                    'username' => $user->username,
                ]);
                // Fallback para autenticação local se configurado
                if (config('ldap_custom.authentication.fallback', true)) {
                    // Tentar com e-mail e com username
                    $ok = $this->guard()->attempt(['email' => $user->email, 'password' => $password], $remember)
                        || $this->guard()->attempt(['username' => $user->username, 'password' => $password], $remember);
                    if ($ok) {
                        Log::info('Login local realizado via fallback após falha LDAP para: ' . ($user->email ?: $user->username));
                        return true;
                    }
                }
                return false;
            }
            
            // Se não conectou e não lançou exceção, considerar fallback
            if (config('ldap_custom.authentication.fallback', true)) {
                $ok = $this->guard()->attempt(['email' => $user->email, 'password' => $password], $remember)
                    || $this->guard()->attempt(['username' => $user->username, 'password' => $password], $remember);
                if ($ok) {
                    Log::info('Login local realizado via fallback após falha LDAP silenciosa para: ' . ($user->email ?: $user->username));
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Erro ao tentar autenticação LDAP: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Extract domain from Base DN
     *
     * @param  string  $baseDn
     * @return string
     */
    protected function getDomainFromBaseDn($baseDn)
    {
        if (empty($baseDn)) {
            return '';
        }
        if (preg_match_all('/DC=([^,]+)/i', $baseDn, $matches) && !empty($matches[1])) {
            return strtolower(implode('.', $matches[1]));
        }
        return '';
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        // Log do login bem-sucedido
        Log::info('Login bem-sucedido para: ' . ($user->email ?: $user->username));
        
        // Atualizar último login
        $user->update(['last_login_at' => now()]);
        
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(\Illuminate\Http\Request $request)
    {
        // Log da tentativa de login falhada
        Log::warning('Tentativa de login falhada para: ' . $request->login);
        
        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }
}
