<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\AgencyRoute;
use App\Models\Flight;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencies = [
            [
                'id' => 'moroni-express',
                'name' => 'Moroni Express',
                'address' => 'Parcelles Assainies, Dakar',
                'opening_hours' => 'Lun–Sam, 9h–19h',
                'whatsapp' => '221770000001',
                'description' => 'Spécialiste des envois Dakar–Comores depuis plus de 8 ans.',
                'routes' => [['SN', 'KM'], ['KM', 'SN']],
                'flights' => [
                    ['SN', 'KM', 3, 'Air Sénégal', 42, 120, '2500'],
                    ['SN', 'KM', 10, 'Air Sénégal', 95, 120, '2500'],
                    ['SN', 'KM', 17, 'Ethiopian', 8, 100, '2700'],
                    ['KM', 'SN', 6, 'Ethiopian', 60, 100, '3000'],
                    ['KM', 'SN', 20, 'Air Sénégal', 0, 100, '3000'],
                ],
            ],
            [
                'id' => 'baobab-cargo',
                'name' => 'Baobab Cargo',
                'address' => 'Liberté 6, Dakar',
                'opening_hours' => 'Lun–Ven, 8h30–18h',
                'whatsapp' => '221770000002',
                'description' => 'Colis et cartons vers la France et les Comores, avec ramassage à Dakar.',
                'routes' => [['SN', 'FR'], ['FR', 'SN'], ['SN', 'KM']],
                'flights' => [
                    ['SN', 'FR', 2, 'Air France', 18, 80, '4000'],
                    ['SN', 'FR', 9, 'Air Sénégal', 70, 80, '3800'],
                    ['FR', 'SN', 5, 'Air France', 25, 60, '8'],
                    ['FR', 'SN', 16, 'Air Sénégal', 50, 60, '8'],
                    ['SN', 'KM', 7, 'Ethiopian', 30, 100, '2600'],
                ],
            ],
            [
                'id' => 'karthala-colis',
                'name' => 'Karthala Colis',
                'address' => 'Médina, Dakar · Volo-Volo, Moroni',
                'opening_hours' => 'Lun–Sam, 9h–18h',
                'whatsapp' => '221770000003',
                'description' => 'Agence présente à Dakar et à Moroni, tous trajets entre les trois pays.',
                'routes' => [['KM', 'SN'], ['SN', 'KM'], ['KM', 'FR'], ['FR', 'KM']],
                'flights' => [
                    ['KM', 'FR', 4, 'Air Austral', 55, 90, '10'],
                    ['KM', 'FR', 18, 'Air Austral', 85, 90, '10'],
                    ['FR', 'KM', 8, 'Air Austral', 12, 90, '9'],
                    ['SN', 'KM', 12, 'Ethiopian', 40, 100, '2600'],
                    ['KM', 'SN', 14, 'Ethiopian', 75, 100, '3000'],
                ],
            ],
            [
                'id' => 'ylang-fret',
                'name' => 'Ylang Fret',
                'address' => 'Gare du Nord, Paris',
                'opening_hours' => 'Mar–Sam, 10h–19h',
                'whatsapp' => '33600000004',
                'description' => 'Envois depuis Paris vers Moroni et Dakar, dépôt le week-end possible.',
                'routes' => [['FR', 'KM'], ['KM', 'FR'], ['FR', 'SN']],
                'flights' => [
                    ['FR', 'KM', 6, 'Air Austral', 33, 70, '9'],
                    ['FR', 'KM', 20, 'Air Austral', 64, 70, '9'],
                    ['FR', 'SN', 11, 'Air France', 22, 50, '8'],
                    ['KM', 'FR', 13, 'Air Austral', 0, 70, '10'],
                ],
            ],
            [
                'id' => 'teranga-envois',
                'name' => 'Teranga Envois',
                'address' => 'Sacré-Cœur, Dakar',
                'opening_hours' => 'Lun–Sam, 9h–20h',
                'whatsapp' => '221770000005',
                'description' => 'Envois rapides Sénégal–France, suivi du colis par WhatsApp.',
                'routes' => [['SN', 'FR'], ['FR', 'SN']],
                'flights' => [
                    ['SN', 'FR', 1, 'Air Sénégal', 5, 60, '3900'],
                    ['SN', 'FR', 8, 'Air France', 48, 60, '4000'],
                    ['FR', 'SN', 4, 'Air Sénégal', 30, 60, '8'],
                    ['FR', 'SN', 15, 'Air France', 44, 60, '8'],
                ],
            ],
        ];

        foreach ($agencies as $data) {
            $agency = Agency::create([
                'name' => $data['name'],
                'slug' => $data['id'],
                'description' => $data['description'],
                'address' => $data['address'],
                'opening_hours' => $data['opening_hours'],
                'whatsapp' => $data['whatsapp'],
                'status' => 'approved',
            ]);

            foreach ($data['routes'] as $route) {
                AgencyRoute::create([
                    'agency_id' => $agency->id,
                    'from_country' => $route[0],
                    'to_country' => $route[1],
                ]);
            }

            foreach ($data['flights'] as $flight) {
                $flightDate = now()->addDays($flight[2]);
                $currency = in_array($flight[0], ['FR']) || in_array($flight[1], ['FR']) ? 'EUR' : 'FCFA';
                $status = $flight[4] === 0 ? 'full' : 'open';

                Flight::create([
                    'agency_id' => $agency->id,
                    'from_country' => $flight[0],
                    'to_country' => $flight[1],
                    'flight_date' => $flightDate,
                    'airline' => $flight[3],
                    'total_kg' => $flight[5],
                    'remaining_kg' => $flight[4],
                    'price_per_kg' => $flight[6],
                    'currency' => $currency,
                    'drop_off_deadline' => $flightDate->copy()->subDay(),
                    'status' => $status,
                ]);
            }
        }
    }
}
