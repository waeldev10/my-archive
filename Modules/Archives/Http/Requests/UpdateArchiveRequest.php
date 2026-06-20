<?php

declare(strict_types=1);

namespace Modules\Archives\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArchiveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $type = $this->route('type');

        $rules = [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_favorite' => ['sometimes', 'boolean'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:100'],
        ];

        return array_merge($rules, $this->typeSpecificRules($type));
    }

    /**
     * Get type-specific validation rules (all optional for updates).
     *
     * @return array<string, mixed>
     */
    private function typeSpecificRules(?string $type): array
    {
        return match ($type) {
            'link' => [
                'url' => ['sometimes', 'required', 'string', 'url', 'max:2048'],
                'domain' => ['nullable', 'string', 'max:255'],
                'preview_image' => ['nullable', 'string', 'max:2048'],
                'preview_description' => ['nullable', 'string', 'max:500'],
            ],
            'image' => [
                'file' => ['sometimes', 'required', 'file', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
                'alt_text' => ['nullable', 'string', 'max:255'],
            ],
            'file' => [
                'file' => ['sometimes', 'required', 'file', 'max:25600'],
                'original_name' => ['nullable', 'string', 'max:255'],
            ],
            'todo' => [
                'due_date' => ['nullable', 'date'],
                'completed_at' => ['nullable', 'date'],
                'priority' => ['nullable', 'string', 'in:low,medium,high'],
            ],
            'plan' => [
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
                'status' => ['nullable', 'string', 'in:draft,active,completed,cancelled'],
                'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            ],
            'project' => [
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
                'status' => ['nullable', 'string', 'in:idea,planning,active,paused,completed,cancelled'],
                'repository_url' => ['nullable', 'string', 'url', 'max:2048'],
            ],
            'course' => [
                'provider' => ['nullable', 'string', 'max:255'],
                'platform' => ['nullable', 'string', 'max:255'],
                'completion_status' => ['nullable', 'string', 'in:not_started,in_progress,completed'],
                'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            ],
            'book' => [
                'author' => ['nullable', 'string', 'max:255'],
                'isbn' => ['nullable', 'string', 'max:20'],
                'pages' => ['nullable', 'integer', 'min:1'],
                'status' => ['nullable', 'string', 'in:to_read,reading,finished,abandoned'],
                'started_at' => ['nullable', 'date'],
                'finished_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            ],
            'snippet' => [
                'code_language' => ['nullable', 'string', 'max:50'],
                'code_content' => ['sometimes', 'required', 'string'],
                'source_url' => ['nullable', 'string', 'url', 'max:2048'],
            ],
            'website' => [
                'url' => ['sometimes', 'required', 'string', 'url', 'max:2048'],
                'domain' => ['nullable', 'string', 'max:255'],
                'feed_url' => ['nullable', 'string', 'url', 'max:2048'],
            ],
            'journal' => [
                'entry_date' => ['sometimes', 'required', 'date'],
                'mood' => ['nullable', 'string', 'max:50'],
                'location' => ['nullable', 'string', 'max:255'],
            ],
            default => [],
        };
    }
}
