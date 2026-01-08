<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BlogRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Models\Blog;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Str;

/**
 * Class BlogCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BlogCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitBlogStore;
    }
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
        CRUD::setModel(Blog::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/blog');
        CRUD::setEntityNameStrings('blog', 'blogs');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('title')->label('Blog Title');
        CRUD::column('image')->type('image');
        CRUD::column('short_desc')->label('Short Description');
        CRUD::column('content')->label('Long Description');
        CRUD::column('status')->label('Status')->type('checkbox');
    }

    protected function setupShowOperation()
    {
        CRUD::column('title')->label('Blog Title');
        CRUD::column('image')->type('image');
        CRUD::column('short_desc')->label('Short Description');
        CRUD::column('content')->label('Long Description');
        CRUD::column('status')->label('Status')->type('checkbox');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(BlogRequest::class);

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
                'path' => 'uploads/blog_images',
            ],
        ])->size(6);

        CRUD::field('short_desc')->label('Short Description')->type('textarea')->size(6);
        CRUD::field('content')->label('Long Description')->type('textarea')->size(6);
        CRUD::field('tags')->label('Tags')->type('text')->hint('Please separate the different tags by `,`')->size(6);
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

    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation();

        $request = $this->crud->getRequest();
        
        // 2. Generate base slug
        $baseSlug = Str::slug($request->title);

        // 3. Ensure UNIQUE slug
        $slug = $baseSlug;
        $count = 1;

        while (Blog::where('slug', $slug)->exists()) {
            $slug = $baseSlug;
            $count++;
        }

        // 4. Merge slug into request
        $request->merge(['slug' => $slug]);
        // 5. Store
        $this->crud->setRequest($request);
        
        $result = $this->traitBlogStore();

        return redirect(backpack_url('blog'));
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(BlogUpdateRequest::class);

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
                'path' => 'uploads/blog_images',
            ],
        ])->size(6);

        CRUD::field('short_desc')->label('Short Description')->type('textarea')->size(6);
        CRUD::field('content')->label('Long Description')->type('textarea')->size(6);
        CRUD::field('tags')->label('Tags')->type('text')->hint('Please separate the different tags by `,`')->size(6);
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
}
