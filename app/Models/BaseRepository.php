<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Pagination\Paginator;
use Closure;
use App\Services\CommonService;

/**
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseRepository
{
    protected $_db;
    protected $_cache;

    /**
     * The config enable cache.
     *
     * @var boolean
     */
    protected $enableCache = true;

    public function __construct($model)
    {
        $this->_db = CommonService::getModel($model, 'db');
        // $this->_cache = CommonService::getModel($model, 'cache');
    }

    public function getInstance($instanceType = 'db')
    {
        return strtolower($instanceType) === 'db' ? $this->_db : $this->_cache;
    }

    /**
     * paginate custom
     *
     * @param  object $query
     * @param int $perPage
     * @param int $intPage
     * @return Paginator | Collection | Array
     */
    private function doPaginate($query, $perPage, $intPage = null)
    {
        if ((int) $perPage === 0) {
            $perPage = 1000000000000;
        }

        $pageName = 'page';
        $intPage = $intPage ?: Paginator::resolveCurrentPage($pageName);

        // paginate
        return $query->paginate($perPage, ['*'], $pageName, $intPage);
    }

    /**
    * @param $query
    * @param $conditions
    * @param $type
    * @return mixed
    */
    private function checkCondition($query, $conditions, $type = 'where')
    {
        $arrOperation = ['<=', '>=', '<', '>', '<>', '!=', 'LIKE', 'NOTIN', 'NOTNULL'];

        switch ($type) {
            case 'has':
                foreach ($conditions as $condition) {
                    if (is_array($condition)) {
                        $query = $query->whereHas($condition[0], function ($query) use ($condition) {
                            $condition[1]($query);
                        });
                    } else {
                        $query = $query->whereHas($condition);
                    }
                }

                break;
            case 'where':
            default:
                foreach ($conditions as $field => $value) {
                    if ($value instanceof Closure) {
                        $query = $query->where(function ($query) use ($value) {
                            $value($query);
                        });
                    } else {
                        // If Array then using whereIn
                        if (is_array($value)) {
                            if (!empty($value)) {
                                // check if $value[0] is an array
                                if (is_array($value[0])) {
                                    foreach ($value as $v) {
                                        $query = $this->checkCondition($query, [
                                            $field => $v,
                                        ], $type);
                                    }
                                } else {
                                    if (in_array(strtoupper($value[0]), $arrOperation)) {
                                        if (strtoupper($value[0]) == 'NOTIN') {
                                            $query = $query->whereNotIn($field, $value[1]);
                                        } elseif (strtoupper($value[0]) == 'NOTNULL') {
                                            $query = $query->whereNotNull($field);
                                        } else {
                                            if (!Str::startsWith($value[1], 'column|')) {
                                                $query = $query->where($field, strtoupper($value[0]), $value[1]);
                                            } else {
                                                $query = $query->whereColumn($field, strtoupper($value[0]), str_replace('column|', '', $value[1]));
                                            }
                                        }
                                    } else {
                                        $query = $query->whereIn($field, $value);
                                    }
                                }
                            } else {
                                $query = $query->whereIn($field, $value);
                            }
                        } else {
                            if (is_null($value)) {
                                $query = $query->whereNull($field);
                            } else {
                                $query = $query->where($field, $value);
                            }
                        }
                    }
                }

                break;
        }

        return $query;
    }

    /**
     * @param $query
     * @param $joinList
     * @return mixed
     */
    private function checkJoin($query, $joinList)
    {
        foreach ($joinList as $join) {
            if (is_array($join)) {
                $query = $query->join($join[0], function ($query) use ($join) {
                    $join[1]($query);
                });
            } else {
                $query = $query->join($join);
            }
        }

        return $query;
    }

    /**
     * @param $query
     * @param $orderByList
     * @return mixed
     */
    private function getOrderBy($query, $orderByList)
    {
        foreach ($orderByList as $key => $value) {
            if (is_numeric($key)) {
                $query = $query->orderByRaw($value);
            } else {
                $query = $query->orderBy($key, $value);
            }
        }

        return $query;
    }

    public function create($params)
    {
        return $this->_db->create($params);
    }

    public function createMulti($multiParams)
    {
        foreach ($multiParams as $params) {
            $this->_db->create($params);
        }
    }

    public function createMultiOrPass($multiParams)
    {
        $results = []; 
    
        foreach ($multiParams as $params) {
            $data = $this->findByAttributes($params);
    
            if (!empty($data)) {
                continue;
            } else {
                $data = $this->_db->create($params); 
            }
    
            $results[] = $data;
        }
    
        return $results;
    }

    public function update($params, $id)
    {
        $ids = Arr::wrap($id);
        $result = [];

        foreach ($ids as $id) {
            $data = $this->_db->find($id);
            if ($data) {
                $data->update($params);
            }

            $result[$id] = $data;
        }

        if (count($result) === 1) {
            return array_shift($result);
        }

        return $result;
    }

    public function findByAttributes($attributes)
    {
        return $this->_db->where($attributes)->first();
    }

    public function upsert(array $attributes, array $options)
    {
        $data = $this->findByAttributes($attributes);

        if ($data) {
            $data->update($options);
        } else {
            $data = $this->_db->create($options);
        }

        return $data;
        /////////////////////////////////////////////////
        // return $this->_db->upsert($data, $uniqueColumns, $updateColumns);
    }

    public function upsertBulk(array $items, array $uniqueKeys)
    {
        $model = $this->_db;
        $columns = array_keys($items[0]);
        $updateColumns = array_diff($columns, $uniqueKeys);
        
        return $model->upsert($items, $uniqueKeys, $updateColumns);
    }
    

    public function createOrPass($attributes, $options = [])
    {
        $data = $this->findByAttributes($attributes);

        if ($data) {
            return;
        } else {
            $data = $this->_db->create($options);
        }

        return $data;
    }

    public function createOrGetData($attributes, $options = [])
    {
        $data = $this->findByAttributes($attributes);

        if ($data) {
            return $data;
        } else {
            $data = $this->_db->create($options);
            return $data;
        }
    }

    public function updateByKeys($conditions, $params)
    {
        $this->_db->updateByAttributes($conditions, $params);
    }

    public function delete($id)
    {
        $id = Arr::wrap($id);

        return $this->_db->destroy($id);
    }

    public function forceDelete($ids)
    {
        foreach (Arr::wrap($ids) as $id) {
            $this->_db->find($id)->forceDelete();
        }
    }

    public function deleteByKeys($conditions, $forceDelete = false)
    {
        if (!$forceDelete) {
            $this->_db->deleteByAttributes($conditions);
        } else {
            $this->_db->forceDeleteByAttributes($conditions);
        }
    }

    public function increment($id, $column, $value = 1, $extra = [])
    {
        return $this->_db->find($id)->increment($column, $value, $extra);
    }

    public function decrement($id, $column, $value = 1, $extra = [])
    {
        return $this->_db->find($id)->decrement($column, $value, $extra);
    }

    public function restore($id)
    {
        $data = $this->getDetailWithTrashed($id);

        if ($data) {
            // first restore
            $data->restore();

            // update deleted_by = null
            $data->update([
                'deleted_by' => null,
            ]);
        }

        return $data;
    }

    public function getDetail($id)
    {
        return $this->_db->find($id);
    }

    public function getDetailWithTrashed($id)
    {
        return $this->_db->withTrashed()->find($id);
    }

    public function getDetailDeleted($id)
    {
        return $this->_db->withTrashed()->whereNotNull('deleted_at')->find($id);
    }

    /**
     * @param array params
     * params = [
     *      type        => '1|2|3' // 1: get one, 2: get list, 3: get all(default)
     *      id          => '' // required if type has value is 1
     *      item        => 20 //default is 20; if value is -1 then get by where in ids
     *      page        => 1
     *      select      => ['*'] // select all column
     *      with        => [] // with relationship
     *      join        => ['join', ['join', 'function'], ...]
     *      where       => ['column' => 'value', function ($query) {}]
     *      whereHas     => ['relation', ['relation', 'function'], ...]
     *      orderBy     => ['column' => 'value', ...]
     *      withTrashed   => true/false // default false
     * ]
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|null
     */
    public function getData($params = [])
    {
        $params = array_merge([
            'type' => 3,
            'id' => '',
            'item' => 20,
            'page' => 1,
            'select' => ['*'],
            'with' => [],
            'withCount' => null,
            'join' => [],
            'where' => [],
            'whereHas' => [],
            'orderBy' => [
                'id' => 'DESC',
            ],
            'isDistinct' => false,
            'withTrashed' => false,
        ], $params);

        $data = null;

        $type = $params['type'];
        $select = $params['select'];
        $id = $params['id'];
        $item = $params['item'];
        $page = $params['page'];
        $with = $params['with'];
        $withCount = $params['withCount'];
        $join = $params['join'];
        $where = $params['where'];
        $whereHas = $params['whereHas'];
        $orderBy = $params['orderBy'];
        $isDistinct = $params['isDistinct'];
        $withTrashed = $params['withTrashed'];

        $query = $this->_db->select($select)->with($with);
        if ($isDistinct) {
            $query->distinct();
        }
        if ($withCount) {
            $query->withCount($withCount);
        }
        $query = $this->checkJoin($query, $join);
        $query = $this->getOrderBy($query, $orderBy);
        if ($withTrashed) {
            $query->withTrashed();
        }

        switch ($type) {
            case 1:
                // get one
                if (!is_array($id) && !empty($id)) {
                    $data = $query->find($id);
                } else {
                    if (!empty($where)) {
                        $query = $query->where(function ($query) use ($where) {
                            $query = $this->checkCondition($query, $where);
                        });
                    }
                    if (!empty($whereHas)) {
                        $query = $this->checkCondition($query, $whereHas, 'has');
                    }

                    $data = $query->first();
                }

                break;
            case 2:
                // get list
                if ($item === -1) {
                    // get list define
                    if (is_array($id) && !empty($id)) {
                        $data = $query->whereIn('id', $id)->get();
                    }
                } else {
                    if (!empty($where)) {
                        $query = $query->where(function ($query) use ($where) {
                            $query = $this->checkCondition($query, $where);
                        });
                    }
                    if (!empty($whereHas)) {
                        $query = $this->checkCondition($query, $whereHas, 'has');
                    }

                    $data = $this->doPaginate($query, $item, $page);
                }

                break;
            case 3:
            default:
                // get all
                if (!empty($where)) {
                    $query = $query->where(function ($query) use ($where) {
                        $query = $this->checkCondition($query, $where);
                    });
                }
                if (!empty($whereHas)) {
                    $query = $this->checkCondition($query, $whereHas, 'has');
                }

                $data = $query->get();

                break;
        }

        return $data;
    }

    public function parseDetail($data)
    {
        if ($data->isNotEmpty()) {
            $data->transform(function ($id) {
                return $this->getDetail($id);
            });
        }

        return $data;
    }
}
