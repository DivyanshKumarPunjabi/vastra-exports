<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReviewRequest;
use App\Models\GoogleReview;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ReviewCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReviewCrudController extends CrudController
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
        CRUD::setModel(GoogleReview::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/review');
        CRUD::setEntityNameStrings('review', 'reviews');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->disableResponsiveTable();
        CRUD::column('author_name')
            ->type('text')
            ->label('Name')->limit(1000);

        CRUD::addColumn([
            'name'  => 'rating',
            'label' => 'Rating',
            'type'  => 'view',
            'view'  => 'vendor.backpack.crud.columns.rating_stars',
        ]);

        CRUD::column('review_text')
            ->type('text')
            ->label('Review Content')->limit(50);

        CRUD::column('review_time')
            ->type('text')
            ->label('Review Date & Time');
    }

    protected function setupShowOperation()
    {
        $this->crud->disableResponsiveTable();
        CRUD::column('author_name')
            ->type('text')
            ->label('Name')->limit(1000);

        CRUD::addColumn([
            'name'  => 'rating',
            'label' => 'Rating',
            'type'  => 'view',
            'view'  => 'vendor.backpack.crud.columns.rating_stars',
        ]);

        CRUD::column('review_text')
            ->type('text')
            ->label('Review Content');

        CRUD::column('review_time')
            ->type('text')
            ->label('Review Date & Time');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ReviewRequest::class);
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
        $this->setupCreateOperation();
    }
}
