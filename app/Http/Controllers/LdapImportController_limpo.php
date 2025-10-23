<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Adldap\Adldap as AdldapClient;
use Adldap\Auth\BindException;
use Adldap\Auth\UsernameRequiredException;
use Adldap\Auth\PasswordRequiredException;
use App\Models\User;

class LdapImportController extends Controller
{
    /**
     * Formulário de conexão/configuração LDAP.
     */
    public function showImportForm()
    {
        // View deve ter CSRF e campos: host, base_dn, username, password, port, ssl, filter, size
        return view('ldap.import');
    }

    /**
     * Preview dos usuários encontrados no LDAP com paginação e seleção de atributos.
     */
    public function preview(Request $request)
    {
        // Validação mínima
        $validator = Validator::make($request->all(), [
            'host'     => 'required|string',
            'base_dn'  => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'port'     => 'nullable|integer|min:1|max:65535',
            'ssl'      => 'nullable|boolean',
            'filter'   => 'nullable|string|max:80',
            'size'     => 'nullable|integer|min:1|max:500',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $size   = $request->integer('size', 100);
        $filter = trim((string) $request->input('filter', ''));
        $preview = [];

        try {
            $provider = $this->connect($request);

            // Buscar apenas atributos necessários
            $select = ['cn', 'samaccountname', 'mail', 'userprincipalname', 'memberof', 'distinguishedname'];
            $search = $provider->search()->users()->select($select);

            if ($request->filled('base_dn')) {
                $search->in($request->base_dn);
            }

            if ($filter !== '') {
                $search->whereContains('samaccountname', $filter)
                       ->orWhereContains('cn', $filter);
            }

            $users = $search->paginate($size);

            foreach ($users as $u) {
                $cn   = method_exists($u, 'getCommonName') ? $u->getCommonName() : ($u->getFirstAttribute('cn') ?? '');
                $sam  = method_exists($u, 'getAccountName') ? $u->getAccountName() : ($u->getFirstAttribute('samaccountname') ?? '');
                $mail = method_exists($u, 'getEmail') ? $u->getEmail() : ($u->getFirstAttribute('mail') ?? '');
                $upn  = $u->getFirstAttribute('userprincipalname') ?? '';
                $dn   = $u->getDn() ?? ($u->getFirstAttribute('distinguishedname') ?? '');

                $group = '';
                if (method_exists($u, 'getGroups')) {
                    $first = $u->getGroups()->first();
                    $group = $first ? ($first->getCommonName() ?? '') : '';
                }

                $preview[] = [
                    'dn'    => $dn,
                    'upn'   => $upn,
                    'name'  => $cn ?: $sam ?: $upn ?: '(sem nome)',
                    'login' => $sam ?: $upn,
                    'email' => $mail ?: $upn,
                    'group' => $group,
                ];
            }
        } catch (UsernameRequiredException|PasswordRequiredException $e) {
            Log::warning('LDAP credenciais ausentes: '.$e->getMessage());
            return back()->with('error', 'Credenciais ausentes para conexão LDAP.')->withInput();
        } catch (BindException $e) {
            Log::warning('Falha de bind LDAP: '.$e->getMessage());
            return back()->with('error', 'Não foi possível autenticar no LDAP. Verifique usuário/senha.')->withInput();
        } catch (\Throwable $e) {
            Log::error('Erro LDAP preview: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Erro ao consultar o LDAP.')->withInput();
        }

        usort($preview, fn($a, $b) => strcmp($a['name'], $b['name']));

        return view('ldap.import', [
            'preview' => $preview,
            'applied_filter' => $filter,
            'limit' => $size,
        ]);
    }

    /**
     * Importa usuários selecionados.
     * Espera users[][dn/upn/name/login/email] enviados pelo formulário.
     */
    public function import(Request $request)
    {
        $payload = $request->input('users');

        if (empty($payload) || !is_array($payload)) {
            return redirect()->route('admin.ldap.import.form')->with('warning', 'Nenhum usuário selecionado.');
        }

        $imported = 0;

        DB::beginTransaction();
        try {
            foreach ($payload as $row) {
                $dn    = isset($row['dn']) ? trim((string) $row['dn']) : '';
                $upn   = isset($row['upn']) ? trim((string) $row['upn']) : '';
                $name  = isset($row['name']) ? trim((string) $row['name']) : '';
                $login = isset($row['login']) ? trim((string) $row['login']) : '';
                $email = isset($row['email']) ? trim((string) $row['email']) : '';

                // Identidade prioritária
                $identity = $dn ?: $upn ?: $email;
                if ($identity === '') {
                    continue;
                }

                if ($email === '' && str_contains($upn, '@')) {
                    $email = $upn;
                }

                $randomPass = Str::random(40);

                // Upsert por DN (ou email)
                $user = User::where('ldap_dn', $dn)->when(!$dn, function ($q) use ($email) {
                    if ($email !== '') {
                        $q->orWhere('email', $email);
                    }
                })->first();

                if ($user) {
                    $user->update([
                        'name'       => $name ?: ($login ?: $email ?: 'Usuário LDAP'),
                        'email'      => $email ?: ($user->email ?? null),
                        'ldap_dn'    => $dn ?: ($user->ldap_dn ?? null),
                        'ldap_upn'   => $upn ?: ($user->ldap_upn ?? null),
                        'is_active'  => true,
                        'role'       => $user->role ?? 'user',
                    ]);
                } else {
                    User::create([
                        'name'          => $name ?: ($login ?: $email ?: 'Usuário LDAP'),
                        'email'         => $email ?: null,
                        'password'      => bcrypt($randomPass),
                        'role'          => 'user',
                        'is_active'     => true,
                        'ldap_dn'       => $dn ?: null,
                        'ldap_upn'      => $upn ?: null,
                        'auth_via_ldap' => true,
                    ]);
                }

                $imported++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao importar usuários LDAP: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.ldap.import.form')->with('error', 'Falha ao importar usuários.');
        }

        return redirect()->route('admin.ldap.import.form')->with('success', "$imported usuário(s) importado(s) com sucesso!");
    }

    /**
     * Testa a conexão LDAP (sem depender de serviço externo).
     */
    public function testConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host'     => 'required|string',
            'base_dn'  => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'port'     => 'nullable|integer|min:1|max:65535',
            'ssl'      => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos: ' . implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            $provider = $this->connect($request);

            // Faz uma busca mínima só para validar acesso/base DN
            $users = $provider->search()->users()->select(['cn'])->limit(1)->get();

            $message = 'Conexão estabelecida com sucesso!';
            if (count($users) > 0) {
                $message .= ' 1 usuário de exemplo encontrado.';
            } else {
                $message .= ' Servidor acessível, mas nenhum usuário retornou com o filtro padrão.';
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (UsernameRequiredException|PasswordRequiredException $e) {
            Log::warning('LDAP credenciais ausentes: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Credenciais ausentes para conexão LDAP.'], 400);
        } catch (BindException $e) {
            Log::warning('Falha de bind LDAP: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Não foi possível autenticar no LDAP. Verifique usuário/senha.'], 401);
        } catch (\Throwable $e) {
            Log::error('Erro no teste de conexão LDAP: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Falha na conexão ao LDAP.'], 500);
        }
    }

    /**
     * Conecta ao LDAP com provider "custom".
     */
    private function connect(Request $request)
    {
        $config = [
            'hosts'    => [$request->host],
            'base_dn'  => $request->base_dn,
            'username' => $request->username,
            'password' => $request->password,
            'port'     => $request->integer('port', 389),
            'use_ssl'  => (bool) $request->boolean('ssl'),
            'timeout'  => 5,
        ];

        $client = new AdldapClient();
        $client->addProvider($config, 'custom');
        return $client->connect('custom');
    }
}
