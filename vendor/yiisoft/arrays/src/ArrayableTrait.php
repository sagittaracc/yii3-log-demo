<?php

declare(strict_types=1);

namespace Yiisoft\Arrays;

use function array_key_exists;
use function in_array;
use function is_array;
use function is_int;
use function is_string;

/**
 * `ArrayableTrait` provides a common implementation of the {@see ArrayableInterface} interface.
 *
 * `ArrayableTrait` implements {@see ArrayableInterface::toArray()} by respecting the field definitions as declared
 * in {@see ArrayableInterface::fields()} and {@see ArrayableInterface::extraFields()}.
 */
trait ArrayableTrait
{
    /**
     * Returns the list of fields that should be returned by default by {@see ArrayableInterface::toArray()}
     * when no specific fields are specified.
     *
     * A field is a named element in the returned array by {@see ArrayableInterface::toArray()}.
     *
     * This method should return an array of field names or field definitions.
     * If the former, the field name will be treated as an object property name whose value will be used
     * as the field value. If the latter, the array key should be the field name while the array value should be
     * the corresponding field definition which can be either an object property name or a PHP callable
     * returning the corresponding field value. The signature of the callable should be:
     *
     * ```php
     * function ($model, $field) {
     *     // return field value
     * }
     * ```
     *
     * For example, the following code declares four fields:
     *
     * - `email`: the field name is the same as the property name `email`;
     * - `firstName` and `lastName`: the field names are `firstName` and `lastName`, and their
     *   values are obtained from the `first_name` and `last_name` properties;
     * - `fullName`: the field name is `fullName`. Its value is obtained by concatenating `first_name`
     *   and `last_name`.
     *
     * ```php
     * return [
     *     'email',
     *     'firstName' => 'first_name',
     *     'lastName' => 'last_name',
     *     'fullName' => function () {
     *         return $this->first_name . ' ' . $this->last_name;
     *     },
     * ];
     * ```
     *
     * In this method, you may also want to return different lists of fields based on some context
     * information. For example, depending on the privilege of the current application user,
     * you may return different sets of visible fields or filter out some fields.
     *
     * The default implementation of this method returns the public object member variables indexed by themselves.
     *
     * @return array The list of field names or field definitions.
     *
     * @see toArray()
     */
    public function fields(): array
    {
        $fields = array_keys(ArrayHelper::getObjectVars($this));
        return array_combine($fields, $fields);
    }

    /**
     * Returns the list of fields that can be expanded further and returned by {@see ArrayableInterface::toArray()}.
     *
     * This method is similar to {@see ArrayableInterface::fields()} except that the list of fields returned
     * by this method are not returned by default by {@see ArrayableInterface::toArray()}]. Only when field names
     * to be expanded are explicitly specified when calling {@see ArrayableInterface::toArray()}, will their values
     * be exported.
     *
     * The default implementation returns an empty array.
     *
     * You may override this method to return a list of expandable fields based on some context information
     * (e.g. the current application user).
     *
     * @return array The list of expandable field names or field definitions. Please refer
     * to {@see ArrayableInterface::fields()} on the format of the return value.
     *
     * @see toArray()
     * @see fields()
     */
    public function extraFields(): array
    {
        return [];
    }

    /**
     * Converts the model into an array.
     *
     * This method will first identify which fields to be included in the resulting array
     * by calling {@see resolveFields()}. It will then turn the model into an array with these fields.
     * If `$recursive` is true, any embedded objects will also be converted into arrays.
     * When embedded objects are {@see ArrayableInterface}, their respective nested fields
     * will be extracted and passed to {@see ArrayableInterface::toArray()}.
     *
     * @param array $fields The fields being requested.
     * If empty or if it contains '*', all fields as specified by {@see ArrayableInterface::fields()} will be returned.
     * Fields can be nested, separated with dots (.). e.g.: item.field.sub-field
     * `$recursive` must be true for nested fields to be extracted. If `$recursive` is false, only the root fields
     * will be extracted.
     * @param array $expand The additional fields being requested for exporting. Only fields declared
     * in {@see ArrayableInterface::extraFields()} will be considered.
     * Expand can also be nested, separated with dots (.). e.g.: item.expand1.expand2
     * `$recursive` must be true for nested expands to be extracted. If `$recursive` is false, only the root expands
     * will be extracted.
     * @param bool $recursive Whether to recursively return array representation of embedded objects.
     *
     * @return array The array representation of the object.
     */
    public function toArray(array $fields = [], array $expand = [], bool $recursive = true): array
    {
        $data = [];
        foreach ($this->resolveFields($fields, $expand) as $field => $definition) {
            $attribute = is_string($definition) ? $this->$definition : $definition($this, $field);

            if ($recursive) {
                $nestedFields = $this->extractFieldsFor($fields, $field);
                $nestedExpand = $this->extractFieldsFor($expand, $field);
                if ($attribute instanceof ArrayableInterface) {
                    $attribute = $attribute->toArray($nestedFields, $nestedExpand);
                } elseif (is_array($attribute) && ($nestedExpand || $nestedFields)) {
                    $attribute = $this->filterAndExpand($attribute, $nestedFields, $nestedExpand);
                }
            }
            $data[$field] = $attribute;
        }

        return $recursive ? ArrayHelper::toArray($data) : $data;
    }

    private function filterAndExpand(array $array, array $fields = [], array $expand = []): array
    {
        $data = [];
        $rootFields = $this->extractRootFields($fields);
        $rootExpand = $this->extractRootFields($expand);
        foreach (array_merge($rootFields, $rootExpand) as $field) {
            if (array_key_exists($field, $array)) {
                $attribute = $array[$field];
                $nestedFields = $this->extractFieldsFor($fields, $field);
                $nestedExpand = $this->extractFieldsFor($expand, $field);
                if ($attribute instanceof ArrayableInterface) {
                    $attribute = $attribute->toArray($nestedFields, $nestedExpand);
                } elseif (is_array($attribute) && ($nestedExpand || $nestedFields)) {
                    $attribute = $this->filterAndExpand($attribute, $nestedFields, $nestedExpand);
                }
                $data[$field] = $attribute;
            }
        }
        return $data;
    }

    /**
     * Extracts the root field names from nested fields.
     * Nested fields are separated with dots (.). e.g: "item.id"
     * The previous example would extract "item".
     *
     * @param array $fields The fields requested for extraction
     *
     * @return array root Fields extracted from the given nested fields.
     */
    protected function extractRootFields(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $result[] = strstr($field . '.', '.', true);
        }

        if (in_array('*', $result, true)) {
            $result = [];
        }

        return array_unique($result);
    }

    /**
     * Extract nested fields from a fields collection for a given root field
     * Nested fields are separated with dots (.). e.g: "item.id"
     * The previous example would extract "id".
     *
     * @param array $fields The fields requested for extraction.
     * @param string $rootField The root field for which we want to extract the nested fields.
     *
     * @return array Nested fields extracted for the given field.
     */
    protected function extractFieldsFor(array $fields, string $rootField): array
    {
        $result = [];

        foreach ($fields as $field) {
            if (str_starts_with($field, "{$rootField}.")) {
                $result[] = preg_replace('/^' . preg_quote($rootField, '/') . '\./i', '', $field);
            }
        }

        return array_unique($result);
    }

    /**
     * Determines which fields can be returned by {@see ArrayableInterface::toArray()}.
     * This method will first extract the root fields from the given fields.
     * Then it will check the requested root fields against those declared in {@see ArrayableInterface::fields()}
     * and {@see ArrayableInterface::extraFields()} to determine which fields can be returned.
     *
     * @param array $fields The fields being requested for exporting.
     * @param array $expand The additional fields being requested for exporting.
     *
     * @return array The list of fields to be exported. The array keys are the field names, and the array values
     * are the corresponding object property names or PHP callables returning the field values.
     */
    protected function resolveFields(array $fields, array $expand): array
    {
        $fields = $this->extractRootFields($fields);
        $expand = $this->extractRootFields($expand);
        $result = [];

        foreach ($this->fields() as $field => $definition) {
            if (is_int($field)) {
                $field = $definition;
            }
            if (empty($fields) || in_array($field, $fields, true)) {
                $result[$field] = $definition;
            }
        }

        if (empty($expand)) {
            return $result;
        }

        foreach ($this->extraFields() as $field => $definition) {
            if (is_int($field)) {
                $field = $definition;
            }
            if (in_array($field, $expand, true)) {
                $result[$field] = $definition;
            }
        }

        return $result;
    }
}
