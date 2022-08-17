<?php

namespace Outl1ne\NovaInlineTextField\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Laravel\Nova\Panel;
use Outl1ne\NovaInlineTextField\InlineText;
use Laravel\Nova\Http\Requests\NovaRequest;

class NovaInlineTextFieldController extends Controller
{
    public function update(NovaRequest $request)
    {
        $modelId = $request->_inlineResourceId;
        $attribute = $request->_inlineAttribute;

        $resourceClass = $request->resource();
        $resourceValidationRules = $resourceClass::rulesForUpdate($request);
        $fieldValidationRules = $resourceValidationRules[$attribute] ?? null;

        if (!empty($fieldValidationRules)) {
            $request->validate([$attribute => $fieldValidationRules]);
        }

        // Find field in question
        try {
            $model = $request->model()->find($modelId);
            $resource = new $resourceClass($model);

            $allFields = collect($resource->fields($request));

            $field = null;
            foreach ($allFields as $_field){

                $class = get_class($_field);
                if ($class === Panel::class){

                    $panelFields = collect($_field->data);
                    $field = $panelFields->first(function ($f) use ($attribute){
                        return get_class($f) === InlineText::class && $f->attribute === $attribute;
                    });

                    if ($field) break;
                } else  if ($class == InlineText::class && $field->attribute === $attribute){
                    $field = $_field;
                    break;
                }
            }


            $field->fillInto($request, $model, $attribute);
            $model->save();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response('', 204);
    }
}
