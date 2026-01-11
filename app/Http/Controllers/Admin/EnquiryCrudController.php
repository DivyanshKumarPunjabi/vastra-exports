<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EnquiryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EnquiryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EnquiryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Enquiry::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/enquiry');
        CRUD::setEntityNameStrings('enquiry', 'enquiries');

        CRUD::addButtonFromView('top', 'date_filter', 'enquiry_date_filter', 'beginning');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        if (request()->filled('from') && request()->filled('to')) {
            CRUD::addClause('whereBetween', 'created_at', [
                request('from') . ' 00:00:00',
                request('to') . ' 23:59:59'
            ]);
        }
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
        CRUD::setValidation(EnquiryRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::field('first_name')->label('First Name')->type('text')->attributes([
            'oninput' => "this.value = this.value.replace(/[^a-zA-Z\s]/g, '');",
            'title' => 'Please enter only alphabets and spaces',
            'autocomplete' => 'off',
        ])->size(4);

        CRUD::field('last_name')->label('Last Name')->type('text')->attributes([
            'oninput' => "this.value = this.value.replace(/[^a-zA-Z\s]/g, '');",
            'title' => 'Please enter only alphabets and spaces',
            'autocomplete' => 'off',
        ])->size(4);

        CRUD::field('email')->label('Email')->type('email')->size(4);
        CRUD::field('mobile')->type('text')->label('Mobile Number')
            ->attributes([
                'pattern' => '[0-9]{10}',
                'maxlength' => 10,
                'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);",
                'title' => 'Please enter exactly 10 digits'
            ])->size(4);
        CRUD::field('message')->label('Enquiry Message')->type('textarea')->size(4);

        $this->crud->replaceSaveActions([
            'name' => 'save_action_one',
            'redirect' => function ($crud, $request, $itemId) {
                return backpack_url('enquiry');
            },
            // OPTIONAL:
            'button_text' => 'Save and Back',
            'visible' => function ($crud) {
                return true;
            },
            'order' => 1
        ]);
    }
}
