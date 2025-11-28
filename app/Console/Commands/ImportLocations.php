<?php

namespace App\Console\Commands;

use App\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locations:import {file? : Caminho para o arquivo CSV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa localizações a partir de um arquivo CSV';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = $this->argument('file') ?? public_path('localizacoes.csv');

        if (!file_exists($filePath)) {
            $this->error("Arquivo não encontrado: {$filePath}");
            $this->info("Crie um arquivo CSV com as seguintes colunas:");
            $this->info("nome,nome_curto,endereco,cidade,estado,pais,cep,telefone,email,observacao,ativo");
            return 1;
        }

        $this->info("Importando localizações de: {$filePath}");

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("Não foi possível abrir o arquivo.");
            return 1;
        }

        // Ler cabeçalho
        $header = fgetcsv($handle, 1000, ',');
        
        if (!$header) {
            $this->error("Arquivo CSV vazio ou inválido.");
            fclose($handle);
            return 1;
        }

        $imported = 0;
        $updated = 0;
        $errors = 0;
        $lineNumber = 1;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $lineNumber++;
                
                if (count($row) < 1 || empty(trim($row[0]))) {
                    continue; // Pular linhas vazias
                }

                // Mapear dados
                $data = [
                    'name' => $row[0] ?? '',
                    'short_name' => $row[1] ?? null,
                    'address' => $row[2] ?? null,
                    'city' => $row[3] ?? null,
                    'state' => $row[4] ?? null,
                    'country' => $row[5] ?? 'Brasil',
                    'postal_code' => $row[6] ?? null,
                    'phone' => $row[7] ?? null,
                    'email' => $row[8] ?? null,
                    'comment' => $row[9] ?? null,
                    'is_active' => isset($row[10]) ? (strtolower($row[10]) === 'sim' || $row[10] === '1' || strtolower($row[10]) === 'true') : true,
                ];

                // Validar dados
                $validator = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'short_name' => 'nullable|string|max:255',
                    'email' => 'nullable|email',
                ]);

                if ($validator->fails()) {
                    $this->warn("Linha {$lineNumber}: Erro de validação - " . implode(', ', $validator->errors()->all()));
                    $errors++;
                    continue;
                }

                // Verificar se já existe (por nome)
                $location = Location::where('name', $data['name'])->first();

                if ($location) {
                    // Atualizar
                    $location->update($data);
                    $updated++;
                    $this->line("Linha {$lineNumber}: Atualizada - {$data['name']}");
                } else {
                    // Criar nova
                    Location::create($data);
                    $imported++;
                    $this->info("Linha {$lineNumber}: Importada - {$data['name']}");
                }
            }

            DB::commit();
            fclose($handle);

            $this->newLine();
            $this->info("========================================");
            $this->info("Importação concluída!");
            $this->info("Novas localizações: {$imported}");
            $this->info("Atualizadas: {$updated}");
            if ($errors > 0) {
                $this->warn("Erros: {$errors}");
            }
            $this->info("========================================");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            $this->error("Erro durante a importação: " . $e->getMessage());
            return 1;
        }
    }
}
