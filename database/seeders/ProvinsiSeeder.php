<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Provinsi;

class ProvinsiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->get('https://wilayah.id/api/provinces.json');
            
            if (!$response->successful()) {
                $response = Http::get('http://wilayah.id/api/provinces.json');
            }
            
            if ($response->successful()) {
                $data = $response->json();
                $provinces = $data['data'];

                foreach ($provinces as $province) {
                    Provinsi::updateOrCreate(
                        ['kode' => $province['code']],
                        [
                            'kode' => $province['code'],
                            'nama' => $province['name']
                        ]
                    );
                }

                $this->command->info('Data provinsi berhasil di import: ' . count($provinces) . ' provinsi');
            } else {
                $this->command->error('Gagal mengambil data provinsi. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}