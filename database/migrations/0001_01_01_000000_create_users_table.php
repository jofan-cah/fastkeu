<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users (extend default)
        Schema::create('users', function (Blueprint $table) {
            $table->string('user_id')->primary();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Password reset tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Features (untuk permissions)
        Schema::create('features', function (Blueprint $table) {
            $table->id('feature_id');
            $table->string('feature_name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Permissions (role + feature + CRUD)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->foreignId('role_id')->constrained('roles', 'role_id')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('features', 'feature_id')->onDelete('cascade');
            $table->boolean('can_create')->default(false);
            $table->boolean('can_read')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();

            $table->unique(['role_id', 'feature_id']);
        });

        // User Roles (many-to-many)
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->foreignId('role_id')->constrained('roles', 'role_id')->onDelete('cascade');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('features');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
