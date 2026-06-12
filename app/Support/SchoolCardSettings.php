<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SchoolCardSettings
{
    public static function all(?int $userId = null): array
    {
        $defaults = self::defaults();
        $path = self::path($userId);

        if (!File::exists($path)) {
            return $defaults;
        }

        $stored = json_decode((string) File::get($path), true);

        return array_replace_recursive($defaults, is_array($stored) ? $stored : []);
    }

    public static function save(array $settings, ?int $userId = null): void
    {
        File::ensureDirectoryExists(dirname(self::path($userId)));
        File::put(self::path($userId), json_encode(array_replace_recursive(self::all($userId), $settings), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function storeFile(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    public static function publicUrl(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }

    public static function defaults(): array
    {
        return [
            'official' => [
                'ministry' => "MINISTERE DE L'EDUCATION NATIONALE",
                'academy' => '',
                'cap' => '',
                'school_name' => '',
                'school_type' => '',
                'academic_year' => now()->format('Y') . '-' . now()->addYear()->format('Y'),
            ],
            'display' => [
                'header_fields' => [
                    'ministry',
                    'academy',
                    'cap',
                    'institution',
                    'class_name',
                ],
                'student_fields' => [
                    'full_name',
                    'matricule',
                    'gender',
                    'birth_date',
                    'birth_place',
                    'academic_year',
                ],
                'custom_lines' => [],
            ],
            'signature_path' => null,
            'card' => [
                'default_template' => 'classic',
                'primary_color' => '#12325f',
                'secondary_color' => '#d4af37',
                'background_color' => '#f8fafc',
                'background_image_path' => null,
                'decorative_image_path' => null,
            ],
        ];
    }

    private static function path(?int $userId = null): string
    {
        $owner = $userId ?: (auth()->id() ?: 'global');

        return storage_path("app/school-card-settings/user-{$owner}.json");
    }
}
