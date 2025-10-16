# Laravel Meeting Calendar Package

A beautiful, reusable Livewire meeting calendar component for Laravel 10–12.

## Installation
```bash
composer require shahed/meeting-calendar
```

## Requirements

- PHP ^8.2
- Laravel ^10.0|^11.0|^12.0
- Livewire ^3.0

## Usage

### 1. Create meetings table migration
```bash
php artisan make:migration create_meetings_table
```

Add this to your migration:
```php
Schema::create('meetings', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->dateTime('start_time');
    $table->dateTime('end_time');
    $table->timestamps();
});
```

Run migration:
```bash
php artisan migrate
```

### 2. Add component to your Blade view
```blade
<livewire:meeting-calendar />
```

### 3. Publish views (optional)
```bash
php artisan vendor:publish --tag=meeting-calendar-views
```

## License

MIT License
