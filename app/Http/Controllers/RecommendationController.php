<?php

namespace App\Http\Controllers;

use App\Services\ProgramRecommendationService;

class RecommendationController extends Controller
{
    public function index(ProgramRecommendationService $recommendationService)
    {
        $profile = auth()->user()->profile;
        $recommendations = $profile
            ? $recommendationService->recommend($profile)
            : collect();

        return view('frontend.recommendations', compact('profile', 'recommendations'));
    }
}
