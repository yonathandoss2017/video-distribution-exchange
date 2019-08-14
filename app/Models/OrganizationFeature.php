<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationFeature extends Model
{
    use SoftDeletes;

    protected $table = 'organization_features';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['organization_id', 'ai_content_review'];

    const FEATURE_AI_REVIEW = 'ai_content_review';

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function has($features)
    {
        if (!is_array($features)) {
            $features = [$features];
        }
        $hasFeatures = [];
        foreach ($features as $feature) {
            if ($this->$feature == 1) {
                $hasFeatures[$feature] = true;
            } else {
                $hasFeatures[$feature] = false;
            }
        }

        return $hasFeatures;
    }
}
