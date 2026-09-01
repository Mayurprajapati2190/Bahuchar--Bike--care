<?php

namespace App\Models\Concerns;

use App\Models\Customer;
use App\Models\Scopes\TeamScope;
use App\Models\ServiceRecord;
use App\Models\Team;
use App\Support\CurrentTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTeam
{
    public static function bootBelongsToTeam(): void
    {
        static::addGlobalScope(new TeamScope);

        static::creating(function (Model $model): void {
            if (filled($model->getAttribute('team_id'))) {
                return;
            }

            if (filled($model->getAttribute('customer_id'))) {
                $fromCustomer = Customer::withoutGlobalScopes()
                    ->whereKey($model->getAttribute('customer_id'))
                    ->value('team_id');

                if ($fromCustomer) {
                    $model->setAttribute('team_id', $fromCustomer);

                    return;
                }
            }

            if (filled($model->getAttribute('service_record_id'))) {
                $fromService = ServiceRecord::withoutGlobalScopes()
                    ->whereKey($model->getAttribute('service_record_id'))
                    ->value('team_id');

                if ($fromService) {
                    $model->setAttribute('team_id', $fromService);

                    return;
                }
            }

            $currentTeamId = app(CurrentTeam::class)->id();

            if ($currentTeamId) {
                $model->setAttribute('team_id', $currentTeamId);
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
