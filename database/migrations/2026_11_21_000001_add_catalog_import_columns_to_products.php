<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns needed by the full item catalogue (Excel import):
 *  - stock becomes optional: NULL = not tracked (always orderable)
 *  - unit (metre, roll, bundle… NULL = per piece) and tax rate
 *  - search_text: lower-cased names/codes so the storefront search does not scan JSON
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->nullable()->default(null)->change();
            $table->json('unit')->nullable()->after('short_description');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('price');
            $table->text('search_text')->nullable()->after('sort_order');
            $table->index(['is_active', 'category_id']);
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'category_id']);
            $table->dropIndex(['price']);
            $table->dropColumn(['unit', 'tax_rate', 'search_text']);
            $table->unsignedInteger('stock')->default(0)->nullable(false)->change();
        });
    }
};
