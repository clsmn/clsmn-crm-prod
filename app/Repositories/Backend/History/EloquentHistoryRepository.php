<?php

namespace App\Repositories\Backend\History;

use App\Models\History\History;
use App\Models\History\HistoryType;
use App\Exceptions\GeneralException;
use Auth;
use DB;
/**
 * Class EloquentHistoryRepository.
 */
class EloquentHistoryRepository implements HistoryContract
{
    /**
     * @var
     */
    public $type;

    /**
     * @var null
     */
    public $subType = null;

    /**
     * @var
     */
    public $text;

    /**
     * @var null
     */
    public $entity_id = null;

    /**
     * @var null
     */
    public $sub_entity_id = null;

    /**
     * @var null
     */
    public $user_id = null;

    /**
     * @var null
     */
    public $icon = null;

    /**
     * @var null
     */
    public $class = null;

    /**
     * @var null
     */
    public $assets = null;

    /**
     * @var string
     */
    public $orderBy = 'DESC';

    /**
     * Pagination type
     * paginate: Prev/Next with page numbers
     * simplePaginate: Just Prev/Next arrows.
     *
     * @var string
     */
    private $paginationType = 'simplePaginate';

    /**
     * @param $type
     *
     * @return $this
     * @throws GeneralException
     */
    public function withType($type)
    {
        //Type can be id or name
        if (is_numeric($type)) {
            $this->type = HistoryType::findOrFail($type);
        } else {
            $this->type = HistoryType::where('name', $type)->first();
        }

        if ($this->type instanceof HistoryType) {
            return $this;
        }

        throw new GeneralException('An invalid history type was supplied: '.$type.'.');
    }

    /**
     * @param $text
     *
     * @return $this
     * @throws GeneralException
     */
    public function withSubType($subType)
    {
        $this->subType = $subType;

        return $this;
    }

    /**
     * @param $text
     *
     * @return $this
     * @throws GeneralException
     */
    public function withUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * @param $text
     *
     * @return $this
     * @throws GeneralException
     */
    public function withText($text)
    {
        if (strlen($text)) {
            $this->text = $text;
        } else {
            throw new GeneralException('You must supply text for each history item.');
        }

        return $this;
    }

    /**
     * @param $text
     *
     * @return $this
     * @throws GeneralException
     */
    public function orderBy($order)
    {
        $this->orderBy = $order;

        return $this;
    }

    /**
     * @param $entity_id
     *
     * @return $this
     */
    public function withEntity($entity_id)
    {
        $this->entity_id = $entity_id;

        return $this;
    }

    /**
     * @param $sub_entity_id
     *
     * @return $this
     */
    public function withSubEntity($sub_entity_id)
    {
        $this->sub_entity_id = $sub_entity_id;

        return $this;
    }

    /**
     * @param $icon
     *
     * @return $this
     */
    public function withIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param $class
     *
     * @return $this
     */
    public function withClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param $assets
     *
     * @return $this
     */
    public function withAssets($assets)
    {
        $this->assets = is_array($assets) && count($assets) ? json_encode($assets) : null;

        return $this;
    }

    /**
     * @return mixed
     */
    public function log()
    {
        return History::create([
            'type_id'       => $this->type->id,
            'sub_type'      => $this->subType,
            'user_id'       => ($this->user_id != null)? $this->user_id : access()->id(),
            'entity_id'     => $this->entity_id,
            'sub_entity_id' => $this->sub_entity_id,
            'icon'          => $this->icon,
            'class'         => $this->class,
            'text'          => $this->text,
            'assets'        => $this->assets,
        ]);
    }

    /**
     * @param null $limit
     * @param bool $paginate
     * @param int  $pagination
     *
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function render($limit = null, $paginate = true, $pagination = 10)
    {
        $history = History::with('user');
        $history = $this->buildPagination($history, $limit, $paginate, $pagination);
        if (! $history->count()) {
            return trans('history.backend.none');
        }

        return $this->buildList($history, $paginate);
    }

    /**
     * @param $type
     * @param null $limit
     * @param bool $paginate
     * @param int  $pagination
     *
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function renderType($type, $limit = null, $paginate = true, $pagination = 10)
    {
        $history = History::with('user');
        $history = $this->checkType($history, $type);
        $history = $this->buildPagination($history, $limit, $paginate, $pagination);
        if (! $history->count()) {
            return trans('history.backend.none_for_type');
        }

        return $this->buildList($history, $paginate);
    }

    /**
     * @param $type
     * @param $entity_id
     * @param null $limit
     * @param bool $paginate
     * @param int  $pagination
     *
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function renderEntity($type, $entity_id, $limit = null, $paginate = true, $pagination = 10)
    {
         if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
        {
            $history = History::with('user', 'type')->where('entity_id', $entity_id)->where('archived', 0);
        }
        else
        {
            $lead_assigned_date = DB::table('leads')->select('assigned_at','data_medium')->where('id',$entity_id)->first();

            $total_history_count = DB::table('call_history')->select('id')->where('lead_id',$entity_id)->where('data_medium','FBL_REM_MS')->count();

            $total_history_no_answer_count = DB::table('call_history')->select('id')->where('lead_id',$entity_id)->where('lead_status','no_answer')->where('data_medium','FBL_REM_MS')->count();

            $source = $lead_assigned_date->data_medium;

            if($lead_assigned_date->data_medium == 'FBL_REM_MS')
            {
                if($total_history_count == $total_history_no_answer_count)
                {
                    $history = History::with('user', 'type')->where('entity_id', $entity_id)->where('archived', 0)->where('created_at','>=',$lead_assigned_date->assigned_at)->orderBy('id', 'DESC');
                }
                else
                {
                    $history = History::with('user', 'type')->where('entity_id', $entity_id)->where('archived', 0);
                }
            }
            else
            {
                $history = History::with('user', 'type')->where('entity_id', $entity_id);
            }
           
        }

        $history = $this->checkType($history, $type);
        $history = $this->buildPagination($history, $limit, $paginate, $pagination);

        if (! $history->count()) 
        {
            return trans('history.backend.none_for_entity', ['entity' => $type]);
        }
        return $this->buildList($history, $paginate);

        // $history = History::with('user', 'type')->where('entity_id', $entity_id);
        // $history = $this->checkType($history, $type);
        // $history = $this->buildPagination($history, $limit, $paginate, $pagination);
        // if (! $history->count()) {
        //     return trans('history.backend.none_for_entity', ['entity' => $type]);
        // }

        // return $this->buildList($history, $paginate);
    }

    /**
     * @param $text
     * @param bool $assets
     *
     * @return mixed|string
     */
    public function renderDescription($text, $assets = false)
    {
        $assets = json_decode($assets, true);
        $count = 1;
        $assetCount = count((is_countable($assets)?$assets:[]));
        $asset_count = count((is_countable($assets)?$assets:[])) + 1;
        if(Auth::user()->roles[0]->name != 'Administrator' || Auth::user()->roles[0]->name != 'Manager')
        {
            $text = 'trans("history.backend.lead.assigned")';
        }
        if ($assetCount) {
            $text = preg_replace_callback('/trans\(\"([^"]+)\"\)/', function ($matches) use($text){
                        $data = trans($matches[1]);
                        if($data != '' && $data != null)
                        {
                            return $data;
                        }else{
                            return $text;
                        }
                    }, $text);

            foreach ($assets as $name => $values) {
                $key = explode('_', $name)[0];
                $type = explode('_', $name)[1];

                switch ($type) {
                    case 'link':
                        if (is_array($values)) {
                            switch (count($values)) {
                                case 1:
                                    $text = str_replace('{'.$key.'}', link_to_route($values[0], $values[0]), $text);
                                break;

                                case 2:
                                    $text = str_replace('{'.$key.'}', link_to_route($values[0], $values[1]), $text);
                                break;

                                case 3:
                                    $text = str_replace('{'.$key.'}', link_to_route($values[0], $values[1], $values[2]), $text);
                                break;

                                case 4:
                                    $text = str_replace('{'.$key.'}', link_to_route($values[0], $values[1], $values[2], $values[3]), $text);
                                break;
                            }
                        } else {
                            //Normal url
                            $text = str_replace('{'.$key.'}', link_to($values, $values), $text);
                        }

                    break;

                    case 'string':
                        $text = str_replace('{'.$key.'}', $values, $text);
                    break;
                }

                $count++;
            }
        }

        if ($asset_count == $count) {
            //Evaluate all trans functions as PHP
            //We don't want to use eval() for security reasons so we're explicitly converting trans cases
            return preg_replace_callback('/trans\(\"([^"]+)\"\)/', function ($matches) {
                return trans($matches[1]);
            }, $text);
        }

        return '';
    }

    /**
     * @param $history
     * @param bool $paginate
     *
     * @return string
     */
    public function buildList($history, $paginate = true)
    {
        $total_history_count = DB::table('call_history')->select('id')->where('lead_id',$history[0]->entity_id)->where('data_medium','FBL_REM_MS')->count();

        $total_history_no_answer_count = DB::table('call_history')->select('id')->where('lead_id',$history[0]->entity_id)->where('lead_status','no_answer')->where('data_medium','FBL_REM_MS')->count();
        $source = DB::table('leads')->select('data_medium')->where('id',$history[0]->entity_id)->first();
        return view('backend.history.partials.list', ['history' => $history, 'paginate' => $paginate,'total_history_count' => $total_history_count,'total_history_no_answer_count' => $total_history_no_answer_count,'source' => $source])
            ->render();
    }

    /**
     * @param $query
     * @param $limit
     * @param $paginate
     * @param $pagination
     *
     * @return mixed
     */
    public function buildPagination($query, $limit, $paginate, $pagination)
    {
        $query->orderBy('id', $this->orderBy);

        if ($paginate && is_numeric($pagination)) {
            return $query->{$this->paginationType}($pagination);
        } else {
            if ($limit && is_numeric($limit)) {
                $query->take($limit);
            }

            return $query->get();
        }
    }

    /**
     * @param $query
     * @param $type
     *
     * @return mixed
     */
    private function checkType($query, $type)
    {
        if (is_numeric($type)) {
            return $query->where('type_id', $type);
        } else {
            $type = strtolower($type);

            return $query->whereHas('type', function ($query) use ($type) {
                $query->where('name', ucfirst($type));
            });
        }
    }
}
