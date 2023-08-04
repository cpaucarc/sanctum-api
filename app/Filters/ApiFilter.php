<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter
{
    protected $safeParams = [];

    protected $columnMap = [];

    protected $operatorMap = [];

    public function transform(Request $request)
    {
        $eloquentQuery = [];

        foreach ($this->safeParams as $param => $operators) {
            $query = $request->query($param);

            if (!isset($query)) {
                continue;
            }
            $column = $this->columnMap[$param] ?? $param;

            if (gettype($query) == 'string') {
                $eloquentQuery[] = [$column, 'like', '%' . $query . '%'];
            }

            if (gettype($query) == 'array') {
                foreach ($operators as $operator) {
                    if (isset($query[$operator])) {
                        $eloquentQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                    }
                }
            }
        }

        info($eloquentQuery);
        return $eloquentQuery;
    }
}
