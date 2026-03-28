<?php

namespace App\Http\Requests\Web\Subcategory;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSubcategoryWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user instanceof User
            && $user->can('create-subcategories');
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'in:0,1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}