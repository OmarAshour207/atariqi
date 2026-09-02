<?php

namespace App\Services;

use App\Models\Social;
use Illuminate\Support\Facades\DB;

class ContactSettingsService
{
    public function mergedSettings(): array
    {
        $contacts = DB::table('contact')
            ->get()
            ->keyBy(fn ($row) => strtolower((string) $row->{'paltform-name'}));

        $social = Social::query()->first();

        return [
            'title' => setting('title'),
            'description' => setting('description'),
            'phonenumber' => setting('phonenumber')
                ?: ($contacts->get('whatsup')?->{'link-title'} ?? null),
            'email' => setting('email')
                ?: ($contacts->get('email')?->{'link-title'} ?? null),
            'address' => setting('address')
                ?: ($contacts->get('location')?->{'link-title'} ?? null),
            'linkedin' => setting('linkedin') ?: ($social?->linkedin ?? null),
            'instagram' => setting('instagram')
                ?: ($social?->instagram ?? $contacts->get('instagram')?->link ?? null),
            'twitter' => setting('twitter')
                ?: ($social?->twitter ?? $contacts->get('twitter')?->link ?? null),
            'google_play' => setting('google_play'),
            'app_store' => setting('app_store'),
        ];
    }

    public function sync(array $data): void
    {
        $now = now();

        if (array_key_exists('email', $data)) {
            $this->updateContactPlatform('Email', [
                'link-title' => (string) ($data['email'] ?? ''),
                'link' => filled($data['email'] ?? null) ? 'mailto:' . $data['email'] : '',
                'date-of-edit' => $now,
            ]);
        }

        if (array_key_exists('phonenumber', $data)) {
            $phone = (string) ($data['phonenumber'] ?? '');
            $digits = preg_replace('/\D/', '', $phone) ?? '';

            $this->updateContactPlatform('Whatsup', [
                'link-title' => $phone,
                'link' => $digits !== ''
                    ? 'https://api.whatsapp.com/send?phone=' . $digits
                    : '',
                'date-of-edit' => $now,
            ]);
        }

        if (array_key_exists('address', $data)) {
            $existing = DB::table('contact')->where('paltform-name', 'Location')->first();

            $this->updateContactPlatform('Location', [
                'link-title' => (string) ($data['address'] ?? ''),
                'link' => $existing?->link ?? '',
                'date-of-edit' => $now,
            ]);
        }

        if (array_key_exists('instagram', $data)) {
            $this->updateContactPlatform('Instagram', [
                'link-title' => $this->linkTitleFromUrl((string) ($data['instagram'] ?? '')),
                'link' => (string) ($data['instagram'] ?? ''),
                'date-of-edit' => $now,
            ]);
        }

        if (array_key_exists('twitter', $data)) {
            $this->updateContactPlatform('Twitter', [
                'link-title' => $this->linkTitleFromUrl((string) ($data['twitter'] ?? '')),
                'link' => (string) ($data['twitter'] ?? ''),
                'date-of-edit' => $now,
            ]);
        }

        $social = Social::query()->first();

        $socialPayload = [
            'linkedin' => $data['linkedin'] ?? $social?->linkedin,
            'instagram' => $data['instagram'] ?? $social?->instagram,
            'twitter' => $data['twitter'] ?? $social?->twitter,
            'whatsapp' => $data['phonenumber'] ?? $social?->whatsapp,
        ];

        if ($social) {
            $social->update($socialPayload);
        } else {
            Social::create($socialPayload);
        }
    }

    private function updateContactPlatform(string $platform, array $values): void
    {
        $updated = DB::table('contact')
            ->where('paltform-name', $platform)
            ->update($values);

        if ($updated === 0 && ($values['link-title'] !== '' || $values['link'] !== '')) {
            DB::table('contact')->insert(array_merge([
                'paltform-name' => $platform,
                'date-of-add' => now(),
            ], $values));
        }
    }

    private function linkTitleFromUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return '';
        }

        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($path === '') {
            return $url;
        }

        $segments = explode('/', $path);

        return (string) end($segments);
    }
}
