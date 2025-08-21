<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $defaultCategories = [
        'Lebensmittel',
        'Essen & Trinken',
        'Strom',
        'Abonnements',
        'Kaffee',
        'Einkaufen',
        'Einkommen',
        'Unterhaltung',
        'Nebenkosten',
        'Transport'
        ];

        foreach ($defaultCategories as $name) {
            Category::firstOrCreate([
                'name' => $name
            ]);

        }
    }
}
