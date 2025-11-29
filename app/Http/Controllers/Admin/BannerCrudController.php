<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BannerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BannerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BannerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Banner::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/banner');
        CRUD::setEntityNameStrings('banner', 'banners');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(BannerRequest::class);
        
        CRUD::field('title')->label('Blog Title')->type('text')->attributes([
            'oninput' => "this.value = this.value.replace(/[^a-zA-Z\s]/g, '');",
            'title' => 'Please enter only alphabets and spaces',
            'autocomplete' => 'off',
        ])->size(6);

        CRUD::field([
            'name' => 'image',
            'type' => 'upload',
            'withFiles' => [
                'disk' => 'public_path',
                'path' => 'uploads/banner_images',
            ],
        ])->size(6);

        CRUD::field('short_descp')->label('Short Description')->type('textarea')->size(6);
        CRUD::field('status')->label('Status')->type('checkbox')->size(6);

        $this->crud->replaceSaveActions([
            'name' => 'save_action_one',
            'redirect' => function ($crud, $request, $itemId) {
                return backpack_url('blog');
            },
            // OPTIONAL:
            'button_text' => 'Save and Back',
            'visible' => function ($crud) {
                return true;
            },
            'order' => 1
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
