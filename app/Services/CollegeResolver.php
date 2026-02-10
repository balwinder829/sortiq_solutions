<?php

namespace App\Services;

use App\Models\College;
use App\Models\State;
use App\Models\District;
use Illuminate\Support\Str;

class CollegeResolver
{
    /**
     * Main entry point
     */
    public function resolve(string $input): College
    {
        $raw = trim($input);

        [$collegeNameRaw, $state, $district] = $this->explodeAndResolve($raw);

        $cleanName = $this->normalizeText($collegeNameRaw);
        $shortName = $this->generateShortName($cleanName);
// dd($cleanName);
        $stateId    = $state?->id;
        $districtId = $district?->id;

        // Find existing
        $query = College::where('clean_name', $cleanName);

        if ($stateId !== null) {
            $query->where('state_id', $stateId);
        }

        if ($districtId !== null) {
            $query->where('district_id', $districtId);
        }

        $college = $query->first();

        if (!$college) {
            $college = College::create([
                'college_name'         => $collegeNameRaw,
                'college_display_name' => $collegeNameRaw,
                'college_short_name'   => $shortName,
                'clean_name'           => $cleanName,
                'slug'                 => Str::slug($collegeNameRaw),
                'state_id'             => $stateId,
                'district_id'          => $districtId,
            ]);
        }

        return $college;
    }

    /**
     * Explode-based parsing
     */
    private function explodeAndResolve(string $raw): array
    {
        $parts = array_values(array_filter(
            array_map('trim', explode(',', $raw))
        ));

        if (count($parts) < 2) {
            return [$raw, null, null];
        }

        $stateText    = $parts[count($parts) - 1];
        $districtText = $parts[count($parts) - 2];
        $collegeParts = array_slice($parts, 0, -2);

        $collegeNameRaw = implode(', ', $collegeParts);

        $state = $this->resolveState(
            $this->normalizeText($stateText)
        );

        $district = null;

        if ($state) {
            $district = $this->resolveDistrict(
                $this->normalizeText($districtText),
                $state->id
            );
        }

        if (!$district) {
            $district = $this->resolveDistrictFromText(
                $this->normalizeText($districtText)
            );

            if (!$state && $district) {
                $state = State::find($district->state_id);
            }
        }

        return [$collegeNameRaw, $state, $district];
    }

    /**
     * Text normalization (SINGLE SOURCE OF TRUTH)
     */
    private function normalizeText(string $text): string
    {
        $text = strtolower(trim($text));

        // normalize connectors
        $text = str_replace(['&', ' and '], ' and ', ' ' . $text . ' ');

        // remove punctuation
        $text = preg_replace('/[^\w\s]/u', ' ', $text);

        // normalize spaces
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * State resolver (aliases supported)
     */
    private function resolveState(string $text): ?State
    {
        $aliases = [
            'hp' => 'Himachal Pradesh',
            'himachal' => 'Himachal Pradesh',
            'himachal pradesh' => 'Himachal Pradesh',
            'himachal pardesh' => 'Himachal Pradesh',

            'hr' => 'Haryana',
            'haryana' => 'Haryana',

            'pb' => 'Punjab',
            'punjab' => 'Punjab',
        ];

        foreach ($aliases as $alias => $stateName) {
            if (str_contains($text, $alias)) {
                return State::firstOrCreate(['name' => $stateName]);
            }
        }

        return null;
    }

    /**
     * District by state
     */
    private function resolveDistrict(string $text, int $stateId): ?District
    {
        return District::where('state_id', $stateId)
            ->get()
            ->first(fn ($d) =>
                str_contains($text, $this->normalizeText($d->name))
            );
    }

    /**
     * District without state
     */
    private function resolveDistrictFromText(string $text): ?District
    {
        return District::all()
            ->first(fn ($d) =>
                str_contains($text, $this->normalizeText($d->name))
            );
    }

    public function resolveWithLocation(
        string $collegeName,
        int $stateId,
        int $districtId,
        ?string $displayName = null
    ): College {

        $cleanName = $this->normalizeText($collegeName);
        $shortName = $this->generateShortName($cleanName);
        // dd($cleanName);
        return College::firstOrCreate(
            [
                'clean_name'  => $cleanName,
                'state_id'    => $stateId,
                'district_id' => $districtId,
            ],
            [
                // canonical name (system)
                'college_name'         => $collegeName,
                'college_short_name'   => $shortName,

                // EXACT user input for display
                'college_display_name' => $displayName ?? $collegeName,

                'slug'                 => \Str::slug($collegeName),
            ]
        );
    }

    public function makeCleanName(string $collegeName): string
    {
        
        return $this->normalizeText($collegeName);
    }

    public function makeSlug(string $collegeName): string
    {
        return \Str::slug($collegeName);
    }

    public function resolveForImport(string $input): array
    {
        $raw = trim($input);

        [$collegeNameRaw, $state, $district] = $this->explodeAndResolve($raw);

        $cleanName  = $this->normalizeText($collegeNameRaw);
        $stateId    = $state?->id;
        $districtId = $district?->id;

        $query = College::where('clean_name', $cleanName);

        if ($stateId !== null) {
            $query->where('state_id', $stateId);
        }

        if ($districtId !== null) {
            $query->where('district_id', $districtId);
        }

        $college = $query->first();

        if ($college) {
            return [
                'college' => $college,
                'status'  => 'skipped', // duplicate
            ];
        }

        $shortName = $this->generateShortName($cleanName);
        $college = College::create([
            'college_name'         => $collegeNameRaw,
            'college_display_name' => $collegeNameRaw,
            'college_short_name'   => $shortName,
            'clean_name'           => $cleanName,
            'slug'                 => Str::slug($collegeNameRaw),
            'state_id'             => $stateId,
            'district_id'          => $districtId,
        ]);

        return [
            'college' => $college,
            'status'  => 'created',
        ];
    }

    protected function generateShortName(string $name): string
    {
        // Remove special characters
        $name = preg_replace('/[^a-zA-Z\s]/', '', $name);

        // Split into words
        $words = preg_split('/\s+/', trim($name));

        // Take first letter of each word
        $initials = array_map(fn($w) => strtoupper($w[0]), $words);

        return implode('', $initials);
    }



}
