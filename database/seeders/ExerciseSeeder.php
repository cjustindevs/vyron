<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercises = [
            // ==================== CHEST ====================
            ['exercise_name' => 'Barbell Bench Press', 'muscle_group' => 'Chest', 'equipment' => 'Barbell', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Dumbbell Bench Press', 'muscle_group' => 'Chest', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Incline Dumbbell Press', 'muscle_group' => 'Chest', 'equipment' => 'Dumbbells', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Decline Bench Press', 'muscle_group' => 'Chest', 'equipment' => 'Barbell', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Chest Flyes', 'muscle_group' => 'Chest', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Cable Crossover', 'muscle_group' => 'Chest', 'equipment' => 'Cable Machine', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Push-up', 'muscle_group' => 'Chest', 'equipment' => 'None', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Dips', 'muscle_group' => 'Chest', 'equipment' => 'Parallel Bars', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Pec Deck Fly', 'muscle_group' => 'Chest', 'equipment' => 'Machine', 'difficulty' => 'beginner'],

            // ==================== BACK ====================
            ['exercise_name' => 'Deadlift', 'muscle_group' => 'Back', 'equipment' => 'Barbell', 'difficulty' => 'advanced'],
            ['exercise_name' => 'Pull-up', 'muscle_group' => 'Back', 'equipment' => 'Pull-up Bar', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Lat Pulldown', 'muscle_group' => 'Back', 'equipment' => 'Cable Machine', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Seated Cable Row', 'muscle_group' => 'Back', 'equipment' => 'Cable Machine', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Bent Over Row', 'muscle_group' => 'Back', 'equipment' => 'Barbell', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'T-Bar Row', 'muscle_group' => 'Back', 'equipment' => 'Barbell', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Face Pull', 'muscle_group' => 'Back', 'equipment' => 'Cable Machine', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Inverted Row', 'muscle_group' => 'Back', 'equipment' => 'Barbell', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Single Arm Dumbbell Row', 'muscle_group' => 'Back', 'equipment' => 'Dumbbell', 'difficulty' => 'beginner'],

            // ==================== LEGS ====================
            ['exercise_name' => 'Barbell Squat', 'muscle_group' => 'Legs', 'equipment' => 'Barbell', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Goblet Squat', 'muscle_group' => 'Legs', 'equipment' => 'Dumbbell', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Leg Press', 'muscle_group' => 'Legs', 'equipment' => 'Leg Press Machine', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Romanian Deadlift', 'muscle_group' => 'Legs', 'equipment' => 'Barbell', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Lunges', 'muscle_group' => 'Legs', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Leg Curl', 'muscle_group' => 'Legs', 'equipment' => 'Machine', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Leg Extension', 'muscle_group' => 'Legs', 'equipment' => 'Machine', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Calf Raises', 'muscle_group' => 'Legs', 'equipment' => 'Dumbbell', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Bulgarian Split Squat', 'muscle_group' => 'Legs', 'equipment' => 'Dumbbells', 'difficulty' => 'intermediate'],

            // ==================== SHOULDERS ====================
            ['exercise_name' => 'Overhead Press', 'muscle_group' => 'Shoulders', 'equipment' => 'Barbell', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Dumbbell Shoulder Press', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Lateral Raises', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Front Raises', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Reverse Flyes', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Arnold Press', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Face Pull', 'muscle_group' => 'Shoulders', 'equipment' => 'Cable Machine', 'difficulty' => 'beginner'],

            // ==================== BICEPS ====================
            ['exercise_name' => 'Barbell Curl', 'muscle_group' => 'Biceps', 'equipment' => 'Barbell', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Dumbbell Curl', 'muscle_group' => 'Biceps', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Hammer Curl', 'muscle_group' => 'Biceps', 'equipment' => 'Dumbbells', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Concentration Curl', 'muscle_group' => 'Biceps', 'equipment' => 'Dumbbell', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Cable Curl', 'muscle_group' => 'Biceps', 'equipment' => 'Cable Machine', 'difficulty' => 'beginner'],

            // ==================== TRICEPS ====================
            ['exercise_name' => 'Tricep Pushdown', 'muscle_group' => 'Triceps', 'equipment' => 'Cable Machine', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Skull Crushers', 'muscle_group' => 'Triceps', 'equipment' => 'Barbell', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Tricep Dips', 'muscle_group' => 'Triceps', 'equipment' => 'Bench', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Overhead Tricep Extension', 'muscle_group' => 'Triceps', 'equipment' => 'Dumbbell', 'difficulty' => 'beginner'],

            // ==================== CORE ====================
            ['exercise_name' => 'Plank', 'muscle_group' => 'Core', 'equipment' => 'None', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Russian Twists', 'muscle_group' => 'Core', 'equipment' => 'Weight Plate', 'difficulty' => 'intermediate'],
            ['exercise_name' => 'Leg Raises', 'muscle_group' => 'Core', 'equipment' => 'None', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Crunches', 'muscle_group' => 'Core', 'equipment' => 'None', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Bicycle Crunches', 'muscle_group' => 'Core', 'equipment' => 'None', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Dead Bug', 'muscle_group' => 'Core', 'equipment' => 'None', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Hanging Leg Raises', 'muscle_group' => 'Core', 'equipment' => 'Pull-up Bar', 'difficulty' => 'intermediate'],

            // ==================== CARDIO ====================
            ['exercise_name' => 'Running', 'muscle_group' => 'Cardio', 'equipment' => 'None', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Jump Rope', 'muscle_group' => 'Cardio', 'equipment' => 'Jump Rope', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Cycling', 'muscle_group' => 'Cardio', 'equipment' => 'Bicycle', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Rowing Machine', 'muscle_group' => 'Cardio', 'equipment' => 'Rower', 'difficulty' => 'beginner'],
            ['exercise_name' => 'Stair Climber', 'muscle_group' => 'Cardio', 'equipment' => 'Stair Machine', 'difficulty' => 'beginner'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}