<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            [
                'city' => 'Rio de Janeiro',
                'iata_code' => 'GIG',
                'country' => 'Brasil',
            ],
            [
                'city' => 'São Paulo',
                'iata_code' => 'GRU',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Foz do Iguaçu',
                'iata_code' => 'IGU',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Florianópolis',
                'iata_code' => 'FLN',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Salvador',
                'iata_code' => 'SSA',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Brasília',
                'iata_code' => 'BSB',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Manaus',
                'iata_code' => 'MAO',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Belo Horizonte',
                'iata_code' => 'CNF',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Recife',
                'iata_code' => 'REC',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Porto Alegre',
                'iata_code' => 'POA',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Curitiba',
                'iata_code' => 'CWB',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Fortaleza',
                'iata_code' => 'FOR',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Natal',
                'iata_code' => 'NAT',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Maceió',
                'iata_code' => 'MCZ',
                'country' => 'Brasil',
            ],
            [
                'city' => 'Campo Grande',
                'iata_code' => 'CGR',
                'country' => 'Brasil',
            ],
        ];

        // Loop through the array and create a Destination record for each entry.
        foreach ($destinations as $destinationData) {
            Destination::create($destinationData);
        }
    }
}
