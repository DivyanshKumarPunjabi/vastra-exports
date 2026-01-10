<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProductCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitProductStore;
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
        CRUD::setModel(Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('product', 'products');
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
        CRUD::column('title')
            ->type('text')
            ->label('Product Title');

        CRUD::column('category_id')
            ->label('Product Category')
            ->type('text')
            ->value(function ($entry) {

                if (!$entry->category_id) {
                    return '-';
                }

                $sp = ProductCategory::withoutGlobalScopes()
                    ->find($entry->category_id);

                return $sp ? $sp->name  : '-';
            });

        CRUD::column('description')
            ->type('textarea')
            ->label('Description')
            ->limit(50);

        CRUD::column('fabric')
            ->type('text')
            ->label('Fabric');

        CRUD::column('style_code')
            ->type('text')
            ->label('Style Code');

        CRUD::column([
            'name' => 'image',
            'type' => 'image',
        ]);

        CRUD::column([
            'name' => 'image_1',
            'type' => 'image',
        ]);

        CRUD::column([
            'name' => 'image_2',
            'type' => 'image',
        ]);

        CRUD::column([
            'name' => 'image_3',
            'type' => 'image',
        ]);

        CRUD::column([
            'name' => 'image_4',
            'type' => 'image',
        ]);

        // CRUD::column([
        //     'name' => 'stock_status', 
        //     'type' => 'text',
        // ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductRequest::class);

        $productCategories = ProductCategory::pluck('name', 'id')->toArray();
        CRUD::field('title')
            ->type('text')
            ->label('Product Title')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('category_id')
            ->type('select_from_array')
            ->options($productCategories)
            ->label('Product Categories')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('description')
            ->type('textarea')
            ->label('Description')
            ->wrapper(['class' => 'form-group col-md-4']);

        // CRUD::field('gsm')
        //     ->type('number')
        //     ->label('GSM')
        //     ->attributes(['min' => 0])
        //     ->wrapper(['class' => 'form-group col-md-6']);

        // CRUD::field('moq')
        //     ->type('number')
        //     ->label('MOQ')
        //     ->attributes(['min' => 1])
        //     ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('fabric')
            ->type('text')
            ->label('Fabric')
            ->wrapper(['class' => 'form-group col-md-4'])
            ->attributes([
                'oninput' => "this.value = this.value.replace(/[^a-zA-Z0-9\\s\\(\\)\\[\\]&-%]/g, '');",
                'title' => 'Only alphabets, spaces, (), and [] are allowed',
                'autocomplete' => 'off'
            ]);

        CRUD::field('style_code')
            ->type('text')
            ->label('Style Code')
            ->wrapper(['class' => 'form-group col-md-4'])
            ->attributes([
                'oninput' => "this.value = this.value.replace(/[^a-zA-Z\\s\\(\\)\\[\\]&-%]/g, '');",
                'title' => 'Only alphabets, spaces, (), and [] are allowed',
                'autocomplete' => 'off'
            ]);

        CRUD::field([
            'name' => 'image',
            'type' => 'upload',
            'withFiles' => [
                'disk' => 'public_path',
                'path' => 'uploads/product_images',
            ],
        ])->size(4);

        CRUD::field([
            'name' => 'image_1',
            'type' => 'upload',
            'withFiles' => [
                'disk' => 'public_path',
                'path' => 'uploads/product_images',
            ],
        ])->size(4);

        CRUD::field([
            'name' => 'image_2',
            'type' => 'upload',
            'withFiles' => [
                'disk' => 'public_path',
                'path' => 'uploads/product_images',
            ],
        ])->size(4);

        CRUD::field([
            'name' => 'image_3',
            'type' => 'upload',
            'withFiles' => [
                'disk' => 'public_path',
                'path' => 'uploads/product_images',
            ],
        ])->size(4);

        CRUD::field([
            'name' => 'image_4',
            'type' => 'upload',
            'withFiles' => [
                'disk' => 'public_path',
                'path' => 'uploads/product_images',
            ],
        ])->size(4);

        // CRUD::field('stock_status')
        //     ->type('select_from_array')
        //     ->label('Stock Status')
        //     ->options([
        //         'in_stock' => 'In Stock',
        //         'out_of_stock' => 'Out of Stock',
        //     ])
        //     ->default('-')
        //     ->wrapper(['class' => 'form-group col-md-4']);
    }

    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation();

        $result = $this->traitProductStore();

        $id = $this->crud->entry->id;
        $lft = $id + 1;
        $rgt = $id + 1;

        Product::find($id)->update([
            'lft' => $lft,
            'rgt' => $rgt,
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
        CRUD::setValidation(ProductUpdateRequest::class);

        $productCategories = ProductCategory::pluck('name', 'id')->toArray();
        CRUD::field('title')
            ->type('text')
            ->label('Product Title')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('category_id')
            ->type('select_from_array')
            ->options($productCategories)
            ->label('Product Categories')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('description')
            ->type('textarea')
            ->label('Description')
            ->wrapper(['class' => 'form-group col-md-4']);

        // CRUD::field('gsm')
        //     ->type('number')
        //     ->label('GSM')
        //     ->attributes(['min' => 0])
        //     ->wrapper(['class' => 'form-group col-md-6']);

        // CRUD::field('moq')
        //     ->type('number')
        //     ->label('MOQ')
        //     ->attributes(['min' => 1])
        //     ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('fabric')
            ->type('text')
            ->label('Fabric')
            ->wrapper(['class' => 'form-group col-md-4'])
            ->attributes([
                'oninput' => "this.value = this.value.replace(/[^a-zA-Z0-9\\s\\(\\)\\[\\]&-%]/g, '');",
                'title' => 'Only alphabets, spaces, (), and [] are allowed',
                'autocomplete' => 'off'
            ]);

        CRUD::field('style_code')
            ->type('text')
            ->label('Style Code')
            ->wrapper(['class' => 'form-group col-md-4'])
            ->attributes([
                'oninput' => "this.value = this.value.replace(/[^a-zA-Z0-9\\s\\(\\)\\[\\]&-]/g, '');",
                'title' => 'Only alphabets, spaces, (), and [] are allowed',
                'autocomplete' => 'off'
            ]);

        // CRUD::field('stock_status')
        //     ->type('select_from_array')
        //     ->label('Stock Status')
        //     ->options([
        //         'in_stock' => 'In Stock',
        //         'out_of_stock' => 'Out of Stock',
        //     ])
        //     ->default('in_stock')
        //     ->wrapper(['class' => 'form-group col-md-4']);
    }

    protected function setupReorderOperation()
    {
        CRUD::set('reorder.label', 'title');
        CRUD::set('reorder.max_level', 5);
    }
}
