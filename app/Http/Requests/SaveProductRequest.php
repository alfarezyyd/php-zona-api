<?php

    namespace App\Http\Requests;

    use App\Enums\ProductStatusEnum;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\Rule;

    class SaveProductRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            return true;
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules(): array
        {
            return [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|gte:0',
                'stock' => 'required|numeric|gte:0',
                'sku' => 'required|string|max:255',
                'produced_by' => 'required|string|max:255',
                'status' => ['required', Rule::enum(ProductStatusEnum::class)],
                "category_id" => ["integer", "min:1", "exists:categories,id"],
                "images" => ["array"],
                "images.*" => ["image", "mimes:jpeg,png,jpg,gif,svg", "max:5120"],
            ];
        }
    }
