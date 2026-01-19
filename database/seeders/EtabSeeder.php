<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EtabSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('etablissements')) {
            $this->command->warn('La table `etablissements` est absente, le seeding des établissements est ignoré.');
            return;
        }

        $disk = Storage::disk('public');
        $entries = $this->etablissementData();

        foreach ($entries as $entry) {
            $existing = DB::table('etablissements')->where('sigle', $entry['sigle'])->first();
            $logoPath = $this->findLogoPath($disk, $entry['sigle']);

            $about = $existing && !empty($existing->about)
                ? $existing->about
                : ($entry['about'] ?? $this->defaultAbout($entry['name']));

            $data = [
                'name' => $entry['name'],
                'type_id' => $entry['type_id'],
                'sigle' => $entry['sigle'],
                'director' => $entry['director'] ?? null,
                'slogan' => $entry['slogan'] ?? null,
                'about' => $about,
                'image_path' => $logoPath ?? ($existing->image_path ?? null),
                'status' => 1,
                'is_doctoral' => $entry['is_doctoral'] ? 1 : 0,
                'uuid' => $existing->uuid ?? (string) Str::uuid(),
                'updated_at' => now(),
            ];

            if (!$existing) {
                $data['created_at'] = now();
            }

            DB::table('etablissements')->updateOrInsert(
                ['sigle' => $entry['sigle']],
                $data
            );
        }
    }

    private function findLogoPath(FilesystemAdapter $disk, string $sigle): ?string
    {
        $files = $disk->files('etabs');
        $target = Str::lower($sigle);

        foreach ($files as $file) {
            if (Str::lower(pathinfo($file, PATHINFO_FILENAME)) === $target) {
                return $file;
            }
        }

        return null;
    }

    private function defaultAbout(string $name): string
    {
        return "Présentation de {$name} au sein de l'Université de Mahajanga.";
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function etablissementData(): array
    {
        return [
            [
                'name' => "Institut d'Odonto-Stomatologie Tropicale de Madagascar",
                'type_id' => 2,
                'sigle' => 'IOSTM',
                'is_doctoral' => false,
            ],
            [
                'name' => 'Institut Universitaire de Gestion et de Management',
                'type_id' => 2,
                'sigle' => 'IUGM',
                'is_doctoral' => false,
            ],
            [
                'name' => 'Institut Supérieur des Sciences et de Technologie',
                'type_id' => 2,
                'sigle' => 'ISSTM',
                'is_doctoral' => false,
            ],
            [
                'name' => 'Institut Universitaire de Technologie et d’Agronomie de Mahajanga',
                'type_id' => 2,
                'sigle' => 'IUTAM',
                'is_doctoral' => false,
            ],
            [
                'name' => 'Institut des Lettres, Civilisations et Sciences Sociales',
                'type_id' => 2,
                'sigle' => 'ILC-SS',
                'is_doctoral' => false,
            ],
            [
                'name' => 'Faculté des Sciences des Technologies et de l’Environnement',
                'type_id' => 1,
                'sigle' => 'FSTE',
                'is_doctoral' => false,
            ],
            [
                'name' => 'Faculté de Médecine',
                'type_id' => 1,
                'sigle' => 'Faculté de Médecine',
                'is_doctoral' => false,
            ],
            [
                'name' => 'École des Arts et Techniques en Prothèses dentaires',
                'type_id' => 3,
                'sigle' => 'EATP',
                'is_doctoral' => false,
            ],
            [
                'name' => 'École de Droit et des Sciences Politiques',
                'type_id' => 3,
                'sigle' => 'EDSP',
                'is_doctoral' => false,
            ],
            [
                'name' => 'École de Tourisme',
                'type_id' => 3,
                'sigle' => 'ET',
                'is_doctoral' => false,
            ],
            [
                'name' => 'École des Langues Commerciales Internationales',
                'type_id' => 3,
                'sigle' => 'ELCI',
                'is_doctoral' => false,
            ],
            [
                'name' => 'École de Vétérinaire',
                'type_id' => 3,
                'sigle' => 'Ecole de Vétérinaire',
                'is_doctoral' => false,
            ],
            [
                'name' => 'Unité de Formation et de Recherche en Sciences Sociales',
                'type_id' => 4,
                'sigle' => 'UFRSS',
                'is_doctoral' => false,
            ],
            [
                'name' => 'École de Pharmacie',
                'type_id' => 3,
                'sigle' => 'Ecole de Pharmacie',
                'is_doctoral' => false,
            ],
            [
                'name' => 'École Normale Supérieure',
                'type_id' => 1,
                'sigle' => 'ENS',
                'is_doctoral' => false,
            ],
            [
                'name' => 'École Doctorale Génie du Vivant et Modélisation',
                'type_id' => 5,
                'sigle' => 'EDGVM',
                'is_doctoral' => true,
            ],
            [
                'name' => 'École Doctorale des Écosystèmes Naturels',
                'type_id' => 5,
                'sigle' => 'EDEN',
                'is_doctoral' => true,
            ],
            [
                'name' => 'École Doctorale Nutrition–Environnement–Santé',
                'type_id' => 5,
                'sigle' => 'EDNES',
                'is_doctoral' => true,
            ],
        ];
    }
}
