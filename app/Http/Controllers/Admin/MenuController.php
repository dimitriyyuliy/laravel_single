<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\DbSort;
use App\Models\Main;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\" . $this->class;
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);

        // Связанная таблица, должен быть метод в моделе с названием таблицы
        $belongTable = $this->belongTable = 'menu_groups';
        // Связанный маршрут
        $this->belongRoute = 'filter-group';

        view()->share(compact('class', 'c','model', 'table', 'route', 'view', 'belongTable'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Записать в куку id из привязанной таблице, если не записано
        // Получаем меню родителя из куки
        $currentParentId = request()->cookie("{$this->table}_id");
        if ($currentParentId) {
            $currentParent = DB::table($this->belongTable)->find($currentParentId);

        } else {

            // Записать в куку id из привязанной таблице, если не записано
            $currentParent = DB::table($this->belongTable)->first();


            // Если нет родительский элементов, то предлагаем создать их
            if (!$currentParent) {
                return redirect()
                    ->route("admin.{$this->belongRoute}.create")
                    ->with('info', __('a.create_parent_element'));
            }


            // Записать куку навсегда (5 лет)
            return redirect()->back()
                ->withCookie(cookie()->forever("{$this->table}_id", $currentParent->id)
                );
        }

        $parentValues = DB::table($this->belongTable)->pluck('title', 'id');
        $parentValues->prepend($this->belongTable, 0);

        $values = null;

        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'title',
            'slug',
            'status',
            'sort',
            'parent_id',
            'id',
        ];

        // Параметры Get запроса
        $get = request()->query();
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;

        // Если в родительской таблице нет элементов, то ничего нельзя добавить
        if ($currentParentId) {

            // Метод для поиска и сортировки запроса БД
            $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage, 'belong_id', $currentParentId);
        }

        // Передать поля для вывода, значение l - с переводом, t - дата
        $thead = [
            'title' => 'l',
            'slug' => null,
            'status' => 'l',
            'sort' => null,
            'parent_id' => null,
            'id' => null,
        ];

        $f = __FUNCTION__;
        $title = __("a.{$this->table}");
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'parentValues', 'values', 'queryArr', 'col', 'cell', 'currentParent', 'thead'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Получаем меню родителя из куки
        $currentParentId = request()->cookie("{$this->table}_id");
        if ($currentParentId) {
            $currentParent = DB::table($this->belongTable)->find($currentParentId);

        } else {

            // Записать в куку id из привязанной таблице, если не записано
            $currentParent = DB::table($this->belongTable)->first();

            // Записать куку навсегда (5 лет)
            return redirect()->back()
                ->withCookie(cookie()->forever("{$this->table}_id", $currentParent->id)
                );
        }

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'currentParent'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'belong_id' => 'required|integer',
            'title' => 'required|string|max:100',
        ];
        $request->validate($rules);
        $data = $request->all();

        // Создаём экземкляр модели
        $values = new Menu();

        // Заполняем модель новыми данными
        $values->fill($data);

        // Сохраняем элемент
        $values->save();

        // Удалить все кэши
        cache()->flush();

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.edit", $values->id)
            ->with('success', __('s.created_successfully', ['id' => $values->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id)
    {
        //
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Получаем меню родителя из куки
        $currentParentId = request()->cookie("{$this->table}_id");
        if ($currentParentId) {
            $currentParent = DB::table($this->belongTable)->find($currentParentId);

        } else {

            // Записать в куку id из привязанной таблице, если не записано
            $currentParent = DB::table($this->belongTable)->first();

            // Записать куку навсегда (5 лет)
            return redirect()->back()
                ->withCookie(cookie()->forever("{$this->table}_id", $currentParent->id)
                );
        }


        // Получаем элемент по id, если нет - будет ошибка
        $values = $this->model::findOrFail($id);


        // Записать в реестр parent_id, для построения дерева
        Main::set('parent_id', $values->parent_id);


        // Элементы связанные
        $valuesBelong = $values->{$this->table};

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'values', 'valuesBelong', 'currentParent'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Получаем элемент по id, если нет - будет ошибка
        $values = $this->model::findOrFail($id);

        $rules = [
            'belong_id' => 'required|integer',
            'parent_id' => 'required|integer|min:0',
            'title' => 'required|string|max:100',
        ];
        $request->validate($rules);
        $data = $request->all();

        // parent_id не должны быть равно id
        if ($values->parent_id == $values->id) {
            $values->parent_id = '0';
        }

        // Заполняем модель новыми данными
        $values->fill($data);

        // Обновляем элемент
        $values->update();

        // Удалить все кэши
        cache()->flush();

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.edit", $values->id)
            ->with('success', __('s.saved_successfully', ['id' => $values->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Получаем элемент по id, если нет - будет ошибка
        $values = $this->model::findOrFail($id);

        // Если есть потомки, то ошибка
        if ($values->parents && $values->parents->isNotEmpty()) {
            return redirect()
                ->route("admin.{$this->route}.edit", $id)
                ->with('error', __('s.remove_not_possible') . ', ' . __('s.there_are_nested') . __('a.id'));
        }

        // Удаляем элемент
        $values->delete();

        // Удалить все кэши
        cache()->flush();

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('success', __('s.removed_successfully', ['id' => $values->id]));
    }
}
