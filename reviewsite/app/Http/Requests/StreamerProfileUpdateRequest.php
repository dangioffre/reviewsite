<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StreamerProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $streamerProfile = $this->route('streamerProfile');
        return auth()->check() && auth()->user()->can('update', $streamerProfile);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bio' => 'nullable|string|max:1000',
            'schedules' => 'nullable|array|max:7', // Max 7 days per week
            'schedules.*.id' => 'nullable|integer|exists:streamer_schedules,id',
            'schedules.*.day_of_week' => 'required_with:schedules|integer|between:0,6',
            'schedules.*.start_time' => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time' => 'required_with:schedules|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.timezone' => [
                'required_with:schedules',
                'string',
                Rule::in(timezone_identifiers_list())
            ],
            'schedules.*.notes' => 'nullable|string|max:255',
            'schedules.*.is_active' => 'nullable|boolean',
            'social_links' => 'nullable|array|max:10',
            'social_links.*.id' => 'nullable|integer|exists:streamer_social_links,id',
            'social_links.*.platform' => 'required_with:social_links|string|max:50',
            'social_links.*.url' => 'required_with:social_links|url|max:500',
            'social_links.*.display_name' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'schedules.*.end_time.after' => 'End time must be after start time.',
            'schedules.*.timezone.in' => 'Please select a valid timezone.',
            'schedules.max' => 'You can only add up to 7 schedule entries.',
            'social_links.max' => 'You can only add up to 10 social links.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that schedule entries don't overlap for the same day
            $schedules = $this->input('schedules', []);
            $daySchedules = [];
            
            foreach ($schedules as $index => $schedule) {
                if (!isset($schedule['day_of_week'], $schedule['start_time'], $schedule['end_time'])) {
                    continue;
                }
                
                $day = $schedule['day_of_week'];
                $start = $schedule['start_time'];
                $end = $schedule['end_time'];
                
                if (!isset($daySchedules[$day])) {
                    $daySchedules[$day] = [];
                }
                
                foreach ($daySchedules[$day] as $existingIndex => $existingSchedule) {
                    if ($this->timesOverlap($start, $end, $existingSchedule['start_time'], $existingSchedule['end_time'])) {
                        $validator->errors()->add(
                            "schedules.{$index}.start_time",
                            'Schedule times cannot overlap on the same day.'
                        );
                        break;
                    }
                }
                
                $daySchedules[$day][] = ['start_time' => $start, 'end_time' => $end];
            }
        });
    }

    /**
     * Check if two time ranges overlap.
     */
    private function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        return $start1 < $end2 && $start2 < $end1;
    }
}
