<?php
namespace App\Http\Requests\State;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->id ? base64_decode($this->id) : 0;
        return [
            'name'              => 'required|string|max:255',
            'code'              => [
                'nullable',
                'string',
                'max:10',
                Rule::unique('states', 'code')->ignore($id),
            ],
            'shortname'          => 'nullable|string|max:10',
            'country_id'        => 'required|integer|exists:countries,id',
            'weight_ranges'      => 'nullable|array',
            'weight_ranges.*.weight_from' => 'required_with:weight_ranges|numeric|min:1',
            'weight_ranges.*.weight_to'   => 'required_with:weight_ranges|numeric|min:1|gte:weight_ranges.*.weight_from',
            'weight_ranges.*.delivery_charge' => 'required_with:weight_ranges|numeric|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('weight_ranges') && is_array($this->weight_ranges)) {
                $ranges = array_filter($this->weight_ranges, function($range) {
                    return !empty($range['weight_from']) && !empty($range['weight_to']) && !empty($range['delivery_charge']);
                });

                // Check for overlapping ranges
                $this->validateWeightRanges($validator, $ranges);
            }
        });
    }

    private function validateWeightRanges($validator, $ranges)
    {
        $ranges = array_values($ranges); // Re-index array
        
        for ($i = 0; $i < count($ranges); $i++) {
            for ($j = $i + 1; $j < count($ranges); $j++) {
                $range1 = $ranges[$i];
                $range2 = $ranges[$j];
                
                // Check if ranges overlap
                if ($this->rangesOverlap($range1, $range2)) {
                    $validator->errors()->add(
                        'weight_ranges',
                        "Weight ranges overlap: Range " . ($i + 1) . " (" . $range1['weight_from'] . "-" . $range1['weight_to'] . "gm) overlaps with Range " . ($j + 1) . " (" . $range2['weight_from'] . "-" . $range2['weight_to'] . "gm)."
                    );
                }
            }
        }
    }

    private function rangesOverlap($range1, $range2)
    {
        // Two ranges overlap if: max(start1, start2) < min(end1, end2)
        $start1 = (int)$range1['weight_from'];
        $end1 = (int)$range1['weight_to'];
        $start2 = (int)$range2['weight_from'];
        $end2 = (int)$range2['weight_to'];
        
        return max($start1, $start2) < min($end1, $end2);
    }
}