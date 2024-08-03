<?php

namespace App\Services;

use App\Models\FeatureFlag;
use App\Models\Project;
use App\Models\TargetingRule;

class FlagEvaluationService
{
    public function evaluate(Project $project, array $context = []): array
    {
        $identifier = (string) ($context['user_identifier'] ?? $context['userId'] ?? 'anonymous');
        $country = strtoupper((string) ($context['country'] ?? ''));

        $flags = $project->featureFlags()->with(['targetingRules', 'experiments'])->get();

        return $flags->mapWithKeys(function (FeatureFlag $flag) use ($context, $identifier, $country) {
            $match = $this->evaluateFlag($flag, $context, $identifier, $country);

            return [$flag->flag_key => $match];
        })->all();
    }

    protected function evaluateFlag(FeatureFlag $flag, array $context, string $identifier, string $country): array
    {
        if (! $flag->is_enabled) {
            return ['enabled' => false, 'reason' => 'flag_disabled'];
        }

        foreach ($flag->targetingRules as $rule) {
            if (! $rule->is_enabled) {
                continue;
            }

            if ($this->matchesRule($rule, $context, $country)) {
                return ['enabled' => true, 'reason' => "rule:{$rule->name}"];
            }
        }

        $bucket = $this->bucket($identifier, $flag->flag_key);
        $rolloutEnabled = $bucket < $flag->rollout_percentage;

        return [
            'enabled' => $rolloutEnabled,
            'reason' => $rolloutEnabled ? 'percentage_rollout' : 'rollout_miss',
            'bucket' => $bucket,
            'rollout' => $flag->rollout_percentage,
        ];
    }

    protected function matchesRule(TargetingRule $rule, array $context, string $country): bool
    {
        $attribute = $rule->attribute;
        $values = $rule->values ?? [];
        $input = $attribute === 'country' ? $country : ($context[$attribute] ?? null);

        // TODO: add regex operator for attribute matching
        return match ($rule->operator) {
            'eq' => $input === ($values[0] ?? null),
            'neq' => $input !== ($values[0] ?? null),
            'in' => in_array($input, $values, true),
            'not_in' => ! in_array($input, $values, true),
            'contains' => is_array($input) && count(array_intersect($values, $input)) > 0,
            default => false,
        };
    }

    protected function bucket(string $identifier, string $key): int
    {
        $hex = substr(hash('sha256', $identifier.'|'.$key), 0, 8);

        return hexdec($hex) % 100;
    }
}
