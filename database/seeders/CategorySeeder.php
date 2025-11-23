<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categoria: ANCESTRY
        $ancestry = Category::firstOrCreate(
            ['slug' => 'ancestry'],
            [
                'name' => 'Ancestry',
                'slug' => 'ancestry',
                'parent_id' => null,
            ]
        );

        // Subcategorias de Ancestry
        Category::firstOrCreate(
            ['slug' => 'rape-indigena'],
            [
                'name' => 'Rapé indígena',
                'slug' => 'rape-indigena',
                'parent_id' => $ancestry->id,
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'artesanatos-indigenas'],
            [
                'name' => 'Artesanatos indígenas',
                'slug' => 'artesanatos-indigenas',
                'parent_id' => $ancestry->id,
            ]
        );

        // Categoria: HEMPWEAR
        $hempwear = Category::firstOrCreate(
            ['slug' => 'hempwear'],
            [
                'name' => 'Hempwear',
                'slug' => 'hempwear',
                'parent_id' => null,
            ]
        );

        // Subcategorias de Hempwear
        Category::firstOrCreate(
            ['slug' => 'roupas'],
            [
                'name' => 'Roupas',
                'slug' => 'roupas',
                'parent_id' => $hempwear->id,
            ]
        );

        Category::firstOrCreate(
            ['slug' => 'acessorios'],
            [
                'name' => 'Acessórios',
                'slug' => 'acessorios',
                'parent_id' => $hempwear->id,
            ]
        );

        $this->command->info('✅ Categorias criadas com sucesso!');
        $this->command->info('   - Ancestry (com 2 subcategorias)');
        $this->command->info('   - Hempwear (com 2 subcategorias)');
    }
}

