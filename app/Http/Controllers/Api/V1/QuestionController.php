<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Question\StoreQuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Http\Traits\ApiResponse;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of questions.
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->has('page')) {
            $questions = Question::latest()->paginate($request->integer('per_page', 15));

            return $this->paginatedResponse(
                QuestionResource::collection($questions),
                __('Questions retrieved successfully')
            );
        }

        $questions = Question::latest()->get();

        return $this->successResponse(
            QuestionResource::collection($questions),
            __('Questions retrieved successfully')
        );
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(StoreQuestionRequest $request): JsonResponse
    {
        $question = Question::create($request->validated());

        return $this->createdResponse(
            new QuestionResource($question),
            __('Question submitted successfully')
        );
    }
}
