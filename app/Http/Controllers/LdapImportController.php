<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use App\Models\User;

class LdapImportController extends Controller
{
    // Exibe o formulário de conexão/configuração LDAP
    public function showImportForm()
    {
        return view('ldap.import');
    }

    // Faz preview dos usuários encontrados no LDAP
    public function preview(Request $request)
    {
        $preview = [];
        try {
            $config = [
                'hosts'    => [$request->host],
                'base_dn'  => $request->base_dn,
                'username' => $request->username,
                'password' => $request->password,
                'port'     => $request->port ?? 389,
                'use_ssl'  => $request->ssl == '1',
            ];
            $provider = new \Adldap\Adldap();
            $provider->addProvider($config, 'custom');
            $ldap = $provider->connect('custom');
            $users = $ldap->search()->users()->get();
            foreach ($users as $user) {
                $preview[] = [
                    'name' => $user->getCommonName(),
                    'login' => $user->getAccountName(),
                    'email' => $user->getEmail(),
                    'group' => $user->getFirstGroup() ? $user->getFirstGroup()->getCommonName() : '',
                ];
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao conectar no LDAP: ' . $e->getMessage());
        }
        // Sugestão: ordenar a lista de usuários por nome para melhor usabilidade
        usort($preview, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        return view('ldap.import', compact('preview'));
    }

    // Importa os usuários selecionados
    public function import(Request $request)
    {
        $imported = 0;
        if ($request->users && is_array($request->users)) {
            foreach ($request->users as $login) {
                // Aqui você pode buscar mais dados do AD se necessário
                // Exemplo: salvar usuário com dados mínimos
                User::updateOrCreate(
                    ['email' => $login.'@empresa.com'], // ajuste conforme seu AD
                    [
                        'name' => ucfirst(str_replace('.', ' ', $login)),
                        'email' => $login.'@empresa.com',
                        'password' => bcrypt(uniqid()),
                        'role' => 'user',
                        'is_active' => true,
                        'ldap_dn' => $login,
                    ]
                );
                $imported++;
            }
        }
        return redirect()->route('ldap.import.form')->with('success', "$imported usuário(s) importado(s) com sucesso!");
    }
}
