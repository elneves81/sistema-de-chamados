<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UbsLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ubs = [
            ['code' => '970972', 'name' => 'VACINA EPIDEMIOLOGIA'],
            ['code' => '9080635', 'name' => 'ESF XARQUINHO II'],
            ['code' => '0287202', 'name' => 'FARMACIA CENTRAL'],
            ['code' => '3584445', 'name' => 'ESF SAO MIGUEL'],
            ['code' => '2705753', 'name' => 'ESF SANTA CRUZ'],
            ['code' => '0777897', 'name' => 'NUCLEO DE SAUDE DIGITAL'],
            ['code' => '7463227', 'name' => 'UPA 24H BATEL'],
            ['code' => '2743221', 'name' => 'CAPS II GUARAPUAVA'],
            ['code' => '2742853', 'name' => 'UNIDADE DE PRONTO ATENDIMENTO TRIANON'],
            ['code' => '2741601', 'name' => 'ESF ENTRE RIOS'],
            ['code' => '2741652', 'name' => 'ESF RIO DAS PEDRAS'],
            ['code' => '2741792', 'name' => 'SAE SERVICO DE ATENDIMENTO ESPECIALIZADO'],
            ['code' => '2743299', 'name' => 'ESF JARDIM DAS AMERICAS'],
            ['code' => '3016730', 'name' => 'ESF TANCREDO NEVES'],
            ['code' => '3016749', 'name' => 'ESF RESIDENCIAL 2000'],
            ['code' => '3016838', 'name' => 'ESF ADAO KAMINSKI'],
            ['code' => '2741369', 'name' => 'AMBULATORIO MUNICIPAL PNEUMOLOGIA E DERMATOLOGIA SANITARIA'],
            ['code' => '6661297', 'name' => 'CAPS AD GUARAPUAVA'],
            ['code' => '3402843', 'name' => 'ESF PAZ E BEM'],
            ['code' => '2706180', 'name' => 'ESF JORDAO'],
            ['code' => '2741563', 'name' => 'ESF BONSUCESSO'],
            ['code' => '2741598', 'name' => 'ESF CAMPO VELHO'],
            ['code' => '2741636', 'name' => 'ESF MORRO ALTO'],
            ['code' => '2741679', 'name' => 'ESF VILA CARLI'],
            ['code' => '2741555', 'name' => 'CEO CENTRO DE ESPECIALIDADES ODONTOLOGICAS GPUAVA'],
            ['code' => '2741644', 'name' => 'ESF PRIMAVERA'],
            ['code' => '2743302', 'name' => 'ESF RECANTO FELIZ'],
            ['code' => '6430651', 'name' => 'SECRETARIA MUNICIPAL DE SAUDE VIGILANCIA EM SAUDE'],
            ['code' => '6936210', 'name' => 'CENTRAL DE REGULACAO SAMU GUARAPUAVA'],
            ['code' => '7513348', 'name' => 'AMBULATORIO DE CURATIVOS ESPECIAIS GUARAPUAVA'],
            ['code' => '7513747', 'name' => 'CAPS AD III INFANTO JUVENIL CIS 5 REGIONAL DE SAUDE'],
            ['code' => '7513739', 'name' => 'CAPS AD III ADULTO CIS 5 REGIONAL DE SAUDE'],
            ['code' => '6397972', 'name' => 'LABORATORIO MUNICIPAL GUARAPUAVA'],
            ['code' => '6592171', 'name' => 'ESF VILA FEROZ'],
            ['code' => '3091120', 'name' => 'ESF PLANALTO'],
            ['code' => '3091139', 'name' => 'ESF PARQUE DAS ARVORES'],
            ['code' => '7423780', 'name' => 'SAMU BRAVO II GUARAPUAVA'],
            ['code' => '7423802', 'name' => 'SAMU BRAVO I GUARAPUAVA'],
            ['code' => '7423810', 'name' => 'SAMU ALFA GUARAPUAVA'],
            ['code' => '7429711', 'name' => 'MELHOR EM CASA GUARAPUAVA'],
            ['code' => '2742365', 'name' => 'ESF GUAIRACA'],
            ['code' => '9917233', 'name' => 'UNIDADE DE PRONTO ATENDIMENTO PRIMAVERA'],
            ['code' => '3409635', 'name' => 'ESF SAO CRISTOVAO'],
            ['code' => '6483798', 'name' => 'SERVICO DE RADIOLOGIA MUNICIPAL GUARAPUAVA'],
            ['code' => '3584461', 'name' => 'ESF JARDIM ARAUCARIA'],
            ['code' => '2743310', 'name' => 'ESF VILA COLIBRI'],
            ['code' => '3016773', 'name' => 'ESF CONCORDIA'],
            ['code' => '3722988', 'name' => 'ESF ENTRE RIOS II'],
            ['code' => '3016811', 'name' => 'CENTRO DE SAUDE DA MULHER'],
            ['code' => '2706164', 'name' => 'ESF XARQUINHO'],
            ['code' => '2706172', 'name' => 'ESF VILA BELA'],
            ['code' => '2706199', 'name' => 'ESF PALMEIRINHA'],
            ['code' => '2741571', 'name' => 'ESF BOQUEIRAO'],
            ['code' => '2741628', 'name' => 'ESF GUARA'],
            ['code' => '2741660', 'name' => 'ESF SANTANA'],
            ['code' => '4465482', 'name' => 'ESF PALMEIRINHA VOLANTE'],
            ['code' => '4465369', 'name' => 'DIVISAO DE PERICIA MEDICA'],
            ['code' => '4403460', 'name' => 'SAMU ALFA 02 GUARAPUAVA'],
            ['code' => '4560604', 'name' => 'DIVISAO DE SEGURANCA E MEDICINA DO TRABALHO'],
            ['code' => '4847636', 'name' => 'ESF CENTRO GEORGE KARAM'],
        ];

        $now = Carbon::now();
        
        foreach ($ubs as $location) {
            // Verifica se já existe
            $exists = DB::table('locations')
                ->where('name', $location['name'])
                ->exists();
                
            if (!$exists) {
                DB::table('locations')->insert([
                    'name' => $location['name'],
                    'short_name' => $location['code'],
                    'city' => 'Guarapuava',
                    'state' => 'PR',
                    'country' => 'Brasil',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
        
        $this->command->info('UBS adicionadas com sucesso!');
    }
}
