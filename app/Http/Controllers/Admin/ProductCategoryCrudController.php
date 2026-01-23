<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductCategoryRequest;
use App\Http\Requests\ProductCategoryUpdateRequest;
use App\Models\ProductCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProductCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductCategoryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitProductCategoryStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(ProductCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product-category');
        CRUD::setEntityNameStrings('product category', 'product categories');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addButtonFromModelFunction('line', 'admin_approval', 'showCategoryProducts', 'first');
        $this->crud->addClause('orderBy', 'lft', 'asc');
        CRUD::column('name')->label('Category Name');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductCategoryRequest::class);
        CRUD::field('name')->label('Category Name')->type('text')->attributes([
            'oninput' => "this.value = this.value.replace(/[^a-zA-Z\\s\\(\\)\\[\\]&/-]/g, '');",
            'title' => 'Only alphabets, spaces, (), and [] are allowed',
            'autocomplete' => 'off',
        ])->size(6);

        $this->crud->replaceSaveActions([
            'name' => 'save_action_one',
            'redirect' => function ($crud, $request, $itemId) {
                return backpack_url('product-category');
            },
            // OPTIONAL:
            'button_text' => 'Save and Back',
            'visible' => function ($crud) {
                return true;
            },
            'order' => 1
        ]);
    }

    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation();

        $result = $this->traitProductCategoryStore();

        $id = $this->crud->entry->id;
        $lft = $id + 1;
        $rgt = $id + 1;

        ProductCategory::find($id)->update([
            '_lft' => $lft,
            '_rgt' => $rgt,
            'depth' => 1,
        ]);

        return $result;
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(ProductCategoryUpdateRequest::class);
        CRUD::field('name')->label('Category Name')->type('text')->attributes([
            'oninput' => "this.value = this.value.replace(/[^a-zA-Z\\s\\(\\)\\[\\]&/-]/g, '');",
            'title' => 'Only alphabets, spaces, (), and [] are allowed',
            'autocomplete' => 'off',
        ])->size(6);

        $this->crud->replaceSaveActions([
            'name' => 'save_action_one',
            'redirect' => function ($crud, $request, $itemId) {
                return backpack_url('product-category');
            },
            // OPTIONAL:
            'button_text' => 'Save and Back',
            'visible' => function ($crud) {
                return true;
            },
            'order' => 1
        ]);
    }

    protected function setupReorderOperation()
    {
        CRUD::set('reorder.label', 'name');
        CRUD::set('reorder.max_level', 5);
    }
}
