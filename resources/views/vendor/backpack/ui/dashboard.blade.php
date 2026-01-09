@extends(backpack_view('blank'))

@php
use Backpack\CRUD\app\Library\Widget;
use App\Models\Blog;
use App\Models\Enquiry;
use App\Models\Banner;
use App\Models\Product;
use App\Models\ProductCategory;



// notice we use Widget::add() to add widgets to a certain group
$blogCount = Blog::count();
$enquiryCount = Enquiry::count();
$bannerCount = Banner::count();
$productCount = Product::count();
$productCategoryCount = ProductCategory::count();

Widget::add()->to('before_content')->type('div')->class('row mt-3')->content([

Widget::make()
->type('progress')
->class('card mb-3 bg-primary text-white')
->statusBorder('start')
->ribbon(['top', 'la-blog'])
->value($blogCount)
->description('Blogs')
->wrapper([
'element' => 'a',
'href' => backpack_url('blog'),
'class' => 'col-sm-6 col-lg-3 text-decoration-none',
]),

Widget::make()
->type('progress')
->class('card mb-3 bg-success text-white')
->statusBorder('start')
->ribbon(['top', 'la-envelope'])
->value($enquiryCount)
->description('Enquiries')
->wrapper([
'element' => 'a',
'href' => backpack_url('enquiry'),
'class' => 'col-sm-6 col-lg-3 text-decoration-none',
]),

Widget::make()
->type('progress')
->class('card mb-3 bg-warning text-dark')
->statusBorder('start')
->ribbon(['top', 'la-image'])
->value($bannerCount)
->description('Banners')
->wrapper([
'element' => 'a',
'href' => backpack_url('banner'),
'class' => 'col-sm-6 col-lg-3 text-decoration-none',
]),

Widget::make()
->type('progress')
->class('card mb-3 bg-info text-white')
->statusBorder('start')
->ribbon(['top', 'la-cubes'])
->value($productCount)
->description('Products')
->wrapper([
'element' => 'a',
'href' => backpack_url('product'),
'class' => 'col-sm-6 col-lg-3 text-decoration-none',
]),

Widget::make()
->type('progress')
->class('card mb-3 bg-danger text-white')
->statusBorder('start')
->ribbon(['top', 'la-tags'])
->value($productCategoryCount)
->description('Product Categories')
->wrapper([
'element' => 'a',
'href' => backpack_url('product-category'),
'class' => 'col-sm-6 col-lg-3 text-decoration-none',
]),

]);

@endphp



@section('content')
@endsection