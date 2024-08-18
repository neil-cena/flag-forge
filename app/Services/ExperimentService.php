<?php

namespace App\Services;

use App\Models\Experiment;

class ExperimentService
{
    public function assignVariant(Experiment $experiment, string $userIdentifier): string
    {
        $variants = $experiment->variants ?? ['control'];
        $allocation = $experiment->allocation ?? [];
        $bucket = $this->bucket($experiment->id, $userIdentifier);

        $cursor = 0;
        foreach ($variants as $variant) {
            $weight = (int) ($allocation[$variant] ?? 0);
            $cursor += $weight;
            if ($bucket < $cursor) {
                return $variant;
            }
        }

        return (string) ($variants[0] ?? 'control');
    }

    protected function bucket(int $experimentId, string $userIdentifier): int
    {
        $hex = substr(hash('sha256', $experimentId.'|'.$userIdentifier), 0, 8);

        return hexdec($hex) % 100;
    }
}
