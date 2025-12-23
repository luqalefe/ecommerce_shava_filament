<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, video, color
            $table->string('group')->default('general'); // general, hero, branding
            $table->string('label')->nullable(); // Label amigável para exibição
            $table->timestamps();
        });

        // Inserir configurações padrão
        $defaults = [
            // Branding
            ['key' => 'logo', 'value' => 'images/logo_shava.png', 'type' => 'image', 'group' => 'branding', 'label' => 'Logo Principal'],
            ['key' => 'logo_footer', 'value' => 'images/logo_shava.png', 'type' => 'image', 'group' => 'branding', 'label' => 'Logo Footer'],
            ['key' => 'favicon', 'value' => 'favicon.ico', 'type' => 'image', 'group' => 'branding', 'label' => 'Favicon'],
            
            // Hero Videos
            ['key' => 'hero_video_desktop', 'value' => 'images/coleção_miraçoes.webm', 'type' => 'video', 'group' => 'hero', 'label' => 'Vídeo Hero Desktop'],
            ['key' => 'hero_video_mobile', 'value' => 'images/coleçãomiraçõesmobile.webm', 'type' => 'video', 'group' => 'hero', 'label' => 'Vídeo Hero Mobile'],
        ];

        foreach ($defaults as $setting) {
            \DB::table('site_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
