<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkoutGenerationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'goal' => 'required|in:weight_loss,muscle_gain,maintenance,endurance,strength',
            'age' => 'nullable|integer|min:13|max:120',
            'sex' => 'nullable|in:male,female,other',
            'height' => 'nullable|numeric|min:100|max:300',
            'weight' => 'nullable|numeric|min:20|max:300',
            'experience_level' => 'required|in:beginner,intermediate,advanced',
            'activity_level' => 'nullable|in:sedentary,light,moderate,active,very_active',
            'workout_location' => 'required|in:home,gym,outdoor',
            'equipment' => 'nullable|array',
            'equipment.*' => 'string',
            'duration' => 'required|integer|min:15|max:180',
            'days_per_week' => 'required|integer|min:1|max:7',
        ];
    }

    public function messages(): array
    {
        return [
            'goal.required' => 'Please select a fitness goal.',
            'experience_level.required' => 'Please select your experience level.',
            'workout_location.required' => 'Please select where you will work out.',
            'duration.required' => 'Please enter your preferred workout duration.',
            'days_per_week.required' => 'Please enter how many days per week you can train.',
        ];
    }
}