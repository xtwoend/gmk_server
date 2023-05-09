<?php

declare(strict_types=1);

namespace App\MdModel;

/**
 */
class ProductionProblem extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'production_problems';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'production_id', 'record_id', 'status', 'action_1', 'action_2', 'action_3', 'operator_id', 'qa_id', 'remark'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];
}
