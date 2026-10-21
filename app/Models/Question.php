<?php

namespace App\Models;

use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'questions',
    ];

    /**
     * Mutator to allow setting question or quiztions interchangeably.
     */
    public function setQuestionAttribute(mixed $value): void
    {
        $this->attributes['questions'] = $value;
    }

    /**
     * Accessor to allow reading question alias.
     */
    public function getQuestionAttribute(): ?string
    {
        return $this->attributes['questions'] ?? null;
    }

    /**
     * Mutator for quiztions alias.
     */
    public function setQuiztionsAttribute(mixed $value): void
    {
        $this->attributes['questions'] = $value;
    }

    /**
     * Accessor for quiztions alias.
     */
    public function getQuiztionsAttribute(): ?string
    {
        return $this->attributes['questions'] ?? null;
    }
}
