<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Manufacturer;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manufacturers = [
            [
                'name' => 'Dell Technologies',
                'website' => 'https://www.dell.com',
                'support_phone' => '0800-123-4567',
                'support_email' => 'suporte@dell.com',
                'comment' => 'Fabricante de computadores e servidores',
                'is_active' => true,
            ],
            [
                'name' => 'HP Inc.',
                'website' => 'https://www.hp.com',
                'support_phone' => '0800-765-4321',
                'support_email' => 'support@hp.com',
                'comment' => 'Impressoras, computadores e notebooks',
                'is_active' => true,
            ],
            [
                'name' => 'Lenovo',
                'website' => 'https://www.lenovo.com',
                'support_phone' => '0800-111-2222',
                'support_email' => 'suporte@lenovo.com',
                'comment' => 'ThinkPad e dispositivos corporativos',
                'is_active' => true,
            ],
            [
                'name' => 'Samsung',
                'website' => 'https://www.samsung.com',
                'support_phone' => '0800-333-4444',
                'support_email' => 'contato@samsung.com',
                'comment' => 'Smartphones, tablets e monitores',
                'is_active' => true,
            ],
            [
                'name' => 'Apple',
                'website' => 'https://www.apple.com',
                'support_phone' => '0800-555-6666',
                'support_email' => 'support@apple.com',
                'comment' => 'MacBooks, iPhones e iPads',
                'is_active' => true,
            ],
            [
                'name' => 'LG Electronics',
                'website' => 'https://www.lg.com',
                'support_phone' => '0800-777-8888',
                'support_email' => 'suporte@lg.com',
                'comment' => 'Monitores e displays profissionais',
                'is_active' => true,
            ],
            [
                'name' => 'Epson',
                'website' => 'https://www.epson.com',
                'support_phone' => '0800-999-0000',
                'support_email' => 'atendimento@epson.com',
                'comment' => 'Impressoras e projetores',
                'is_active' => true,
            ],
        ];

        foreach ($manufacturers as $manufacturer) {
            Manufacturer::create($manufacturer);
        }
    }
}
