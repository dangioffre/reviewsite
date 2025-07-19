<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StreamerProfileStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
            'schedules.*.day_of_week' => 'required_with:schedules|integer|between:0,6',
            'schedules.*.start_time' => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time' => 'required_with:schedules|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.timezone' => [
                'required_with:schedules',
                'string',
                Rule::in(timezone_identifiers_list())
            ],
            'schedules.*.notes' => 'nullable|string|max:255',
            'social_links' => 'nullable|array|max:10',
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
}
