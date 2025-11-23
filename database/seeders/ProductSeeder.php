<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar categorias
        $rapeIndigena = Category::where('slug', 'rape-indigena')->first();
        $artesanatosIndigenas = Category::where('slug', 'artesanatos-indigenas')->first();
        $roupas = Category::where('slug', 'roupas')->first();
        $acessorios = Category::where('slug', 'acessorios')->first();
        $vitrola = Category::where('slug', 'vitrola')->orWhere('name', 'Vitrola')->first();

        // ============================================
        // PRODUTOS - RAPÉ INDÍGENA (3 produtos)
        // ============================================
        if ($rapeIndigena) {
            Product::firstOrCreate(
                ['slug' => 'rape-indigena-tradicional'],
                [
                    'category_id' => $rapeIndigena->id,
                    'name' => 'Rapé Indígena Tradicional',
                    'slug' => 'rape-indigena-tradicional',
                    'sku' => 'RAPE-001',
                    'short_description' => 'Rapé indígena tradicional, feito com ervas sagradas da floresta.',
                    'long_description' => 'Rapé indígena tradicional produzido com ervas sagradas colhidas na floresta amazônica. Preparado seguindo métodos ancestrais, este rapé é utilizado em rituais e cerimônias tradicionais. Produto autêntico e de alta qualidade.',
                    'price' => 89.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 50,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'rape-indigena-premium'],
                [
                    'category_id' => $rapeIndigena->id,
                    'name' => 'Rapé Indígena Premium',
                    'slug' => 'rape-indigena-premium',
                    'sku' => 'RAPE-002',
                    'short_description' => 'Rapé indígena premium com blend especial de ervas medicinais.',
                    'long_description' => 'Rapé indígena premium elaborado com uma seleção especial de ervas medicinais. Combinação única de plantas sagradas que proporciona uma experiência autêntica e poderosa. Embalagem especial preservando a qualidade do produto.',
                    'price' => 129.90,
                    'sale_price' => 109.90,
                    'is_active' => true,
                    'quantity' => 30,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'rape-indigena-ceremonial'],
                [
                    'category_id' => $rapeIndigena->id,
                    'name' => 'Rapé Indígena Cerimonial',
                    'slug' => 'rape-indigena-ceremonial',
                    'sku' => 'RAPE-003',
                    'short_description' => 'Rapé indígena cerimonial para rituais e práticas espirituais.',
                    'long_description' => 'Rapé indígena cerimonial especialmente preparado para rituais e práticas espirituais. Feito com as melhores ervas sagradas e preparado por mestres tradicionais. Ideal para cerimônias e conexão espiritual.',
                    'price' => 159.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 25,
                ]
            );
        }

        // ============================================
        // PRODUTOS - ARTESANATOS INDÍGENAS (3 produtos)
        // ============================================
        if ($artesanatosIndigenas) {
            Product::firstOrCreate(
                ['slug' => 'cesto-indigena-artesanal'],
                [
                    'category_id' => $artesanatosIndigenas->id,
                    'name' => 'Cesto Indígena Artesanal',
                    'slug' => 'cesto-indigena-artesanal',
                    'sku' => 'ART-001',
                    'short_description' => 'Cesto indígena artesanal feito à mão com fibras naturais.',
                    'long_description' => 'Cesto indígena artesanal confeccionado manualmente por artesãos indígenas usando técnicas tradicionais. Feito com fibras naturais da floresta, este cesto é funcional e decorativo, representando a rica cultura indígena brasileira.',
                    'price' => 149.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 15,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'colar-indigena-artesanal'],
                [
                    'category_id' => $artesanatosIndigenas->id,
                    'name' => 'Colar Indígena Artesanal',
                    'slug' => 'colar-indigena-artesanal',
                    'sku' => 'ART-002',
                    'short_description' => 'Colar indígena artesanal com sementes e elementos naturais.',
                    'long_description' => 'Colar indígena artesanal confeccionado com sementes naturais, penas e elementos da floresta. Cada peça é única e feita à mão por artesãos indígenas, preservando técnicas ancestrais de confecção.',
                    'price' => 79.90,
                    'sale_price' => 69.90,
                    'is_active' => true,
                    'quantity' => 20,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'peneira-indigena-artesanal'],
                [
                    'category_id' => $artesanatosIndigenas->id,
                    'name' => 'Peneira Indígena Artesanal',
                    'slug' => 'peneira-indigena-artesanal',
                    'sku' => 'ART-003',
                    'short_description' => 'Peneira indígena artesanal feita com cipó e fibras naturais.',
                    'long_description' => 'Peneira indígena artesanal confeccionada manualmente com cipó e fibras naturais. Utensílio tradicional usado para peneirar farinha e outros alimentos. Peça funcional e decorativa que representa a sabedoria indígena.',
                    'price' => 99.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 12,
                ]
            );
        }

        // ============================================
        // PRODUTOS - ROUPAS (3 produtos)
        // ============================================
        if ($roupas) {
            Product::firstOrCreate(
                ['slug' => 'camiseta-hempwear-ecologica'],
                [
                    'category_id' => $roupas->id,
                    'name' => 'Camiseta Hempwear Ecológica',
                    'slug' => 'camiseta-hempwear-ecologica',
                    'sku' => 'HEMP-ROUPA-001',
                    'short_description' => 'Camiseta ecológica feita com fibra de cânhamo, sustentável e confortável.',
                    'long_description' => 'Camiseta ecológica produzida com fibra de cânhamo, uma das fibras mais sustentáveis do planeta. Confortável, respirável e durável. Ideal para quem busca moda consciente e respeito ao meio ambiente. Disponível em vários tamanhos.',
                    'price' => 129.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 50,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'calca-hempwear-ecologica'],
                [
                    'category_id' => $roupas->id,
                    'name' => 'Calça Hempwear Ecológica',
                    'slug' => 'calca-hempwear-ecologica',
                    'sku' => 'HEMP-ROUPA-002',
                    'short_description' => 'Calça ecológica de cânhamo, resistente e sustentável.',
                    'long_description' => 'Calça ecológica confeccionada com tecido de cânhamo, conhecido por sua durabilidade e sustentabilidade. Confortável para o dia a dia, resistente e com design moderno. Perfeita para quem valoriza moda sustentável.',
                    'price' => 249.90,
                    'sale_price' => 219.90,
                    'is_active' => true,
                    'quantity' => 30,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'casaco-hempwear-ecologico'],
                [
                    'category_id' => $roupas->id,
                    'name' => 'Casaco Hempwear Ecológico',
                    'slug' => 'casaco-hempwear-ecologico',
                    'sku' => 'HEMP-ROUPA-003',
                    'short_description' => 'Casaco ecológico de cânhamo, quente e sustentável.',
                    'long_description' => 'Casaco ecológico produzido com fibra de cânhamo, oferecendo aquecimento natural e sustentabilidade. Design versátil que combina estilo e consciência ambiental. Ideal para climas mais frios, mantendo você aquecido de forma natural.',
                    'price' => 349.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 20,
                ]
            );
        }

        // ============================================
        // PRODUTOS - ACESSÓRIOS (3 produtos)
        // ============================================
        if ($acessorios) {
            Product::firstOrCreate(
                ['slug' => 'bolsa-hempwear-ecologica'],
                [
                    'category_id' => $acessorios->id,
                    'name' => 'Bolsa Hempwear Ecológica',
                    'slug' => 'bolsa-hempwear-ecologica',
                    'sku' => 'HEMP-ACC-001',
                    'short_description' => 'Bolsa ecológica de cânhamo, resistente e estilosa.',
                    'long_description' => 'Bolsa ecológica confeccionada com tecido de cânhamo, conhecido por sua resistência e durabilidade. Design moderno e funcional, perfeita para o dia a dia. Ideal para quem busca acessórios sustentáveis e de qualidade.',
                    'price' => 179.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 25,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'mochila-hempwear-ecologica'],
                [
                    'category_id' => $acessorios->id,
                    'name' => 'Mochila Hempwear Ecológica',
                    'slug' => 'mochila-hempwear-ecologica',
                    'sku' => 'HEMP-ACC-002',
                    'short_description' => 'Mochila ecológica de cânhamo, espaçosa e resistente.',
                    'long_description' => 'Mochila ecológica produzida com tecido de cânhamo, oferecendo resistência e durabilidade excepcionais. Espaçosa e funcional, perfeita para uso diário, viagens ou atividades ao ar livre. Compartimentos organizados e design ergonômico.',
                    'price' => 299.90,
                    'sale_price' => 269.90,
                    'is_active' => true,
                    'quantity' => 18,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'boné-hempwear-ecologico'],
                [
                    'category_id' => $acessorios->id,
                    'name' => 'Boné Hempwear Ecológico',
                    'slug' => 'boné-hempwear-ecologico',
                    'sku' => 'HEMP-ACC-003',
                    'short_description' => 'Boné ecológico de cânhamo, confortável e sustentável.',
                    'long_description' => 'Boné ecológico confeccionado com tecido de cânhamo, oferecendo proteção solar e estilo. Confortável, respirável e durável. Perfeito para uso no dia a dia, esportes ou atividades ao ar livre. Design moderno e sustentável.',
                    'price' => 89.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 40,
                ]
            );
        }

        // ============================================
        // PRODUTOS - VITROLA (3 produtos - usando Seda como exemplo)
        // ============================================
        if ($vitrola) {
            Product::firstOrCreate(
                ['slug' => 'seda-para-vitrola'],
                [
                    'category_id' => $vitrola->id,
                    'name' => 'Seda para Vitrola',
                    'slug' => 'seda-para-vitrola',
                    'sku' => 'VIT-001',
                    'short_description' => 'Seda de alta qualidade para vitrola, ideal para preservar seus discos.',
                    'long_description' => 'Seda de alta qualidade especialmente desenvolvida para vitrolas. Protege seus discos de vinil contra poeira, estática e arranhões. Material premium que garante a preservação e qualidade do som dos seus discos favoritos.',
                    'price' => 29.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 100,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'agulha-vitrola-premium'],
                [
                    'category_id' => $vitrola->id,
                    'name' => 'Agulha para Vitrola Premium',
                    'slug' => 'agulha-vitrola-premium',
                    'sku' => 'VIT-002',
                    'short_description' => 'Agulha premium para vitrola, proporciona som cristalino e preserva seus discos.',
                    'long_description' => 'Agulha premium de alta qualidade para vitrolas. Proporciona som cristalino e preserva seus discos de vinil, reduzindo o desgaste. Tecnologia avançada que garante a melhor experiência de audição e durabilidade dos seus discos.',
                    'price' => 149.90,
                    'sale_price' => 129.90,
                    'is_active' => true,
                    'quantity' => 30,
                ]
            );

            Product::firstOrCreate(
                ['slug' => 'limpa-discos-vitrola'],
                [
                    'category_id' => $vitrola->id,
                    'name' => 'Kit Limpa Discos para Vitrola',
                    'slug' => 'limpa-discos-vitrola',
                    'sku' => 'VIT-003',
                    'short_description' => 'Kit completo para limpeza de discos de vinil, mantendo seus discos sempre limpos.',
                    'long_description' => 'Kit completo para limpeza de discos de vinil. Inclui solução de limpeza especializada, pano macio e escova antiestática. Mantém seus discos sempre limpos, preservando a qualidade do som e prolongando a vida útil dos seus vinis.',
                    'price' => 79.90,
                    'sale_price' => null,
                    'is_active' => true,
                    'quantity' => 45,
                ]
            );
        }

        $this->command->info('✅ Produtos criados com sucesso!');
        $this->command->info('   - 3 produtos em Rapé indígena');
        $this->command->info('   - 3 produtos em Artesanatos indígenas');
        $this->command->info('   - 3 produtos em Roupas');
        $this->command->info('   - 3 produtos em Acessórios');
        $this->command->info('   - 3 produtos em Vitrola');
        $this->command->info('');
        $this->command->info('📸 Lembre-se de adicionar as fotos pelo painel admin!');
    }
}

