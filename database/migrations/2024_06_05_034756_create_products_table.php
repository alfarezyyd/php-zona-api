<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->nullable(false);
                $table->string('name')->nullable(false);
                $table->text('description')->nullable(false);
                $table->float('price')->nullable(false);
                $table->integer('stock')->nullable(false);
                $table->string('sku')->nullable(false);
                $table->string('produced_by')->nullable(false);
                $table->timestamps();
                $table->softDeletes()->nullable(false);
                $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
                $table->unsignedBigInteger('image_path')->nullable();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('products');
        }
    };
