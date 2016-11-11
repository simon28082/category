<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 2016/11/7
 * Time: 10:17
 */

namespace CrCms\Category\Repositories;


use CrCms\Category\Models\Category;
use CrCms\Category\Repositories\Interfaces\CategoryRepositoryInterface;
use CrCms\Kernel\Repositories\Repository;
use CrCms\Kernel\Repositories\Traits\RepositoryTrait;
use Illuminate\Database\Eloquent\Model;

class CategoryRepository implements CategoryRepositoryInterface
{

    use RepositoryTrait {
        create as createRt;
        update as updateRt;
        all as allRt;
    }

    /**
     *
     */
    const STATUS_OPEN = 1;

    /**
     *
     */
    const STATUS_CLOSE = 2;

    /**
     *
     */
    const STATUS = [self::STATUS_OPEN=>'开启',self::STATUS_CLOSE=>'关闭'];


    /**
     * @var array
     */
    protected $child = [];

    /**
     * CategoryRepository constructor.
     * @param Category $Model
     */
    public function __construct(Category $model)
    {
        $this->model = $model;
    }


    /**
     * @return array
     */
    public function status() : array
    {
        return static::STATUS;
    }


    /**
     * @return int
     */
    public function statusOpen() : int
    {
        return static::STATUS_OPEN;
    }


    /**
     * @return int
     */
    public function statusClose() : int
    {
        return static::STATUS_CLOSE;
    }


    /**
     * @param array $columns
     * @return \unknown
     */
    public function all(array $columns = ['*'])
    {
        $models = $this->allRt()->toArray();
        return show_tree(array_tree($models));
    }


    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data) : Model
    {
        return $this->createRt($data);
    }


    /**
     * @param array $data
     * @param int $id
     * @return Model
     */
    public function update(array $data, int $id) : Model
    {
        return $this->updateRt([
            'name'=>$data['name'],
            'mark'=>$data['mark'],
            'status'=>$data['status'],
            'remark'=>$data['remark'],
        ], $id);
    }


    /**
     * 查找出所有子集
     * @param int $id
     * @return mixed
     */
    public function findAllChild(int $id,bool $isSelf = true) : array
    {

        $self = $isSelf ?
                $this->model->find($id) :
                $this->model->where('parent_id',$id)->first();

        if ($self)
        {
            $this->child[] = $self;
            return $this->findAllChild($self->id,false);
        }

        return $this->child;
    }
}